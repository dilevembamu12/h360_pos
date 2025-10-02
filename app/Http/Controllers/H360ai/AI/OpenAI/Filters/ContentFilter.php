<?php
/**
 * @package ContentFilter
 * @author TechVillage <support@techvill.org>
 * @contributor kabir Ahmed <kabir.techvill@gmail.com>
 * @created 29-03-2023
 */

namespace Modules\OpenAI\Filters;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ContentFilter extends Filter
{

    /**
     * Filter by usecase query string
     *
     * @param  string  $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function useCase($value)
    {
        return $this->query->WhereHas('metas', function($q) use ($value) {
            $q->where('key', 'use_case_id')->Where('value', $value);
        });
    }

    /**
     * Filter by model query string
     *
     * @param  string  $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function model($value)
    {
        return $this->query->WhereHas('metas', function($q) use ($value) {
            $q->where('key', 'template_model')->Where('value', $value);
        });
    }

    /**
     * Filter by userId query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function userId($id)
    {
        return $this->query->WhereHas('metas', function($q) use ($id) {
            $q->where('key', 'template_creator_id')->Where('value', $id);
        });
    }
    
    /**
     * Filter by language query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function language($value)
    {
        return $this->query->WhereHas('metas', function($q) use ($value) {
            $q->where('key', 'template_language')->Where('value', $value);
        });

    }


    /**
     * Filter by search query string
     *
     * @param  string  $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function search($value)
    {
        $value = xss_clean($value['value']);

        return $this->query->where(function ($query) use ($value) {
            $query->whereLike('meta_model.value', $value)
                ->orWhereLike('meta_language.value', $value)
                ->orWhereLike('content', $value)
                ->orWhereHas('user', function($q) use ($value) {
                    $q->whereLike('name', $value);
                });
        });
      
    }
}
