<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            // 1. Prevent Delete on lead_feedbacks (MySQL)
            DB::unprepared("
                CREATE TRIGGER `prevent_feedback_delete`
                BEFORE DELETE ON `lead_feedbacks`
                FOR EACH ROW
                BEGIN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'CRITICAL ERROR: Lead Feedbacks are strictly immutable and cannot be deleted.';
                END;
            ");

            // 2. Prevent Update on lead_feedbacks (MySQL)
            DB::unprepared("
                CREATE TRIGGER `prevent_feedback_update`
                BEFORE UPDATE ON `lead_feedbacks`
                FOR EACH ROW
                BEGIN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'CRITICAL ERROR: Lead Feedbacks are strictly immutable and cannot be edited.';
                END;
            ");

            // 3. Auto-populate transferred_at when status becomes Approved (MySQL)
            DB::unprepared("
                CREATE TRIGGER `set_transfer_timestamp`
                BEFORE UPDATE ON `lead_transfers`
                FOR EACH ROW
                BEGIN
                    IF NEW.status = 'Approved' AND OLD.status != 'Approved' AND NEW.transferred_at IS NULL THEN
                        SET NEW.transferred_at = CURRENT_TIMESTAMP();
                    END IF;
                END;
            ");
        } elseif ($driver === 'sqlite') {
            // SQLite equivalents for Pest / In-Memory testing
            DB::unprepared("
                CREATE TRIGGER prevent_feedback_delete
                BEFORE DELETE ON lead_feedbacks
                BEGIN
                    SELECT RAISE(ABORT, 'CRITICAL ERROR: Lead Feedbacks are strictly immutable and cannot be deleted.');
                END;
            ");

            DB::unprepared("
                CREATE TRIGGER prevent_feedback_update
                BEFORE UPDATE ON lead_feedbacks
                BEGIN
                    SELECT RAISE(ABORT, 'CRITICAL ERROR: Lead Feedbacks are strictly immutable and cannot be edited.');
                END;
            ");

            DB::unprepared("
                CREATE TRIGGER set_transfer_timestamp
                AFTER UPDATE OF status ON lead_transfers
                FOR EACH ROW
                WHEN NEW.status = 'Approved' AND OLD.status != 'Approved' AND NEW.transferred_at IS NULL
                BEGIN
                    UPDATE lead_transfers SET transferred_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
                END;
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS `prevent_feedback_delete`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `prevent_feedback_update`;');
        DB::unprepared('DROP TRIGGER IF EXISTS `set_transfer_timestamp`;');
    }
};
