<?php

namespace App\Models;

use App\Models\Interfaces\FilteredInterface;
use App\Traits\FilterScopeTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int id
 * @property string entity_type
 * @property string code
 * @property string name
 * @property string type
 * @property string|null lookup_type
 * @property int sort_order
 * @property bool is_required
 * @property bool is_unique
 * @property bool is_readonly
 * @property int parent_id
 * @property int created_by
 * @property int updated_by
 * @property Collection permissions
 * @property Collection options
 * @property float width
 * @property string tooltip
 * @property string tooltip_type
 * @property array property
 * @property bool is_multiple
 */
class CustomField extends Model implements FilteredInterface
{
    use HasFactory;
    use SoftDeletes;
    use FilterScopeTrait {
        attachCustomFilter as protected traitAttachCustomFilter;
    }

    public const STATUS_DISABLED = 'Disabled';
    public const FIELD_TYPE_CONTAINER = 'container';
    public const FIELD_TYPE_TEXT = 'text';
    public const FIELD_TYPE_TEXTAREA = 'textarea';
    public const FIELD_TYPE_EMAIL = 'email';
    public const FIELD_TYPE_PHONE = 'phone';
    public const FIELD_TYPE_SELECT = 'select';
    public const FIELD_TYPE_MULTISELECT = 'multiselect';
    public const FIELD_TYPE_DATE = 'date';
    public const FIELD_TYPE_DATETIME = 'datetime';
    public const FIELD_TYPE_PRICE = 'price';
    public const FIELD_TYPE_BOOL = 'bool';
    public const FIELD_TYPE_CHECKBOX = 'checkbox';
    public const FIELD_TYPE_NUMBER = 'number';
    public const FIELD_TYPE_FILE = 'file';
    public const FIELD_TYPE_IMAGE = 'image';
    public const FIELD_TYPE_LOOKUP = 'lookup';
    public const FIELD_TYPE_JSON = 'json';
    public const FIELD_TYPE_INTERNAL = 'internal'; //this field exist in main entity table. not it custom fields

    public const AVAILABLE_ENTITY_TYPES = [
        self::FIELD_TYPE_CONTAINER,
        self::FIELD_TYPE_TEXT,
        self::FIELD_TYPE_TEXTAREA,
        self::FIELD_TYPE_EMAIL,
        self::FIELD_TYPE_PHONE,
        self::FIELD_TYPE_SELECT,
        self::FIELD_TYPE_MULTISELECT,
        self::FIELD_TYPE_DATE,
        self::FIELD_TYPE_DATETIME,
        self::FIELD_TYPE_PRICE,
        self::FIELD_TYPE_BOOL,
        self::FIELD_TYPE_CHECKBOX,
        self::FIELD_TYPE_NUMBER,
        self::FIELD_TYPE_FILE,
        self::FIELD_TYPE_IMAGE,
        self::FIELD_TYPE_LOOKUP,
        self::FIELD_TYPE_JSON,
    ];

    public const LOOKUP_TYPE_LEAD_SOURCE = 'lead_source';
    public const LOOKUP_TYPE_LEAD_STATUS = 'lead_status';
    public const LOOKUP_TYPE_LEAD_TYPE = 'lead_type';
    public const LOOKUP_TYPE_SOLUTION = 'solution';
    public const LOOKUP_TYPE_INDUSTRY = 'industries';
    public const LOOKUP_TYPE_USER = 'users';
    public const LOOKUP_TYPE_ACCOUNT = 'account';
    public const LOOKUP_TYPE_OPPORTUNITY = 'opportunity';
    public const LOOKUP_TYPE_ACCOUNT_PARTNERSHIP = 'account_partnership_status';
    public const LOOKUP_TYPE_CONTACT_AUTHORITY = 'contact_authority';
    public const LOOKUP_TYPE_CONTACT_TYPE = 'contact_type';
    public const LOOKUP_TYPE_CONTACT = 'contact';

    public const AVAILABLE_LOOKUP_TYPES = [
        self::LOOKUP_TYPE_LEAD_SOURCE,
        self::LOOKUP_TYPE_LEAD_STATUS,
        self::LOOKUP_TYPE_LEAD_TYPE,
        self::LOOKUP_TYPE_SOLUTION,
        self::LOOKUP_TYPE_INDUSTRY,
        self::LOOKUP_TYPE_USER,
        self::LOOKUP_TYPE_ACCOUNT,
        self::LOOKUP_TYPE_OPPORTUNITY,
        self::LOOKUP_TYPE_ACCOUNT_PARTNERSHIP,
        self::LOOKUP_TYPE_CONTACT_AUTHORITY,
        self::LOOKUP_TYPE_CONTACT_TYPE,
        self::LOOKUP_TYPE_CONTACT,
    ];

    protected $table = 'custom_field';

    protected $fillable = [
        'entity_type',
        'code',
        'name',
        'type',
        'lookup_type',
        'sort_order',
        'is_required',
        'is_unique',
        'is_readonly',
        'parent_id',
        'created_by',
        'updated_by',
        'deleted_at',
        'width',
        'tooltip',
        'tooltip_type',
        'property',
        'is_multiple',
    ];

    protected $casts = [
        'sort_order' => 'int',
        'is_required' => 'boolean',
        'is_unique' => 'boolean',
        'is_readonly' => 'boolean',
        'is_multiple' => 'boolean',
        'property' => 'array',
    ];
    public static array $attributeTypeFields = [
        self::FIELD_TYPE_TEXT => 'text_value',
        self::FIELD_TYPE_TEXTAREA => 'text_value',
        self::FIELD_TYPE_PRICE => 'float_value',
        self::FIELD_TYPE_BOOL => 'boolean_value',
        self::FIELD_TYPE_SELECT => 'integer_value',
        self::FIELD_TYPE_MULTISELECT => 'text_value',
        self::FIELD_TYPE_CHECKBOX => 'text_value',
        self::FIELD_TYPE_EMAIL => 'text_value',
        self::FIELD_TYPE_PHONE => 'text_value',
        self::FIELD_TYPE_LOOKUP => 'integer_value',
        self::FIELD_TYPE_DATETIME => 'datetime_value',
        self::FIELD_TYPE_DATE => 'date_value',
        self::FIELD_TYPE_FILE => 'text_value',
        self::FIELD_TYPE_IMAGE => 'text_value',
        self::FIELD_TYPE_NUMBER => 'float_value',
        self::FIELD_TYPE_JSON => 'json_value',
    ];

    //@todo generate slug  for code

    public function options(): HasMany
    {
        return $this->hasMany(CustomFieldOption::class)->orderBy('sort_order');
    }

    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'type',
            ],
            'like' => [
                'name',
            ],
            'sort' => [
                'id',
                'entity_type',
                'name',
            ],
            'relation' => [
                'tag' => [
                    'eloquent_m' => 'tagsAssociation.masterTagsData',
                    'where' => 'tag',
                ],
            ],
            'json' => [],
            'range' => [],
            'custom' => [
                'entityType',
                'status',
            ],
            'custom_sort' => [],
        ];
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class);
    }
}
