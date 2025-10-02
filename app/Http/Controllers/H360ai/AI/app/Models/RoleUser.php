<?php
/**
 * @package Role user
 * @author TechVillage <support@techvill.org>
 * @contributor Sabbir Al-Razi <[sabbir.techvill@gmail.com]>
 * @created 20-05-2021
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Model;
use Cache;

class RoleUser extends Model
{
    use HasFactory;

    /**
     * timestamps
     * @var boolean
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]|bool
     */
    protected $guarded = false;

    /**
     * Relation with User Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * Relation with Role Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsTo('App\Models\Role', 'role_id');
    }

    /**
     * Store
     * @param array $data
     * @return boolean
     */
    public function store($data = [])
    {
    	if (parent::insert($data)) {
    		return true;
    	}

        return false;
    }

    /**
     * get roel id by user
     * Get the roles of a user by user ID
     * @param integer $userId
     * @return array
     */
    public static function getRoleIDByUser($userId)
    {
        $data = Cache::get(config('cache.prefix') . '-role-id-by-user-' . $userId);

        if (empty($data)) {

            $data = optional(parent::where('user_id', $userId)->first())->role_id;
            Cache::put(config('cache.prefix') . '-role-id-by-user-' . $userId, $data, 7 * 86400);
        }

        return $data;
    }

    /**
     * Update RoleUser
     * @param array $data
     * @param null $id
     * @return bool
     */
    public function update($data = [], $id = null)
    {
        $result = parent::where('user_id', $data['user_id']);
        if ($result->exists()) {
            $result->update($data);
            Cache::forget(config('cache.prefix') . '-role-id-by-user-' . $data['user_id']);
            return true;
        } else {
            parent::insert($data);
            Cache::forget(config('cache.prefix') . '-role-id-by-user-' . $data['user_id']);
        }

        return false;
    }

    /**
     * delete
     * @param int $id
     * @return array
     */
    public function remove($id)
    {
        $data = ['status' => 'fail', 'message' => __('The :x does not exist.', ['x' => __('User')])];
        $record = parent::where('user_id', $id);
        if (!empty($record)) {
            $record->delete();

            $data['status'] = 'success';
            $data['message'] = __('User role deleted successfully.');
            Cache::forget(config('cache.prefix') . '-role-id-by-user-' . $id);
        }

        return $data;
    }
}
