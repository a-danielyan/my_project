<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\CreatedByUpdatedByTrait;
use App\Traits\FilterScopeTrait;
use App\Traits\TagAssociationTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 *
 * @property int id
 * @property string name
 * @property string license_type
 * @property int license_duration_in_month
 * @property User createdBy
 * @property User|null updatedBy
 * @property Carbon created_at
 * @property Tag[] tag
 *
 */
class License extends Model implements FilteredInterface
{
    use HasFactory;
    use SoftDeletes;
    use CreatedByUpdatedByTrait;
    use TagAssociationTrait;
    use FilterScopeTrait;

    protected $table = 'license';

    public const STATUS_DISABLED = 'Disabled';

    public const AVAILABLE_LICENSE_TYPES = [
        'DeviceLicense',
        'AccountLicense',
        'AnnualMaintenance',
        'AccountFeatureLicense',
        'DeviceFeatureLicense',
    ];

    public const LICENSE_STATUS_ACTIVE = 'Active';
    public const LICENSE_STATUS_INACTIVE = 'InActive';

    protected $fillable = [
        'name',
        'status',
        'license_duration_in_month',
        'license_type',
        'created_by',
    ];

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'name',
                'license_type',
                'license_duration_in_month',
            ],
            'like' => [],
            'sort' => [
                'id',
                'name',
                'created_at',
                'license_type',
                'license_duration_in_month',
            ],
            'relation' => [
                'tag' => [
                    'eloquent_m' => 'tagsAssociation.masterTagsData',
                    'where' => 'tag',
                ],
            ],
            'custom' => [
                'status',
            ],
            'custom_sort' => [],
        ];
    }
}
