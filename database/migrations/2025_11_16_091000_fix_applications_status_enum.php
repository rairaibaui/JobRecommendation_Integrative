<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Recreate the applications table to update the CHECK constraint for status
        DB::statement('PRAGMA foreign_keys = OFF');

        DB::statement('ALTER TABLE applications RENAME TO applications_old');

        DB::statement(<<<'SQL'
CREATE TABLE "applications" (
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "job_title" varchar not null,
  "job_data" text,
  "resume_snapshot" text,
  "created_at" datetime,
  "updated_at" datetime,
  "job_posting_id" integer,
  "interview_date" datetime,
  "interview_notes" text,
  "interview_location" varchar,
  "status" varchar check ("status" in ('pending', 'reviewing', 'for_interview', 'interviewed', 'accepted', 'rejected')) not null default 'pending',
  "employer_id" integer,
  "company_name" varchar,
  "updated_by" integer,
  "status_updated_at" datetime,
  foreign key("job_posting_id") references job_postings("id") on delete cascade on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action
)
SQL
        );

        // Copy data across (map columns explicitly)
        DB::statement(<<<'SQL'
INSERT INTO applications (id, user_id, job_title, job_data, resume_snapshot, created_at, updated_at, job_posting_id, interview_date, interview_notes, interview_location, status, employer_id, company_name, updated_by, status_updated_at)
SELECT id, user_id, job_title, job_data, resume_snapshot, created_at, updated_at, job_posting_id, interview_date, interview_notes, interview_location, status, employer_id, company_name, updated_by, status_updated_at
FROM applications_old;
SQL
        );

        DB::statement('DROP TABLE applications_old');
        DB::statement('PRAGMA foreign_keys = ON');
    }

    public function down(): void
    {
        // Recreate previous schema without 'interviewed' in the CHECK
        DB::statement('PRAGMA foreign_keys = OFF');
        DB::statement('ALTER TABLE applications RENAME TO applications_new');

        DB::statement(<<<'SQL'
CREATE TABLE "applications" (
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "job_title" varchar not null,
  "job_data" text,
  "resume_snapshot" text,
  "created_at" datetime,
  "updated_at" datetime,
  "job_posting_id" integer,
  "interview_date" datetime,
  "interview_notes" text,
  "interview_location" varchar,
  "status" varchar check ("status" in ('pending', 'reviewing', 'for_interview', 'accepted', 'rejected')) not null default 'pending',
  "employer_id" integer,
  "company_name" varchar,
  "updated_by" integer,
  "status_updated_at" datetime,
  foreign key("job_posting_id") references job_postings("id") on delete cascade on update no action,
  foreign key("user_id") references users("id") on delete cascade on update no action
)
SQL
        );

        DB::statement(<<<'SQL'
INSERT INTO applications (id, user_id, job_title, job_data, resume_snapshot, created_at, updated_at, job_posting_id, interview_date, interview_notes, interview_location, status, employer_id, company_name, updated_by, status_updated_at)
SELECT id, user_id, job_title, job_data, resume_snapshot, created_at, updated_at, job_posting_id, interview_date, interview_notes, interview_location, status, employer_id, company_name, updated_by, status_updated_at
FROM applications_new;
SQL
        );

        DB::statement('DROP TABLE applications_new');
        DB::statement('PRAGMA foreign_keys = ON');
    }
};
