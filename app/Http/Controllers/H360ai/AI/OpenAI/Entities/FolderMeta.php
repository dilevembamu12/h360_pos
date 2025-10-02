<?php

namespace Modules\OpenAI\Entities;

use Illuminate\Database\Eloquent\Model;

class FolderMeta extends Model
{

    protected $fillable = [];

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'folder_meta';

    /**
     * timestamps
     *
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Relation with Folder Model
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }
    
}
