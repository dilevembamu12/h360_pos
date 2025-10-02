<?php
/**
 * @package EmbedResourcetFilter
 * @author TechVillage <support@techvill.org>
 * @contributor kabir Ahmed <kabir.techvill@gmail.com>
 * @created 20-03-2024
 */

namespace Modules\OpenAI\Filters;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class EmbededResourceFilter extends Filter
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

    public function type($value)
    {
        return $this->query->where('type', $value);
    }

    public function state($value)
    {
        return $this->query->whereHas('metas', function ($query) use ($value) {
            $query->where('key', 'state')->where('value', $value);
        });
    }

    /**
     * Filter by provider query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function provider($value)
    {
        return $this->query->whereHas('metas', function($query) use ($value) {
            $query->where('key', 'embedding_provider')
                  ->where('value', 'like', "%{$value}%");
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
        $value = gettype($value) == 'array' ? $value['value'] : $value;
        $value = xss_clean($value);

        return $this->query->where(function ($query) use ($value) {
            $query->where(function ($query) use ($value) {
                    $query->where('original_name', 'like', '%' . $value . '%')
                          ->orWhere('type', 'like', '%' . $value . '%');
                })
                ->orWhere(function ($query) use ($value) {
                    $query->whereHas('metas', function ($query) use ($value) {
                            $query->where('key', 'state')->where('value', 'like', '%' . $value . '%');
                        })
                        ->orWhereHas('user', function ($query) use ($value) {
                            $query->where('name', 'like', '%' . $value . '%');
                        });
                });
        });
        
      
    }
}
