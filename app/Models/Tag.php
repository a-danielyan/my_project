<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\CreatedByUpdatedByTrait;
use App\Traits\FilterScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string tag
 * @property string entity_type
 * @property string text_color
 * @property string background_color
 * @property string entity
 * @property User createdBy
 * @property User|null updatedBy
 */
class Tag extends Model implements FilteredInterface
{
    use HasFactory;
    use CreatedByUpdatedByTrait;
    use FilterScopeTrait;
    use SoftDeletes;

    protected $table = 'tag';

    protected $fillable = [
        'tag',
        'background_color',
        'text_color',
        'created_by',
        'entity_type',
    ];

    public const AVAILABLE_ENTITY = [
        'Account',
        'Activity',
        'Contact',
        'Device',
        'Estimate',
        'Lead',
        'Opportunity',
        'Product',
        'User',
        'License',
        'Template',
    ];

    /**
     * Get the parent commentable model (post or video).
     */
    public function commentable(): MorphTo
    {
        //@todo check
        return $this->morphTo();
    }

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
            ],
            'like' => [
                'tag',
            ],
            'sort' => [
                'id',
                'tag',
                'createdBy',
                'updatedBy',
                'createdAt',
                'updatedAt',
            ],
            'custom' => [
                'entityType',
            ],
        ];
    }
}
