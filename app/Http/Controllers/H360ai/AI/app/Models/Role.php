<?php
/**
 * @package Role
 * @author TechVillage <support@techvill.org>
 * @contributor Al Mamun <[almamun.techvill@gmail.com]>
 * @created 07-03-2023
 */

namespace App\Models;

use App\Models\Model;

class Role extends Model
{
    /**
     * timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Fillable
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'type',
        'description'
    ];

     /**
     * Relation with RoleUser model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function roleUser()
    {
        return $this->hasMany('App\Models\RoleUser', 'role_id');
    }

    /**
     * Relation with User model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function user()
    {
        return $this->belongsToMany(User::class)->using(RoleUser::class);
    }
}
