<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\CreatedByUpdatedByTrait;
use App\Traits\FilterScopeTrait;
use App\Traits\TagAssociationTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string entity
 * @property string template
 * @property string name
 * @property string status
 * @property string thumb_image
 * @property bool is_default
 * @property bool is_shared
 * @property User createdBy
 * @property User updatedBy
 */
class Template extends Model implements FilteredInterface
{
    use HasFactory;
    use TagAssociationTrait;
    use CreatedByUpdatedByTrait;
    use SoftDeletes;
    use FilterScopeTrait;

    protected $table = 'template';

    protected $fillable = [
        'entity',
        'template',
        'is_default',
        'name',
        'status',
        'thumb_image',
        'created_by',
        'updated_by',
        'is_shared',
    ];
    public const TEMPLATE_TYPE_INVOICE = 'Invoice';
    public const TEMPLATE_TYPE_ESTIMATE = 'Estimate';
    public const TEMPLATE_TYPE_PROPOSAL = 'Proposal';

    public const TEMPLATE_TYPE_EMAIL = 'Email';

    public const TEMPLATE_GROUP_MY = 'my';
    public const TEMPLATE_GROUP_SHARED = 'shared';
    public const TEMPLATE_GROUP_SYSTEM = 'system';

    public const AVAILABLE_TEMPLATE_TYPES = [
        self::TEMPLATE_TYPE_INVOICE,
        self::TEMPLATE_TYPE_ESTIMATE,
        self::TEMPLATE_TYPE_EMAIL,
        self::TEMPLATE_TYPE_PROPOSAL,
    ];

    protected $casts = [
        'is_default' => 'bool',
        'is_shared' => 'bool',
    ];

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'is_shared',
                'status',
                'is_default',
                'name',
            ],
            'like' => [],
            'sort' => [
                'id',
            ],
            'relation' => [],
            'custom' => [
                'tag',
                'entity',
                'group',
            ],
            'custom_sort' => [],
        ];
    }

    protected function filterByEntity(Builder $query, string|array $value): Builder
    {
        /** @var User $user */
        $user = auth()->user();

        if (is_array($value)) {
            $query->where(function ($query) use ($value, $user) {
                foreach ($value as $entity) {
                    switch ($entity) {
                        case Template::TEMPLATE_TYPE_PROPOSAL:
                        case Template::TEMPLATE_TYPE_INVOICE:
                            $query->orWhere('entity', $entity);
                            break;

                        case Template::TEMPLATE_TYPE_EMAIL:
                            $query->orWhere(function ($query) use ($entity, $user) {
                                $query->where('entity', $entity)->where(function ($query) use ($user) {
                                    $query->where('created_by', $user->getKey())
                                        ->orWhere('is_shared', true);
                                });
                            });

                            break;
                    }
                }
            });
        } else {
            switch ($value) {
                case Template::TEMPLATE_TYPE_PROPOSAL:
                case Template::TEMPLATE_TYPE_INVOICE:
                    $query->where('entity', $value);
                    break;


                case Template::TEMPLATE_TYPE_EMAIL:
                    $query->where('entity', $value)->where(function ($query) use ($user) {
                        $query->where('created_by', $user->getKey())
                            ->orWhere('is_shared', true);
                    });
                    break;
            }
        }

        return $query;
    }

    protected function filterByGroup(Builder $query, string|array $value): Builder
    {
        /** @var User $user */
        $user = auth()->user();

        switch ($value) {
            case self::TEMPLATE_GROUP_MY:
                $query->where('created_by', $user->getKey());
                break;

            case self::TEMPLATE_GROUP_SHARED:
                $query->where('is_shared', true);
                break;

            case self::TEMPLATE_GROUP_SYSTEM:
                $systemUser = User::query()->whereHas('role', function ($query) {
                    $query->where('name', Role::MAIN_ADMINISTRATOR_ROLE);
                })->pluck('id')->toArray();

                $query->whereIn('created_by', $systemUser);
                break;
        }

        return $query;
    }
}
