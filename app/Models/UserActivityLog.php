<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivityLog extends Model
{
    use HasFactory;

    protected $table = 'user_activity_log';

    protected $fillable = [
        'user_id',
        'component',
        'component_id',
        'activity',
        'time',
    ];

    public static function saveActivityData($componentId, $componentName, $activity, $userId): void
    {
        self::query()->create([
            'user_id' => $userId,
            'component' => $componentName,
            'component_id' => $componentId,
            'activity' => $activity,
            'time' => now(),
        ]);
    }
}
