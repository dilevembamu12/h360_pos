<?php

namespace Modules\OpenAI\Entities;

use App\Models\Model;
use App\Traits\ModelTraits\hasFiles;

class EmbededVector extends Model
{
    use hasFiles;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'embeded_vectors';
}
