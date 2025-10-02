<?php
/**
 * @package ContentFilter
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <kabir.techvill@gmail.com>
 * @created 14-01-2025
 */

namespace Modules\OpenAI\Filters\v2;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class VideoFilter extends Filter
{

    /**
     * Filter by userId query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function userId($id)
    {
        return $this->query->WhereHas('metas', function($q) use ($id) {
            $q->where('key', 'video_creator_id')->where('value', $id);
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
            $query->where('title', 'LIKE', '%' . $value . '%')
            ->orWhere('creators.name', 'LIKE', $value);;
        });
      
    }
}
