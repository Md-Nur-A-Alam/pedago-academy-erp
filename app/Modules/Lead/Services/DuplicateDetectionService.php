<?php

namespace App\Modules\Lead\Services;

use App\Models\DuplicateLeadReview;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;

class DuplicateDetectionService
{
    /**
     * Inspect an incoming lead payload against the smart duplicate matrix.
     *
     * @param  array<string, mixed>  $payload
     * @return array{
     *     decision: string,
     *     matched_lead: ?Lead,
     *     review_id: ?int,
     *     reason: ?string
     * }
     */
    public function evaluate(array $payload): array
    {
        $primaryPhone = trim($payload['contact_number'] ?? '');
        $altPhone = trim($payload['alternative_contact_number'] ?? '');
        $studentName = trim($payload['student_name'] ?? '');
        $courseId = $payload['interested_course_id'] ?? null;

        if (empty($primaryPhone)) {
            return [
                'decision' => 'INVALID_PAYLOAD',
                'matched_lead' => null,
                'review_id' => null,
                'reason' => 'Contact number is required.',
            ];
        }

        // Search for existing phone matches (primary or alternate)
        $matchedLead = Lead::query()
            ->where(function ($q) use ($primaryPhone, $altPhone) {
                $q->where('contact_number', $primaryPhone)
                    ->orWhere('alternative_contact_number', $primaryPhone);

                if (! empty($altPhone)) {
                    $q->orWhere('contact_number', $altPhone)
                        ->orWhere('alternative_contact_number', $altPhone);
                }
            })
            ->latest()
            ->first();

        // 1. No phone match found => Completely valid new lead
        if (! $matchedLead) {
            return [
                'decision' => 'VALID_NEW',
                'matched_lead' => null,
                'review_id' => null,
                'reason' => 'No duplicate contact match detected.',
            ];
        }

        // 2. Check for Definite Duplicate (Same Phone + Same Name + Same Course)
        $isSameName = strtolower(trim($matchedLead->student_name)) === strtolower($studentName);
        $isSameCourse = (int) $matchedLead->interested_course_id === (int) $courseId;

        if ($isSameName && $isSameCourse) {
            return [
                'decision' => 'DEFINITE_DUPLICATE',
                'matched_lead' => $matchedLead,
                'review_id' => null,
                'reason' => 'Identical inquiry found with identical phone, student name, and course.',
            ];
        }

        // 3. Cross-Number Match or Sibling / Multi-Course Inquiry => Route to Review Queue
        $reason = $isSameCourse
            ? 'Same phone number but different student name (potential sibling enrollment).'
            : 'Same contact number inquiring for a different course.';

        $review = DuplicateLeadReview::create([
            'incoming_lead_data' => $payload,
            'matched_lead_id' => $matchedLead->id,
            'match_reason' => $reason,
            'status' => 'Pending Review',
            'created_at' => now(),
        ]);

        return [
            'decision' => 'NEEDS_REVIEW',
            'matched_lead' => $matchedLead,
            'review_id' => $review->id,
            'reason' => $reason,
        ];
    }

    /**
     * Resolve a duplicate lead review record (Admin / Supervisor action).
     *
     * @param  string  $action  'Approved New' | 'Merged' | 'Discarded'
     */
    public function resolveReview(DuplicateLeadReview $review, string $action, ?int $reviewerId = null): ?Lead
    {
        $reviewerId = $reviewerId ?? Auth::id();
        $payload = $review->incoming_lead_data;

        $review->update([
            'status' => $action,
            'reviewed_by_user_id' => $reviewerId,
            'reviewed_at' => now(),
        ]);

        if ($action === 'Approved New') {
            // Insert as a new separate valid lead into central queue
            return Lead::create([
                'source_id' => $payload['source_id'],
                'student_name' => $payload['student_name'],
                'contact_number' => $payload['contact_number'],
                'alternative_contact_number' => $payload['alternative_contact_number'] ?? null,
                'email' => $payload['email'] ?? null,
                'age' => $payload['age'] ?? null,
                'class' => $payload['class'] ?? null,
                'interested_course_id' => $payload['interested_course_id'] ?? null,
                'status' => 'New',
                'current_owner_id' => null,
                'team_id' => null,
            ]);
        }

        return null;
    }
}
