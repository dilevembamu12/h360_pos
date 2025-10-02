<?php

namespace Modules\OpenAI\Entities;

use Illuminate\Database\Eloquent\Model;

use Modules\OpenAI\Traits\MetaTrait;

class ContentType extends Model
{
    use MetaTrait;

    /**
     * Fillable
     *
     * @var array
     */
    protected $fillable = [];

    /**
     * Checks if the meta is already fetched or not
     * @var bool
     */
    protected $metaFetched = false;

    /**
     * Meta Table
     *
     * @var string
     */
    protected $metaTable = 'content_types_meta';

    /**
     * timestamps
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Relation with ContentTypeMeta model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function metadata()
    {
        return $this->hasMany(ContentTypeMeta::class, 'content_type_id', 'id');
    }

    /**
     * get meta data
     *
     * @param array $data
     * @return array
     */
    public static function getData($slug = null)
    {
        return parent::with('metadata')->where('slug', $slug)->first() ?? [];
    }

}
