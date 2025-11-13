<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Normalize existing users.phone_number to 11-digit local format (0917...)
        $users = DB::table('users')->select('id', 'phone_number')->get();

        foreach ($users as $u) {
            $raw = $u->phone_number;
            if (is_null($raw) || $raw === '') continue;
            // normalize in-line: remove non-digits
            $s = preg_replace('/[^0-9]/', '', $raw);
            if ($s === '' ) continue;
            if (strpos($s, '63') === 0 && strlen($s) >= 11) {
                $s = '0' . substr($s, 2);
            }
            if (strlen($s) === 10 && strpos($s, '9') === 0) {
                $s = '0' . $s;
            }
            if (strlen($s) > 11) {
                $s = substr($s, -11);
            }
            // Only update if result looks valid and different
            if ($s && $s !== $raw) {
                // Avoid overwriting if another user already has this normalized number
                $exists = DB::table('users')->where('phone_number', $s)->where('id', '<>', $u->id)->exists();
                if (! $exists) {
                    DB::table('users')->where('id', $u->id)->update(['phone_number' => $s]);
                }
            }
        }
    }

    public function down()
    {
        // Nothing to rollback safely
    }
};
