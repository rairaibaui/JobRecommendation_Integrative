<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::statement('ALTER TABLE application_history RENAME TO application_history_old');

        DB::statement(<<<'SQL'
CREATE TABLE "application_history" (
  "id" integer primary key autoincrement not null,
  "application_id" integer not null,
  "employer_id" integer not null,
  "job_seeker_id" integer not null,
  "job_posting_id" integer,
  "job_title" varchar not null,
  "company_name" varchar,
  "decision" varchar check ("decision" in ('hired', 'rejected', 'terminated', 'resigned')) not null,
  "rejection_reason" text,
  "applicant_snapshot" text,
  "job_snapshot" text,
  "decision_date" datetime not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("application_id") references "applications"("id") on delete cascade,
  foreign key("employer_id") references "users"("id") on delete cascade,
  foreign key("job_seeker_id") references "users"("id") on delete cascade,
  foreign key("job_posting_id") references "job_postings"("id") on delete set null
)
SQL
        );

        DB::statement(<<<'SQL'
INSERT INTO application_history (id, application_id, employer_id, job_seeker_id, job_posting_id, job_title, company_name, decision, rejection_reason, applicant_snapshot, job_snapshot, decision_date, created_at, updated_at)
SELECT id, application_id, employer_id, job_seeker_id, job_posting_id, job_title, company_name, decision, rejection_reason, applicant_snapshot, job_snapshot, decision_date, created_at, updated_at
FROM application_history_old;
SQL
        );

        DB::statement('DROP TABLE application_history_old');
        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Recreate original application_history pointing to applications_old (if you need to rollback)
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::statement('ALTER TABLE application_history RENAME TO application_history_new');

        DB::statement(<<<'SQL'
CREATE TABLE "application_history" (
  "id" integer primary key autoincrement not null,
  "application_id" integer not null,
  "employer_id" integer not null,
  "job_seeker_id" integer not null,
  "job_posting_id" integer,
  "job_title" varchar not null,
  "company_name" varchar,
  "decision" varchar check ("decision" in ('hired', 'rejected', 'terminated', 'resigned')) not null,
  "rejection_reason" text,
  "applicant_snapshot" text,
  "job_snapshot" text,
  "decision_date" datetime not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("application_id") references "applications_old"("id") on delete cascade,
  foreign key("employer_id") references "users"("id") on delete cascade,
  foreign key("job_seeker_id") references "users"("id") on delete cascade,
  foreign key("job_posting_id") references "job_postings"("id") on delete set null
)
SQL
        );

        DB::statement(<<<'SQL'
INSERT INTO application_history (id, application_id, employer_id, job_seeker_id, job_posting_id, job_title, company_name, decision, rejection_reason, applicant_snapshot, job_snapshot, decision_date, created_at, updated_at)
SELECT id, application_id, employer_id, job_seeker_id, job_posting_id, job_title, company_name, decision, rejection_reason, applicant_snapshot, job_snapshot, decision_date, created_at, updated_at
FROM application_history_new;
SQL
        );

        DB::statement('DROP TABLE application_history_new');
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
