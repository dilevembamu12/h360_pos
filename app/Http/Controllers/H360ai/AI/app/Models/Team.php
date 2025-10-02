<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\Validator;

use App\Models\{User, TeamMemberMeta};

class Team extends Model
{
    use HasFactory;

    /**
     * Relation with user model
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation with user model as parent
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Relation with TeamMemberMeta
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function memberMeta()
    {
        return $this->hasMany(TeamMemberMeta::class)->where('category', 'usage');
    }

    /**
     * Is team member 
     *
     * @param  int  $id
     * @return Illuminate\Database\Eloquent\Model
     */
    public static function getMember($id)
    {
        return parent::where(['user_id' => $id, 'status' => 'Active'])->first();
    }

    /**
     * Store team member
     * @param array $data
     * @return boolean
     */
    public function store($data = [])
    {
    	if ($id = parent::insertGetId($data)) {
    		return $id;
    	}
        return false;
    }


    /**
     * Member session data
     * @return Illuminate\Database\Eloquent\Model|false
     */
    public static function memberSession()
    {
        $userTeam =  parent::getMember(auth()->user()->id);
        if (!empty($userTeam)) {
            return TeamMemberMeta::getMemberMeta($userTeam->id, 'packageUserId');
        } else {
            return false;
        }
    }

    /**
     * Update Team member aactive status
     *
     * @param array $data
     * @param int $id
     * @return boolean
     */
    public function updatTeamStatus($data = [], $id = null)
    {
        return  parent::where('id', $id)
        ->update([
            'status' => $data['status']
        ]);
    }

    /**
     * Update team validation
     *
     * @param array $data
     * @return mixed
     */
    protected static function updateTeamValidation($data = [])
    {
        $validator = Validator::make($data, [
            'team_id' => 'required|numeric',
            'name'    => 'min:3|max:191',
            'status'  => 'in:Active,Inactive,Deleted',
        ]);

        return $validator;
    }
}
