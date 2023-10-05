<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Helpers\DBHelper;
use App\Models\Interfaces\FilteredInterface;
use App\Models\Interfaces\LoggableModelInterface;
use App\Models\Interfaces\StoreFileInterface;
use App\Traits\FilterScopeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * @property int id
 * @property string first_name
 * @property string last_name
 * @property string email
 * @property int updated_by
 * @property string password
 * @property string status
 * @property string|null avatar
 * @property int created_by
 * @property Carbon created_at
 * @property Carbon|null updated_at
 * @property string theme_mode
 * @property TusFileData|null userDataFile
 * @property UserLastLogin|null lastLogin
 * @property UserLastLoginActivity|null lastAuthActivity
 * @property User createdBy
 * @property User updatedBy
 * @property Role role
 * @property array dashboard_blocks
 * @property string user_signature
 * @method Builder filter(array $params)
 */
class User extends Authenticatable implements JWTSubject, LoggableModelInterface, FilteredInterface, StoreFileInterface
{
    use HasFactory;
    use Notifiable;
    use FilterScopeTrait;
    use SoftDeletes;

    public const STATUS_ACTIVE = 'Active';
    public const STATUS_INACTIVE = 'Inactive';
    public const STATUS_DISABLED = 'Disabled';
    public const PROFILE_URI = 'assets/system/user/profile/';
    public const THEME_MODE_LIGHT = 'light';
    public const THEME_MODE_DARK = 'dark';
    public const THEME_MODE_AUTO = 'auto';
    public const AJAY_EMAIL = 'ajay@mvix.com';
    public const AVAILABLE_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
        self::STATUS_DISABLED,
    ];

    public const EMAIL_FOR_CRON_USER = 'cron_user@mvix.com';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id',
        'avatar',
        'status',
        'role_id',
        'zoho_entity_id',
        'user_signature',
        'dashboard_blocks',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dashboard_blocks' => 'array',
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return string
     */
    public function getJWTIdentifier(): string
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public static function getLoggableName(): string
    {
        return 'User';
    }


    public static function filterArray(): array
    {
        return [
            'equal' => [
                'id',
                'role_id',
            ],
            'like' => [
                'firstName',
                'lastName',
                'email',
                'phoneNo',
            ],
            'sort' => [
                'id',
                'firstName',
                'lastName',
                'createdBy',
                'updatedBy',
                'createdAt',
                'updatedAt',
                'status',
                'email',
                'phoneNo',
            ],
            'relation' => [
                'lastLogin' => [
                    'eloquent_m' => 'userActivity',
                    'where' => 'activity_time',
                ],
                'lastAuthActivity' => [
                    'eloquent_m' => 'userActivity',
                    'where' => 'activity_time',
                ],
                'tag' => [
                    'eloquent_m' => 'tagsAssociation.masterTagsData',
                    'where' => 'tag',
                ],
            ],
            'custom' => [
                'fullName',
                'username',
                'search',
                'status',
            ],
            'custom_sort' => [
                'lastLogin',
                'lastAuthActivity',
                'fullName',
                'role',
                'username',
            ],
        ];
    }

    /**
     * @param Builder $query
     * @param string $order
     * @return Builder
     */
    protected function sortByUsername(Builder $query, string $order): Builder
    {
        $query
            ->orderBy('first_name', $order)
            ->orderBy('last_name', $order);

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $order
     * @return Builder
     */
    protected function sortByFullName(Builder $query, string $order): Builder
    {
        $query
            ->orderBy('first_name', $order)
            ->orderBy('last_name', $order);

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $order
     * @return Builder
     */
    protected function sortByLastLogin(Builder $query, string $order): Builder
    {
        $query
            ->leftJoin(
                'user_last_login',
                function (JoinClause $join) {
                    $join->on('users.id', '=', 'user_last_login.user_id');
                },
            )
            ->orderBy('user_last_login.activity_time', $order);

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $order
     * @return Builder
     */
    protected function sortByLastAuthActivity(Builder $query, string $order): Builder
    {
        $query
            ->leftJoin(
                'user_last_login_activity',
                function ($join) {
                    /** @var JoinClause $join */
                    $join->on('users.id', '=', 'user_last_login_activity.user_id');
                },
            )
            ->orderBy('user_last_login_activity.activity_time', $order);

        return $query;
    }

    /**
     * @param Builder $query
     * @param string $order
     * @return Builder
     */
    protected function sortByRole(Builder $query, string $order): Builder
    {
        $query
            ->leftJoin(
                'role',
                'users.role_id',
                '=',
                'role.id',
            )
            ->orderBy('role.name', $order);

        return $query;
    }

    protected function sortByGroup(Builder $query, string $order): Builder
    {
        $query
            ->leftJoin(
                'org_user_group_association',
                'org_user.id',
                '=',
                'org_user_group_association.user_id',
            )
            ->leftJoin(
                'org_group',
                'org_user_group_association.group_id',
                '=',
                'org_group.id',
            )
            ->orderBy('org_group.title', $order);

        return $query;
    }

    protected function sortByEmail(Builder $query, string $order): Builder
    {
        $query
            ->orderBy('email', $order);

        return $query;
    }

    protected function filterFullName(Builder $query, string $value): Builder
    {
        $trimValue = preg_replace('/\s{2,}/', ' ', $value);
        $arrayFullName = explode(' ', $trimValue);

        $firstName = $arrayFullName[0];
        $lastName = $arrayFullName[1] ?? '';

        $query
            ->where('first_name', $firstName)
            ->where('last_name', $lastName);

        return $query;
    }

    protected function filterByUsername(Builder $query, string $value): Builder
    {
        $searchValue = '%' . DBHelper::escapeForLike(trim($value)) . '%';

        return $query->where(function ($query) use ($searchValue) {
            $query
                ->orWhere('first_name', 'like', $searchValue)
                ->orWhere('last_name', 'like', $searchValue);
        });
    }

    /**
     * @param Builder $query
     * @param string $value
     * @return Builder
     */
    protected function filterBySearch(Builder $query, string $value): Builder
    {
        return $query->where(function ($query) use ($value) {
            $trimValue = preg_replace('/\s{2,}/', ' ', trim($value));
            $arrayFullName = explode(' ', $trimValue);

            $firstName = $arrayFullName[0];
            $lastName = $arrayFullName[1] ?? '';

            $searchValue = '%' . DBHelper::escapeForLike(trim($value)) . '%';

            if ($lastName) {
                $query
                    ->orWhere(function ($query) use ($firstName, $lastName) {
                        $query
                            ->where('first_name', 'like', '%' . $firstName . '%')
                            ->where('last_name', 'like', '%' . $lastName . '%');
                    })
                    ->orWhere(function ($query) use ($firstName, $lastName) {
                        $query
                            ->where('first_name', 'like', '%' . $lastName . '%')
                            ->where('last_name', 'like', '%' . $firstName . '%');
                    });
            } else {
                $query
                    ->orWhere('first_name', 'like', $searchValue)
                    ->orWhere('last_name', 'like', $searchValue);
            }
            $query
                ->orWhere('email', 'LIKE', $searchValue)
                ->orWhereHas('role', function (Builder $query) use ($searchValue) {
                    $query->where('display_name', 'LIKE', $searchValue);
                });
        });
    }

    public function storedFiles(): MorphMany
    {
        return $this->morphMany(TusFileData::class, 'media');
    }

    public function userDataFile(): MorphOne
    {
        return $this->morphOne(TusFileData::class, 'media')
            ->where('target', TusFileData::TARGET_USER_DATA);
    }

    public function lastLogin(): HasOne
    {
        return $this->hasOne(UserLastLogin::class, 'user_id');
    }

    public function lastAuthActivity(): HasOne
    {
        return $this->hasOne(UserLastLoginActivity::class, 'user_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'updated_by');
    }


    public function comments(): MorphMany
    {
        // https://laravel.com/docs/10.x/eloquent-relationships#polymorphic-relationships   @todo check
        return $this->morphMany(Tag::class, 'commentable');
    }

    public function hasPermissionTo(string $entityType, string $permissionAction, ?string $fieldName = null): bool
    {
        if ($this->isMainAdmin()) {
            //admin have all permissions by default
            return true;
        }

        $permissions = Permission::findPermissionByRole(
            $entityType,
            $permissionAction,
            $this->role,
            $fieldName,
        );

        return !$permissions->isEmpty();
    }

    public function isMainAdmin(): bool
    {
        return $this->role->name == Role::MAIN_ADMINISTRATOR_ROLE;
    }
}
