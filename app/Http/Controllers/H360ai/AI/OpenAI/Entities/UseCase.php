<?php

/**
 * @package UseCase
 * @author TechVillage <support@techvill.org>
 * @contributor Soumik Datta <soumik.techvill@gmail.com>
 * @contributor AH Millat <millat.techvill@gmail.com>
 * @created 22-02-2023
 * @modified 13-03-2023
 */

namespace Modules\OpenAI\Entities;

use App\Models\Model;
use App\Traits\ModelTrait;
use App\Traits\ModelTraits\{Filterable, hasFiles};
use Modules\MediaManager\Http\Models\ObjectFile;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
Use Modules\OpenAI\Entities\Option;

class UseCase extends Model
{
    use ModelTrait, hasFiles, Filterable;

    /**
     * Table
     *
     * @var string
     */
    protected $table = 'use_cases';

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
    protected $fillable = ['name', 'description', 'slug', 'status', 'prompt', 'creator_type', 'creator_id'];

    /**
     * Relation with UseCaseCategory Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function useCaseCategories(): BelongsToMany
    {
        return $this->belongsToMany(UseCaseCategory::class, 'use_case_use_case_category', 'use_case_id', 'use_case_category_id');
    }

    /**
     * Clear related model data
     *
     * @param  UseCase  $useCase
     */
    public static function clearFootprints(UseCase $useCase): void
    {
        $useCase->useCaseCategories()->sync([]);
        ObjectFile::where('object_type', '=', 'use_cases')->where('object_id', $useCase->id)->delete();
    }

    /**
     * Get all use cases
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAll()
    {
        return self::where('status', 'Active')->get();
    }


    /**
     * get use case by slug
     *
     * @param mixed $name
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function bySlug($name)
    {
        return self::whereSlug($name)->first();
    }

    /**
     * Relation with Option Model
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function option()
    {
        return $this->hasMany(Option::class, 'use_case_id');
    }

    /**
     * Relation with object File
     * @return [type]
     */
    public function objectImage()
    {
        return $this->hasOne('Modules\MediaManager\Http\Models\ObjectFile', 'object_id')->where('object_type', 'use_cases');
    }

    /**
     * Relation with User Model
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'creator_id');
    }

    /**
     * count use case
     *
     * @return int
     */
    public static function useCaseCount()
    {
        return UseCase::where('status', 'active')->count();
    }

    /**
     * show counted use case
     *
     * @return string|int
     */
    public function showUseCaseCount()
    {
        $useCase = $this->useCaseCount();
        return $useCase > 0 ? $useCase - 1 . '+' : 0;
    }
}
