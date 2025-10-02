<?php

namespace App\Http\Controllers\H360ai\AI\OpenAI\Entities;

use App\Http\Controllers\H360ai\AI\app\Models\Model;
use App\Http\Controllers\H360ai\AI\OpenAI\Traits\ModelTraits\hasFiles;
use App\Http\Controllers\H360ai\AI\OpenAI\Traits\ModelTraits\{
   
    Metable,
    Filterable
};


class EmbededResource extends Model
{
    //use hasFiles, Metable, Filterable; // Using the traits for handling files and meta data

    protected $connection ="h360ai_mysql";

    /**
     * The table associated with the model.
     *  
     * @var string
     */
    protected $table = 'embeded_resources';

    /**
     * The table associated with the model's meta data.
     *
     * @var string
     */
    protected $metaTable = 'embeded_resources_meta';

    /**
     * Define a relation with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Http\Controllers\H360ai\AI\app\Models\User', 'user_id');
    }

    /**
     * Define a relation with child EmbededResource models.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function childs()
    {
        return $this->hasMany('Modules\OpenAI\Entities\EmbededResource', 'parent_id')->with(['user', 'metas', 'childs']);
    }
}
