<?php
/**
 * @package AudioFilter
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <shakib.techvill@gmail.com>
 * @created 31-08-2023
 */

namespace Modules\OpenAI\Filters;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class AudioFilter extends Filter
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
     * Filter by userId query string
     *
     * @param  string  $name
     * @return EloquentBuilder|QueryBuilder
     */
    public function language($name)
    {
        return $this->query->where('language', $name);
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
            $query->where('prompt', 'like', '%'. $value . '%')
                ->orWhere('language', 'like', '%'. $value . '%')
                ->orWhere('gender', 'like', '%'. $value . '%')
                ->orWhereHas('user', function($query) use ($value) {
                    $query->whereLike('name', $value);
                });
        });
      
    }
}
