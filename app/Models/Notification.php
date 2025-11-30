<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * نموذج التنبيهات - يمثل الرسالئ والتنبيهات للمستخدمين
 * Notification Model - Represents system notifications and messages for users
 *
 * @property int $id
 * @property int $user_id
 * @property string $type
 * @property string $title
 * @property string $body
 * @property string $status
 * @property array $metadata
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    /**
     * احصل على المستخدم الذي استلم التنبيه
     * Get the user who received this notification
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
