<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class MakePhoneNumberUnique extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration will:
     *  - Normalize existing phone numbers to the canonical 11-digit format (0917...)
     *  - Detect any collisions after normalization and abort with a helpful message if found
     *  - Add a unique index on `phone_number` if no collisions are present
     */
    public function up()
    {
        // Normalize stored phone numbers using the same rules as application code
        $normalize = function ($raw) {
            if (empty($raw)) return null;
            $s = preg_replace('/[^0-9]/', '', $raw);
            if ($s === '') return null;
            if (strpos($s, '63') === 0 && strlen($s) >= 11) {
                $s = '0' . substr($s, 2);
            }
            if (strlen($s) === 10 && strpos($s, '9') === 0) {
                $s = '0' . $s;
            }
            if (strlen($s) > 11) {
                $s = substr($s, -11);
            }
            return $s;
        };

        // Normalize numbers in a transaction so partial work can be rolled back
        DB::beginTransaction();
        try {
            $users = DB::table('users')->select('id', 'phone_number')->get();
            foreach ($users as $u) {
                $raw = $u->phone_number;
                $normalized = $normalize($raw);
                if ($normalized !== null && $normalized !== $raw) {
                    DB::table('users')->where('id', $u->id)->update(['phone_number' => $normalized]);
                }
            }

            // Check for duplicates after normalization
            $duplicates = DB::table('users')
                ->select('phone_number', DB::raw('count(*) as cnt'))
                ->whereNotNull('phone_number')
                ->groupBy('phone_number')
                ->having('cnt', '>', 1)
                ->get();

            if ($duplicates->count() > 0) {
                DB::rollBack();
                $msg = "Phone number collisions detected after normalization. Migration aborted.\n";
                foreach ($duplicates as $d) {
                    $msg .= "Normalized phone {$d->phone_number} appears {$d->cnt} times.\n";
                }
                // Throwing an exception will abort the migration and surface the message
                throw new \Exception($msg);
            }

            // Add unique index (if not already present)
            if (!Schema::hasColumn('users', 'phone_number')) {
                DB::rollBack();
                throw new \Exception('users.phone_number column does not exist; aborting migration.');
            }

            // Use Doctrine schema manager if available to check existing indexes
            $connection = Schema::getConnection();
            $indexExists = false;
            try {
                $sm = $connection->getDoctrineSchemaManager();
                $indexes = $sm->listTableIndexes('users');
                $indexExists = array_key_exists('users_phone_number_unique', $indexes);
            } catch (\Throwable $e) {
                // If Doctrine not available or check fails, fall back to attempting to create and ignore errors
                $indexExists = false;
            }

            if (! $indexExists) {
                try {
                    Schema::table('users', function (Blueprint $table) {
                        $table->unique('phone_number', 'users_phone_number_unique');
                    });
                } catch (\Throwable $e) {
                    // If index already exists (race or prior partial migration), ignore
                    $msg = $e->getMessage();
                    if (stripos($msg, 'already exists') === false && stripos($msg, 'duplicate') === false) {
                        throw $e; // rethrow unexpected errors
                    }
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            // Drop the index if exists
            $table->dropIndex('users_phone_number_unique');
        });
    }
}
