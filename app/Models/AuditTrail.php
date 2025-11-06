<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditTrail extends Model
{
    protected $fillable = [
        'validation_id',
        'admin_id',
        'admin_email',
        'action',
        'notes',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function validation(): BelongsTo
    {
        return $this->belongsTo(DocumentValidation::class, 'validation_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
