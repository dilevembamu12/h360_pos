<?php

namespace Modules\OpenAI\Entities;

use App\Models\Model;

class EmbededUser extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'embeded_users';

    /**
     * Define a relation with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        // Defines a "belongsTo" relationship with the User model, using 'user_id' as the foreign key
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
