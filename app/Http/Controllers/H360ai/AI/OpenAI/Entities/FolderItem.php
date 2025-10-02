<?php

namespace Modules\OpenAI\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FolderItem extends Model
{
    use HasFactory;

    protected $fillable = ['folder_id', 'item_id', 'item_type'];
    
    protected $table = "folder_items" ;

    /**
     * Relation with Folder Model
     */
    public function folder()
    {
        return $this->belongsTo(Folder::class, 'folder_id');
    }
}
