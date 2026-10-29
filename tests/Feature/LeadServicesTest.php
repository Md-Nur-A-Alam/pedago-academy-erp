<?php

use App\Models\Course;
use App\Models\DuplicateLeadReview;
use App\Models\Lead;
use App\Models\LeadBatch;
use App\Models\LeadBatchItem;
use App\Models\LeadSource;
use App\Models\User;
use App\Modules\Lead\Services\DuplicateDetectionService;
use App\Modules\Lead\Services\EnrollmentService;
use App\Modules\Lead\Services\LeadDistributionEngine;
use App\Modules\Lead\Services\LeadTransferService;
use App\Modules\Lead\Services\PaymentVerificationService;

beforeEach(function () {
    // Lock test time to 14:00 (2:00 PM) well inside shift hours (10:00 - 18:00)
    Carbon\Carbon::setTestNow(Carbon\Carbon::parse('2026-10-29 14:00:00', 'Asia/Dhaka'));
    Artisan::call('migrate:fresh');
    Artisan::call('db:seed');
});

afterEach(function () {
    Carbon\Carbon::setTestNow();
});

test('lead distribution engine assigns standard batch of n=3 when supply is plentiful', function () {
    $engine = new LeadDistributionEngine;

    $course = Course::first();
    $source = LeadSource::first();

    // Create 15 unassigned leads (5 available agents * 3 = 15)
    for ($i = 1; $i <= 15; $i++) {
        Lead::create([
            'source_id' => $source->source_id,
            'student_name' => "Student {$i}",
            'contact_number' => "018111100{$i}",
            'interested_course_id' => $course->id,
            'status' => 'New',
        ]);
    }

    $result = $engine->run();

    expect($result['status'])->toBe('success')
        ->and($result['leads_distributed'])->toBe(15)
        ->and($result['executives_assigned'])->toBe(5);

    // Each active batch should have 3 items
    $batches = LeadBatch::where('status', 'Active')->get();
    expect($batches)->toHaveCount(5);

    foreach ($batches as $batch) {
        expect($batch->items)->toHaveCount(3);
    }
});

test('lead distribution engine degrades to n=2 plus round robin on moderate scarcity', function () {
    $engine = new LeadDistributionEngine;

    // 5 agents, 12 leads (Threshold moderate: 2*5 = 10 <= 12 < 15)
    $plan = $engine->calculateAllocationPlan(12, 5);

    // Plan should assign 3 leads to first 2 agents, 2 leads to remaining 3 agents
    expect($plan)->toBe([3, 3, 2, 2, 2]);
});

test('duplicate detection service identifies exact matches and routes sibling inquiries to review queue', function () {
    $duplicateService = new DuplicateDetectionService;
    $course = Course::first();
    $source = LeadSource::first();

    // Create existing lead
    $existingLead = Lead::create([
        'source_id' => $source->source_id,
        'student_name' => 'Farhan Kabir',
        'contact_number' => '01799998888',
        'interested_course_id' => $course->id,
        'status' => 'New',
    ]);

    // 1. Same phone + same name + same course => DEFINITE_DUPLICATE
    $exactMatchResult = $duplicateService->evaluate([
        'source_id' => $source->source_id,
        'student_name' => 'Farhan Kabir',
        'contact_number' => '01799998888',
        'interested_course_id' => $course->id,
    ]);
    expect($exactMatchResult['decision'])->toBe('DEFINITE_DUPLICATE');

    // 2. Same phone + different student name => NEEDS_REVIEW
    $siblingResult = $duplicateService->evaluate([
        'source_id' => $source->source_id,
        'student_name' => 'Zayan Kabir (Brother)',
        'contact_number' => '01799998888',
        'interested_course_id' => $course->id,
    ]);
    expect($siblingResult['decision'])->toBe('NEEDS_REVIEW')
        ->and(DuplicateLeadReview::count())->toBe(1);

    // 3. New unique phone => VALID_NEW
    $validResult = $duplicateService->evaluate([
        'source_id' => $source->source_id,
        'student_name' => 'New Student',
        'contact_number' => '01500000000',
        'interested_course_id' => $course->id,
    ]);
    expect($validResult['decision'])->toBe('VALID_NEW');
});

test('lead transfer service supports instant admin bypass and dual team leader approvals', function () {
    $transferService = new LeadTransferService;
    $admin = User::where('role', 'Admin')->first();
    $sender = User::where('email', 'arafat@pedago.com')->first();
    $receiver = User::where('email', 'farhana@pedago.com')->first();
    $source = LeadSource::first();

    $lead = Lead::create([
        'source_id' => $source->source_id,
        'student_name' => 'Transfer Test Student',
        'contact_number' => '01655554444',
        'current_owner_id' => $sender->id,
        'status' => 'Assigned',
    ]);

    // Admin direct transfer => Immediate Approved without TL approvals
    $adminTransfer = $transferService->requestTransfer($lead, $sender, $receiver, 'Admin operational reallocation', $admin);
    expect($adminTransfer->status)->toBe('Approved')
        ->and($adminTransfer->is_admin_override)->toBeTrue()
        ->and($lead->fresh()->current_owner_id)->toBe($receiver->id);

    // Normal agent request => Pending without freezing lead
    $normalTransfer = $transferService->requestTransfer($lead, $receiver, $sender, 'Lead requested original counselor', $receiver);
    expect($normalTransfer->status)->toBe('Pending')
        ->and($normalTransfer->is_admin_override)->toBeFalse()
        ->and($lead->fresh()->current_owner_id)->toBe($receiver->id); // Not changed yet!
});

test('enrollment and payment verification workflow completes sales batch task', function () {
    $enrollmentService = new EnrollmentService;
    $paymentService = new PaymentVerificationService;

    $salesRep = User::where('email', 'arafat@pedago.com')->first();
    $accountsUser = User::where('role', 'Accounts')->first();
    $course = Course::first();
    $source = LeadSource::first();

    // Create lead assigned to sales rep in an active batch
    $lead = Lead::create([
        'source_id' => $source->source_id,
        'student_name' => 'Admitted Student',
        'contact_number' => '01311112222',
        'interested_course_id' => $course->id,
        'current_owner_id' => $salesRep->id,
        'status' => 'Assigned',
    ]);

    $batch = LeadBatch::create([
        'user_id' => $salesRep->id,
        'batch_size' => 1,
        'status' => 'Active',
    ]);

    $batchItem = LeadBatchItem::create([
        'batch_id' => $batch->id,
        'lead_id' => $lead->id,
        'is_completed' => false,
    ]);

    // 1. Sales rep creates enrollment form with 2 installments
    $enrollmentData = [
        'base_course_fee' => 10000.00,
        'discount_percentage' => 10.00, // Deal amount = 9,000
        'total_installments' => 2,
        'installments' => [
            ['due_amount' => 5000.00, 'due_date' => now()->toDateString()],
            ['due_amount' => 4000.00, 'due_date' => now()->addDays(30)->toDateString()],
        ],
    ];

    $enrollmentResult = $enrollmentService->createEnrollment($lead, $salesRep, $enrollmentData);
    $enrollment = $enrollmentResult['enrollment'];

    expect($lead->fresh()->status)->toBe('Enrolled - Pending Payment')
        ->and($enrollment->final_deal_amount)->toEqual(9000.00)
        ->and($enrollmentResult['verification_url'])->toContain('/enroll/verify/');

    // 2. Customer submits 1st installment payment via bKash
    $payment = $enrollmentService->submitPayment($enrollment, [
        'amount' => 5000.00,
        'payment_method' => 'bKash',
        'transaction_id' => 'BKASH-TRX-998877',
    ]);

    expect($payment->status)->toBe('Pending')
        ->and($lead->fresh()->status)->toBe('Payment Under Review');

    // 3. Accounts approves the payment
    $approvalResult = $paymentService->approvePayment($payment, $accountsUser);

    expect($approvalResult['status'])->toBe('success')
        ->and($approvalResult['receipt_number'])->toContain('MR-')
        ->and($batchItem->fresh()->is_completed)->toBeTrue()
        ->and($batch->fresh()->status)->toBe('Completed')
        ->and($lead->fresh()->status)->toBe('Converted');
});
