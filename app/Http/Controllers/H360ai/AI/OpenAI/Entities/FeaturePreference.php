<?php

namespace Modules\OpenAI\Entities;

use App\Traits\ModelTrait;
use App\Traits\ModelTraits\Metable;
use App\Traits\ModelTraits\Filterable;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ModelTraits\hasFiles;

class FeaturePreference extends Model
{
    use Metable;
    use ModelTrait;
    use Filterable;
    use hasFiles;

    /**
     * The table associated with the model's meta data.
     *
     * @var string
     */
    protected $metaTable = 'feature_preference_metas';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

}
