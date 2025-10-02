<?php

namespace Modules\OpenAI\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\OpenAI\Traits\FolderTrait;

class Folder extends Model
{
    use FolderTrait;

    /**
     * Fillable
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'user_id',
        'name',
        'slug'
    ];

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'folders';

    /**
     * Relation with User Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * Relation with Folder Item model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function folderItem()
    {
        return $this->hasMany(FolderItem::class);
    }

    /**
     * Relation with Folder Meta model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function metaData()
    {
        return $this->hasMany(FolderMeta::class);
    }
}
