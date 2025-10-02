<?php

/**
 * @package UseCaseCategory
 * @author TechVillage <support@techvill.org>
 * @contributor Soumik Datta <soumik.techvill@gmail.com>
 * @created 22-02-2023
 */

namespace Modules\OpenAI\Entities;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UseCaseCategory extends Model
{
    use ModelTrait;

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'use_case_categories';

    /**
     * Hidden
     *
     * @var array
     */
    protected $hidden = ['pivot'];

    /**
     * Fillable
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'description', 'slug'];

    /**
     * The use cases that belong to the use case category.
     */
    public function useCases(): BelongsToMany
    {
        return $this->belongsToMany(UseCase::class, 'use_case_use_case_category', 'use_case_category_id', 'use_case_id');
    }
}
