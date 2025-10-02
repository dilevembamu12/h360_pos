<?php
/**
 * @package ContentFilter
 * @author TechVillage <support@techvill.org>
 * @contributor kabir Ahmed <kabir.techvill@gmail.com>
 * @created 29-03-2023
 */

namespace Modules\OpenAI\Filters\v2;

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
        return $this->query
            ->join('archives_meta as meta_user', function ($join) use ($id) {
                $join->on('archives.id', '=', 'meta_user.owner_id')
                    ->where('meta_user.key', '=', 'image_creator_id')
                    ->where('meta_user.value', $id);
            })
            ->select('archives.*')
            ->distinct();
    }
    
    /**
     * Filter by language query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function size($size)
    {
        return $this->query
            ->join('archives_meta as meta_size', function ($join) use ($size) {
                $join->on('archives.id', '=', 'meta_size.owner_id')
                    ->where('meta_size.key', '=', 'generation_options')
                    ->where(\DB::raw("JSON_UNQUOTE(JSON_EXTRACT(meta_size.value, '$.size'))"), 'LIKE', '%' . $size . '%');
            })
            ->select('archives.*')
            ->distinct();
    }

    /**
     * Order the query results based on the given value.
     *
     * @param string $value The value determining the order direction. Use 'newest' for descending order.
     * @return EloquentBuilder|QueryBuilder
     */
    public function orderBy($value)
    {
        if ($value == 'oldest') {
            return $this->query->orderBy('archives.created_at', 'asc');
        } else {
            return $this->query->orderBy('archives.created_at', 'desc');
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
            $query->where('title', 'LIKE', '%' . $value . '%')
            ->orWhere('creators.name', 'LIKE', $value);
        });
      
    }
}
