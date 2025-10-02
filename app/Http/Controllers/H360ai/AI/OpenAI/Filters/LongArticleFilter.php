<?php
/**
 * @package ContentFilter
 * @author TechVillage <support@techvill.org>
 * @contributor Ahammed Imtiaze <imtiaze.techvill@gmail.com>
 * @created 30-01-2024
 */

namespace Modules\OpenAI\Filters;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class LongArticleFilter extends Filter
{
    /**
     * Filter by model query string
     *
     * @param  string  $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function model($value)
    {
       $this->query->whereHas('metas', function($q) use ($value) {
            $q->where('key', 'long_article_generate_model')
              ->where('value', $value);
       });
    }

    /**
     * Filter by userId query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function user($id)
    {
        return $this->query->where('user_id', $id);
    }

    public function provider($value)
    {
        return $this->query->where('provider', $value);
    }

    /**
     * filter status  query string
     *
     * @param string $status
     * @return query builder
     */
    public function status($status)
    {
        return $this->query->where('status', $status);
    }

    /**
     * Filter by search query string
     *
     * @param  string  $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function search($value)
    {
        if (gettype($value) == 'string') {
            $value = xss_clean($value);
        } else if (gettype($value) == 'array') {
            $value = xss_clean($value['value']);
        }
        
        return $this->query->where(function ($query) use ($value) {
            $query->where('title', 'LIKE', '%' . $value . '%')
                ->OrWhere('content', 'LIKE', '%' . $value . '%')
                ->OrWhereHas('user', function($userQuery) use ($value) {
                    $userQuery->where('name', 'LIKE', '%' . $value . '%');
                })
                ->OrWhereHas('metas', function($q) use ($value) {
                    $q->where('key', 'long_article_generate_model')->orWhere('value', 'LIKE', "%$value%");
                });
        });
    }
}
