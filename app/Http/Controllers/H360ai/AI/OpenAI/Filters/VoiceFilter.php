<?php
/**
 * @package VoiceFilter
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <shakib.techvill@gmail.com>
 * @created 10-08-2023
 */

namespace Modules\OpenAI\Filters;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class VoiceFilter extends Filter
{

    /**
     * Filter by userId query string
     *
     * @param  string  $data
     * @return EloquentBuilder|QueryBuilder
     */
    public function gender($data)
    {
        return $this->query->where('gender', $data);
    }
    
    /**
     * Filter by language query string
     *
     * @param  string  $name
     * @return EloquentBuilder|QueryBuilder
     */
    public function language($name)
    {
        return $this->query->where('language_code', 'like', $name . '%');
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
            $query->where('language_code', 'LIKE', $value . '%')
                ->orWhere('gender', 'LIKE', '%' . $value . '%')
                ->orWhere('name', 'LIKE', '%' . $value . '%')
                ->orWhere('status', 'LIKE', '%' . $value . '%');
        });
    }
}
