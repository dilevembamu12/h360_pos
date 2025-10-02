<?php
/**
 * @package ArchiveFilter
 * @author TechVillage <support@techvill.org>
 * @contributor kabir Ahmed <kabir.techvill@gmail.com>
 * @created 25-03-2024
 */

namespace Modules\OpenAI\Filters;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ArchiveFilter extends Filter
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
     * Order the query results based on the given value.
     *
     * @param string $value The value determining the order direction. Use 'newest' for descending order.
     * @return EloquentBuilder|QueryBuilder
     */
    public function orderBy($value)
    {
        if ($value == 'newest') {
            return $this->query->orderBy('created_at', 'desc');
        } else {
            return $this->query->orderBy('created_at', 'asc');
        }
    }

    /**
     * Filter the query results based on the provided chatbot code.
     *
     * @param string $chatbot_code The chatbot code to filter by. Use 'all' to return all results.
     * @return EloquentBuilder|QueryBuilder
     */
    public function chatBot($chatbot_code)
    {
        if ($chatbot_code == 'all') {
            return $this->query;
        }

        return $this->query->where(function ($query) use ($chatbot_code) {
            $query->whereHas('metas', function($query) use ($chatbot_code) {
                $query->where(['key' => 'chatbot_code', 'value' =>  $chatbot_code]);
            });
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
            $query->whereLike('title', $value)
                ->orWhereHas('metas', function ($q) use ($value) {
                    $q->where(function ($subQuery) use ($value) {
                        $subQuery->where('key', 'visitor_id')->where('value', 'like', '%' . $value . '%');
                    });
                })
                ->orWhereHas('user', function ($q) use ($value) {
                    $q->where('name', 'like', '%' . $value . '%');
                });
        });
        
      
    }
}
