<?php

/**
 * @package User
 * @author TechVillage <support@techvill.org>
 * @contributor Sabbir Al-Razi <[sabbir.techvill@gmail.com]>
 * @contributor Soumik Datta <[soumik.techvill@gmail.com]>
 * @created 20-05-2021
 * @modified 15-02-2023
 */

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Modules\Blog\Http\Models\Blog;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Notifications\Notifiable;
use App\Traits\ModelTraits\{hasFiles, Metable};
use Modules\MediaManager\Http\Models\ObjectFile;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\{Auth, DB, Validator};
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\{LogsActivity, CausesActivity};
use App\Rules\{CheckValidFile, CheckValidEmail, StrengthPassword};
use Modules\Reviews\Entities\Review;
use Modules\Subscription\Entities\SubscriptionDetails;

class User extends Authenticatable
{
    use HasFactory, Notifiable, ModelTrait, HasApiTokens, hasFiles, Metable, LogsActivity, CausesActivity, SoftDeletes;

    /**
     * Guard
     *
     * @var string
     */
    protected $guard = 'web';

    /**
     * Meta Table
     *
     * @var string
     */
    protected $metaTable = 'users_meta';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birthday',
        'address',
        'gender',
        'status',
        'activation_code',
        'activation_otp',
        'email_verified_at',
        'updated_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Attributes to log.
     * Alternatively $logAttributesToIgnore can be used.
     *
     * @var array
     */
    protected static $logAttributes = ['*'];

    /**
     * Attributes (when updated) that don't trigger an activity being logged
     *
     * @var array
     */
    protected static $ignoreChangedAttributes = [];

    /**
     * Events that will get logged automatically
     * Accepted => 'created', 'updated', 'deleted'
     *
     * @var array
     */
    protected static $recordEvents = [];

    /**
     * Customized log name
     *
     * @var string
     */
    protected static $logName = 'USER EVENT';

    /**
     * Log only changed attributes on update event
     *
     * @var boolean
     */
    protected static $logOnlyDirty = true;

    /**
     * Allow empty logs
     * Storing empty logs can happen when you only want to log a certain attribute but only another changes
     *
     * @var boolean
     */
    protected static $submitEmptyLogs = false;

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleted(function ($user) {
            $activities = Activity::where('causer_type', '=', User::class)
                ->where('causer_id', '=', $user->id)->get();

            if (count($activities) > 0) {
                foreach ($activities as $activity) {
                    $activity->delete();
                }
            }
        });
    }

    /**
     * Relation with Role model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_users');
    }

    /**
     * Get latest role
     *
     * @return \App\Models\Role
     */
    public function role()
    {
        $cacheKey = config('cache.prefix') . '.roleResource.' . $this->table . $this->id;

        if (cache()->has($cacheKey)) {
            return cache()->get($cacheKey);
        }

        cache()->put([$cacheKey => $this->roles()->latest()->first()], now()->addDays(7));

        return $this->roles()->latest()->first();
    }

    /**
     * Relation with RoleUser model
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function roleUser()
    {
        return $this->hasOne(RoleUser::class);
    }

    /**
     * Relation with ObjectFile model
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function avatarFile()
    {
        return $this->hasOne('Modules\MediaManager\Http\Models\ObjectFile', 'object_id')->where('object_type', 'users');
    }

    /**
     * Relation with Address model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Relation with Blog model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    /**
     * Relation with SubscriptionDetails model
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function subscriptionDescription()
    {
        return $this->hasOne(SubscriptionDetails::class)->where('status', 'Active')->first();
    }

    /**
     * relation with affiliate user
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function affiliateUser()
    {
        return $this->hasOne('Modules\Affiliate\Entities\AffiliateUser', 'user_id', 'id');
    }

    /**
     * Relation with User withdrawal setting model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function withdrawalSettings()
    {
        return $this->hasMany('Modules\Affiliate\Entities\UserWithdrawalSetting', 'user_id', 'id');
    }

    /**
     * Interact with the user's use case favorites.
     */
    protected function useCaseFavorites(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($favoritesMeta = $this->metas()->where('key', 'use_case_favorites')->first()) {
                    return is_array($favoritesMeta->value) ? $favoritesMeta->value : json_decode($favoritesMeta->value, true);
                }

                return [];
            },
            set: fn (array $value) => $this->setMeta('use_case_favorites', json_encode($value))
        );
    }

    /**
     * Interact with the user's theme preference meta.
     */
    protected function themePreference(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($themePreferenceMeta = $this->metas()->where('key', 'theme_preference')->first()) {
                    return $themePreferenceMeta->value;
                }
                return null;
            },
            set: fn (string $value) => $this->setMeta('theme_preference', $value)
        );
    }

    /**
     * Interact with the user's OpenAI document bookmarks.
     */
    protected function documentBookmarksOpenAI(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($bookmarksMeta = $this->metas()->where('key', 'document_bookmarks_openai')->first()) {
                    return is_array($bookmarksMeta->value) ? $bookmarksMeta->value : json_decode($bookmarksMeta->value, true);
                }
                return [];
            },
            set: fn (array $value) => $this->setMeta('document_bookmarks_openai', json_encode($value))
        );
    }

    /**
     * User Store Validation from frontend
     *
     * @param  array  $data
     * @param  bool  $captchaAction
     * @return mixed
     */
    protected static function siteStoreValidation($data = [], $captchaAction = true)
    {
        $captchaRule = 'nullable';
        if (!request()->is('api/*') && isRecaptchaActive() && $captchaAction) {
            $data['gCaptcha'] = $data['g-recaptcha-response'] ?? null;
            $captchaRule = 'required|captcha';
        }

        $sendMail = isset($data['send_mail']) && strtolower($data['send_mail']) == 'on' ? 'on' : false;
        $validator = Validator::make($data, [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'max:191', 'unique:users,email,NULL,id,deleted_at,NULL', new CheckValidEmail],
            'password' => ['required', 'max:191', new StrengthPassword],
            'status' => 'required|in:Pending,Active,Inactive,Deleted',
            'send_mail' => 'in:' . $sendMail,
            'attachment'  => [new CheckValidFile(getFileExtensions(2))],
            'gCaptcha' => $captchaRule
        ]);

        return $validator;
    }

    /**
     * User Update Validation from frontend
     *
     * @param array $data
     * @return mixed
     */
    protected static function siteUpdateValidation($data = [])
    {
        $validator = Validator::make($data, [
            'name' => 'required|min:3|max:191',
            'image'  => ['nullable', new CheckValidFile(getFileExtensions(2)), 'max:' . preference('file_size') * 1024],
        ], [
            'image.max' => __('Maximum File Size :x MB.', ['x' => preference('file_size')])
        ]);

        return $validator;
    }

    /**
     * User Email Update Validation from frontend
     *
     * @param array $data
     * @param int $id
     * @return mixed
     */
    protected static function siteUpdateEmailValidation($data = [])
    {
        $validator = Validator::make($data, [
            'email' => ['required', 'max:191', 'unique:users,email,NULL,id,deleted_at,NULL', new CheckValidEmail],
        ]);

        return $validator;
    }

    /**
     * User Password Update Validation from frontend
     *
     * @param array $data
     * @return mixed
     */
    protected static function siteUpdatePasswordValidation($data = [])
    {
        $validator = Validator::make($data, [
            'old_password' => 'required|max:191',
            'new_password' => ['required', 'max:191', new StrengthPassword],
            'confirm_password' => 'required|max:191|same:new_password',

        ]);

        return $validator;
    }

    /**
     * Update password validation
     *
     * @param array $data
     * @return mixed
     */
    protected static function updatePasswordValidation($data = [])
    {
        $sendMail = isset($data['send_mail']) && strtolower($data['send_mail']) == 'on' ? 'on' : false;
        $validator = Validator::make($data, [
            'password'    => ['required', new StrengthPassword],
            'confirm_password' => 'required|same:password',
            'send_mail' => 'in:' . $sendMail
        ]);

        return $validator;
    }

    /**
     * Update validation
     *
     * @param array $data
     * @param int $id
     * @return mixed
     */
    protected static function updateValidation($data, $id)
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => ['required', 'unique:users,email,' . $id, new CheckValidEmail],
        ];

        if (Auth::user()->role()->type != 'admin') {
            $rules['status'] = 'required|in:Pending,Active,Inactive,Deleted';
            $rules['role_ids'] = 'required';
        }
        $validator = Validator::make($data, $rules);

        return $validator;
    }

    /**
     * Update password validation
     *
     * @param array $data
     * @param int $id
     * @return mixed
     */
    protected static function updateProfileValidation($data, $id)
    {
        $validator = Validator::make($data, [
            'name'        => 'required|min:3|max:191',
            'email'       => ['required', 'max:191', 'unique:users,email,' . $id, new CheckValidEmail],
            'designation' => 'nullable|min:3|max:90',
            'description' => 'nullable|min:3|max:191',
            'facebook'    => 'nullable|url',
            'twitter'     => 'nullable|url',
            'instagram'   => 'nullable|url',
        ]);

        return $validator;
    }

    /**
     * Import validation
     *
     * @param array $data
     * @return mixed
     */
    protected static function importValidation($data = [])
    {
        $validator = Validator::make($data, [
            'file' => 'required|mimes:csv,txt|max:1024',
        ], [
            'file.mimes' => __('The file must be a file of type: :x', ['x' => __('CSV')])
        ]);

        return $validator;
    }

    /**
     * Store User data
     *
     * @param array $data
     * @return int|bool
     */
    public function store($data = [], $from = null, $url = null)
    {
        $id = parent::insertGetId($data);

        if ($from == 'url') {
            $this->uploadFilesFromUrl($url, ['isUploaded' => false, 'isOriginalNameRequired' => true, 'thumbnail' => false, 'isSavedInObjectFiles' => true]);
        } else {
            $fileIds = [];

            if (request()->has('file_id')) {
                foreach (request()->file_id as $data) {
                    $fileIds[] = $data;
                }
            }

            ObjectFile::storeInObjectFiles($this->objectType(), $this->objectId(), $fileIds);
        }

        if (!empty($id)) {
            self::forgetCache();
            return $id;
        }

        return false;
    }

    /**
     * Update User
     *
     * @param array $data
     * @param int $id
     * @return boolean
     */
    public function updateUser($data = [], $id = null)
    {
        $result = parent::where('id', $id)->first();

        if (is_null($result)) {
            return false;
        }

        if ($id == 1 && isset($data['userData']) && isset($data['userData']['status']) && $data['userData']['status'] == 'Deleted') {
            $data['userData']['status'] = 'Active';
        }

        $result->update($data['userData'] ?? $data);
        $result->updateFiles(['isUploaded' => false, 'isSavedInObjectFiles' => true, 'isOriginalNameRequired' => true, 'thumbnail' => false]);

        if (isset($data['userMetaData'])) {
            $result->setMeta($data['userMetaData']);
            $result->save();
        }

        self::forgetCache();
        self::forgetCache('roleResource.' . 'users' . $id);
        return true;
    }

    /**
     * Site Update User
     *
     * @param array $data
     * @param int $id
     * @return boolean
     */
    public function siteUpdatePassword($data = [], $id = null)
    {
        $result = parent::where('id', $id);
        if ($result->exists()) {
            $result->update($data);
            self::forgetCache();
            return true;
        }

        return false;
    }

    /**
     * Get user data
     * @param array $data
     * @return collection|boolean
     */
    public function getData($token)
    {
        $reset = PasswordReset::where('otp', $token)->orWhere('token', $token)->first();
        $user = null;
        if (!empty($reset)) {
            $user = parent::where('email', $reset->email)->first();
        }

        return !empty($user) ? $user : false;
    }

    /**
     * Get user role slug
     * @param int $userId
     * @return array
     */
    public static function getSlug($userId)
    {
        $roles = Role::select('slug')->whereIn('id', parent::getRoleIdsByUserId($userId))->get();
        $array = [];
        foreach ($roles as $key => $value) {
            $array[] = $value->slug;
        }
        return $array;
    }

    /**
     * Check user verification type
     *
     * @param string $type
     * @return boolean
     */
    public static function userVerification($type = null)
    {
        $verificationType = ['otp', 'token', 'both'];
        $preference = preference('email');

        if (is_null($type) || !in_array($type, $verificationType)) {
            return false;
        }

        foreach ($verificationType as $key => $value) {
            if ($value == $type) {
                return $preference == 'both' || $preference == $value;
            }
        }
    }

    /**
     * Remove user profile picture
     *
     * @return boolean
     */
    public function removeProfileImage()
    {
        if (parent::find(auth()->user()->id)->deleteFiles(['thumbnail' => false])) {
            return true;
        }
        return false;
    }

    /**
     * User Email Validation
     *
     * @param array $data
     * @return mixed
     */
    public static function userEmailValidation($data = [])
    {
        $validator = Validator::make($data, [
            'email' => ['required', 'max:191', 'unique:users,email', new CheckValidEmail],
        ]);
        return $validator;
    }

    /**
     * Gets event description for activity model event logging
     * Used by spatie/laravel-activitylog
     *
     * @return string
     */
    public function getDescriptionForEvent(string $eventName): string
    {
        return "This model has been {$eventName}";
    }

    /**
     * Implements the getActivitylogOptions abstract method
     *
     * @return LogOptions
     */
    public function getActivitylogOptions() : LogOptions
    {
        return LogOptions::defaults()
            ->useLogName(self::$logName)
            ->logOnly(self::$logAttributes)
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn (string $eventName) => "User model has been {$eventName}");
    }

    /**
     * Relation with Review model
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function review()
    {
        return $this->hasMany(Review::class)->latest();
    }

    /**
     * Get user reviews
     *
     * @return array
     */
    public static function getUserReviews($type, $limit, $ids = []){

        $reviews = []; 

        if ($type == 'latestReviews') {

            $reviews = parent::with('metas')->join('reviews', 'reviews.user_id', 'users.id')
            ->select('reviews.title as title', 'reviews.comments as comments', 'reviews.rating as rating', 'users.name as name', 'users.id as id', 'users.*')
            ->where('reviews.status', 'Active')
            ->orderBy('reviews.id', 'DESC')
            ->take($limit)
            ->get();
        }

        if ($type == 'selectedReviews') {
            
            $reviews = parent::with('metas')->join('reviews', 'reviews.user_id', 'users.id')
            ->select('reviews.title as title', 'reviews.comments as comments', 'reviews.rating as rating', 'users.name as name', 'users.id as id', 'users.*')
            ->whereIn('reviews.id', $ids)
            ->where('reviews.status', 'Active')
            ->orderBy('reviews.id', 'DESC')
            ->get();
        }
        
        $classOne = 'bg-color-F6 dark:bg-color-29 p-5 rounded-[20px] relative h-max';
        $classTwo = 'bg-white dark:bg-color-14 border border-color-DF dark:border-color-47 p-5 rounded-[20px] relative h-max';
        $rev = [];

        foreach ($reviews as $key => $review) {

            if ($key % 4 == 1 || $key % 4 == 2)  {

                $rev[$key] = [
                    'class' => $classTwo,
                    'user_name' => $review->name,
                    'title' => $review->title,
                    'comments' => $review->comments,
                    'rating' => number_format((float)$review->rating, 1, '.', ''),
                    'photo' => $review->fileUrl(),
                    'designation' => $review->designation
                ];

            } else {

                $rev[$key] = [
                    'class' => $classOne,
                    'user_name' => $review->name,
                    'title' => $review->title,
                    'comments' => $review->comments,
                    'rating' => number_format((float)$review->rating, 1, '.', ''),
                    'photo' => $review->fileUrl(),
                    'designation' => $review->designation
                ];
            }
        }

        $result = array_chunk($rev, 2, true);

        return $result;
    }

    /**
     * Check token existence
     * @param array $data
     * @return boolean
     */
    public function tokenExist($data)
    {
        if (parent::where('activation_otp', $data)->first()) {
            return true;
        }

        return false;
    }

    /**
     * Relation with Thread model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function threads()
    {
        return $this->hasMany('Modules\Ticket\Http\Models\Thread', 'assigned_member_id', 'id');

    }

    /**
     * Update User
     *
     * @param array $data
     * @param int $id
     * @return boolean
     */
    public function updateMember($data = [], $id = null)
    {
        return  parent::where('id', $id)
        ->update([
            'name'   => $data['name']
        ]);
    }
    
    /**
     * Has Credit
     * 
     * @param string $creditType
     * @return bool
     */
    public function hasCredit($creditType)
    {
        $limit = $this->getMeta($creditType . '_limit');
        $used = $this->getMeta($creditType . '_used');
        
        return boolval($limit) && ($limit - $used) > 0;

    }

    public function scopeStatus($query, $status)
    {
        $query->where('status', $status);
    }
}
