<?php

namespace App\Models;

use App\Http\Resource\AccountMinimalResource;
use App\Http\Resource\ContactMinimalResource;
use App\Http\Resource\OpportunityMinimalResource;
use App\Http\Resource\ReferenceTables\ContactTypeResource;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @property int id
 * @property int field_id
 * @property int entity_id
 * @property string|null text_value
 * @property bool|null boolean_value
 * @property int|null integer_value
 * @property float|null float_value
 * @property Carbon|null datetime_value
 * @property Carbon|null date_value
 * @property array|null json_value
 * @property CustomField|null customField
 * @property CustomFieldOption|null relatedOption
 * @property User relatedUser
 * @property Account relatedAccount
 * @property Opportunity relatedOpportunity
 * @property Contact relatedContact
 * @property ContactType relatedContactType
 */
class CustomFieldValues extends Model
{
    use HasFactory;

    protected $table = 'custom_field_values';

    protected $fillable = [
        'field_id',
        'entity_id',
        'entity',
        'text_value',
        'boolean_value',
        'integer_value',
        'float_value',
        'datetime_value',
        'date_value',
        'json_value',
    ];

    protected $casts = [
        'json_value' => 'array',
    ];

    protected function textValue(): Attribute
    {
        return Attribute::make(
            get: function () {
                $attribute = $this->customField;
                if (!$attribute || $attribute->type == CustomField::FIELD_TYPE_INTERNAL) {
                    return null;
                }
                $fieldType = CustomField::$attributeTypeFields[$attribute->type];

                //@todo this is may be slowest part  as possible solution we can cache
                // lead source/ lead status and other small tables
                if ($attribute->type == 'lookup') {
                    return $this->getLookUpValues($attribute);
                }

                if ($attribute->type == CustomField::FIELD_TYPE_SELECT) {
                    return $this->getSelectValues($attribute);
                }

                if ($attribute->type == CustomField::FIELD_TYPE_MULTISELECT) {
                    return $this->getMultiSelectValues($attribute);
                }

                if ($attribute->type == CustomField::FIELD_TYPE_JSON) {
                    return json_decode($this->attributes['json_value']);
                }
                if ($attribute->type == CustomField::FIELD_TYPE_BOOL) {
                    return (bool)$this->attributes[$fieldType] ?? false;
                }

                return $this->attributes[$fieldType] ?? null;
            },
        );
    }

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class, 'field_id');
    }

    public function relatedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'integer_value');
    }

    public function relatedAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'integer_value');
    }

    public function relatedContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'integer_value');
    }

    public function relatedContactType(): BelongsTo
    {
        return $this->belongsTo(ContactType::class, 'integer_value');
    }

    public function relatedOpportunity(): BelongsTo
    {
        return $this->belongsTo(Opportunity::class, 'integer_value');
    }

    public function relatedOption(): BelongsTo
    {
        return $this->belongsTo(CustomFieldOption::class, 'integer_value');
    }

    private function getLookUpValues($attribute)
    {
        if ($this->attributes['integer_value'] !== null) {
            return $this->getRelatedRecord($attribute->lookup_type, $this->attributes['integer_value']);
        } else {
            if ($this->attributes['json_value'] !== null) {
                $result = [];

                $encodedValues = json_decode($this->attributes['json_value']);
                if (is_array($encodedValues)) {
                    foreach ($encodedValues as $lookupId) {
                        try {
                            $result[] = $this->getRelatedRecord($attribute->lookup_type, $lookupId);
                        } catch (Throwable) {
                        }
                    }
                }

                return $result;
            } else {
                return null;
            }
        }
    }


    private function getRelatedRecord(string $lookupType, int $lookupId)
    {
        switch ($lookupType) {
            case CustomField::LOOKUP_TYPE_LEAD_SOURCE:
            case CustomField::LOOKUP_TYPE_LEAD_STATUS:
            case CustomField::LOOKUP_TYPE_LEAD_TYPE:
            case CustomField::LOOKUP_TYPE_SOLUTION:
            case CustomField::LOOKUP_TYPE_INDUSTRY:
                return Cache::remember(
                    $lookupType . '#' . $lookupId . 'obj',
                    60 * 60 * 24 * 7,
                    function () use ($lookupType, $lookupId) {
                        return DB::table($lookupType)->select(['id', 'name', 'status'])->find($lookupId);
                    },
                );


            case CustomField::LOOKUP_TYPE_USER:
                return Cache::remember(
                    $lookupType . '#' . $lookupId . 'obj',
                    60 * 60,
                    function () {
                        return $this->relatedUser;
                    },
                );

            case CustomField::LOOKUP_TYPE_ACCOUNT:
                return Cache::remember(
                    $lookupType . '#' . $lookupId . 'obj',
                    60 * 60,
                    function () {
                        $relatedEntity = $this->relatedAccount;
                        $relatedEntity?->load('customFields');

                        return new AccountMinimalResource(
                            $relatedEntity,
                            customFieldList: [
                                'first-name',
                                'last-name',
                                'account-name',
                            ],
                        );
                    },
                );


            case CustomField::LOOKUP_TYPE_CONTACT:
                return Cache::remember(
                    $lookupType . '#' . $lookupId . 'obj',
                    60 * 60,
                    function () {
                        $relatedEntity = $this->relatedContact;
                        $relatedEntity?->load('customFields');

                        return new ContactMinimalResource(
                            $relatedEntity,
                            customFieldList: [
                                'email',
                                'phone',
                                'mobile',
                                'first-name',
                                'last-name',
                            ],
                        );
                    },
                );

            case CustomField::LOOKUP_TYPE_CONTACT_TYPE:
                return Cache::remember(
                    $lookupType . '#' . $lookupId . 'obj',
                    60 * 60,
                    function () {
                        return new ContactTypeResource($this->relatedContactType);
                    },
                );


            case CustomField::LOOKUP_TYPE_OPPORTUNITY:
                return Cache::remember(
                    $lookupType . '#' . $lookupId . 'obj',
                    60 * 60,
                    function () {
                        return new OpportunityMinimalResource(
                            $this->relatedOpportunity()->with(['customFields', 'customFields.customField'])->first(),
                            customFieldList: [
                                'next-step',
                                'amount',
                                'probability',
                                'description',
                                'opportunity_name',
                                'project_type',
                                'expecting_closing_date',
                                'stage',
                                'expected_revenue',
                            ],
                        );
                    },
                );


            default:
                return DB::table($lookupType)->find($lookupId);
        }
    }

    private function getSelectValues($attribute): array
    {
        return ['id' => $this->relatedOption?->id, 'name' => $this->relatedOption?->name];
    }

    private function getMultiSelectValues($attribute): ?array
    {
        $items = explode(',', $this->attributes['text_value']);

        $items = CustomFieldOption::query()->whereIn('id', $items)->get(['name', 'id']);

        return $items->toArray();
    }

    public function entity(): MorphTo
    {
        return $this->morphTo();
    }

    public function customFieldValue(): HasOne
    {
        return $this->hasOne(CustomFieldValues::class, 'entity_id', 'entity_id');
    }
}
