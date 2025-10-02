<?php
/**
 * @package ContentFilter
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <shakib.techvill@gmail.com>
 * @created 02-11-2024
 */

namespace Modules\OpenAI\Filters\v2;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class TemplateFilter extends Filter
{
    /**
     * Order the query results based on the given value.
     *
     * @param string $value The value determining the order direction. Use 'newest' for descending order.
     * @return EloquentBuilder|QueryBuilder
     */
    public function orderBy($value)
    {
        if ($value == 'oldest') {
            return $this->query->orderBy('id', 'asc');
        } else {
            return $this->query->orderBy('id', 'desc');
        }
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
            $query->where('provider', 'like', "%$value%")
                ->orWhere(function($subQuery) use ($value) {
                    $subQuery->whereHas('metas', function($q) use ($value) {
                            $q->where('key', 'template_title')
                            ->where('value', 'like', "%$value%");
                        })
                        ->orWhereHas('user', function($q) use ($value) {
                            $q->where('name', 'like', "%$value%");
                        });
                });
        });
      
    }
}
