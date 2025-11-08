<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Jobs\AutoUnverifyResumeJob;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class AutoUnverifyResumeJobTest extends TestCase
{
    public function test_revokes_when_outdated_exceeded()
    {
        Config::set('verification.outdated_grace_minutes', 10);

        // Lightweight test double (no DB needed)
        $fakeUser = new class {
            public $id = 9999;
            public $resume_verification_status = 'verified';
            public $verification_score = 100;
            public $verified_at;
            public $resume_outdated_at;
            public $verification_notes;
            public $saved = false;
            public function save()
            {
                $this->saved = true;
            }
        };

    $fakeUser->verified_at = \Carbon\Carbon::now();
    $fakeUser->resume_outdated_at = \Carbon\Carbon::now()->subMinutes(11);

        $job = new AutoUnverifyResumeJob($fakeUser->id);
        $job->evaluateUser($fakeUser);

        $this->assertEquals('pending', $fakeUser->resume_verification_status);
        $this->assertNull($fakeUser->resume_outdated_at);
        $this->assertNull($fakeUser->verified_at);
        $this->assertEquals(0, $fakeUser->verification_score);
    }

    public function test_skips_when_within_grace_period()
    {
        Config::set('verification.outdated_grace_minutes', 10);

        $fakeUser = new class {
            public $id = 10000;
            public $resume_verification_status = 'verified';
            public $verification_score = 100;
            public $verified_at;
            public $resume_outdated_at;
            public $verification_notes;
            public $saved = false;
            public function save() { $this->saved = true; }
        };

    $fakeUser->verified_at = \Carbon\Carbon::now();
    $fakeUser->resume_outdated_at = \Carbon\Carbon::now()->subMinutes(5);

        $job = new AutoUnverifyResumeJob($fakeUser->id);
        $job->evaluateUser($fakeUser);

        // Still verified because grace period has not elapsed
        $this->assertEquals('verified', $fakeUser->resume_verification_status);
        $this->assertNotNull($fakeUser->resume_outdated_at);
        $this->assertNotNull($fakeUser->verified_at);
    }
}
