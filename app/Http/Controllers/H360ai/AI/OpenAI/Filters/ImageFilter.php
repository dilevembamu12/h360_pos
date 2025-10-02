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

class ImageFilter extends Filter
{

    /**
     * Filter by userId query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function userId($id)
    {
        return $this->query->where('user_id', $id);
    }
    
    /**
     * Filter by language query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function size($id)
    {
        return $this->query->where('size', $id);
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
            $query->whereLike('images.name', $value)
                ->orWhereLike('size', $value)
                ->orWhereHas('user', function($q) use ($value) {
                    $q->whereLike('name', $value);
                });
        });
      
    }
}
