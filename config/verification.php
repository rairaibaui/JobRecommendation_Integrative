<?php

return [
    // Number of days a user has to update their resume after a contact change
    // before the system automatically revokes resume verification.
    'outdated_grace_days' => env('VERIFICATION_OUTDATED_GRACE_DAYS', 7),

    // Number of minutes to keep the verified badge visible after a contact change
    // before revoking verification when the resume wasn't updated. This is used
    // by minute-level enforcement (e.g. 10 minutes). If your app uses the
    // queued delayed job (recommended), set this to 10. If you rely on the
    // scheduled fallback command, ensure it runs frequently enough (e.g. every
    // minute) for minute-level enforcement to be effective.
    'outdated_grace_minutes' => env('VERIFICATION_OUTDATED_GRACE_MINUTES', 10),
];
