<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'link',
        'read',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markAsRead()
    {
        $this->update([
            'read' => true,
            'read_at' => now(),
        ]);
    }

    protected static function booted()
    {
        static::created(function (Notification $notification) {
            // Mirror employer notifications as audit logs
            try {
                $user = $notification->user()->first();
                if ($user && $user->user_type === 'employer') {
                    \App\Models\AuditLog::create([
                        'user_id' => $notification->user_id,
                        'actor_id' => is_array($notification->data) && isset($notification->data['actor_id']) ? $notification->data['actor_id'] : null,
                        'event' => $notification->type ?? 'notification',
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'data' => $notification->data ?? [],
                        'ip_address' => request()->ip() ?? null,
                        'user_agent' => request()->userAgent() ?? null,
                    ]);
                }
            } catch (\Throwable $e) {
                // Fail-safe: never break notification creation if audit logging fails
            }
        });
    }
}
