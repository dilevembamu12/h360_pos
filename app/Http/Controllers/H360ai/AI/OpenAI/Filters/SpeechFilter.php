<?php
/**
 * @package SpeechFilter
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <shakib.techvill@gmail.com>
 * @created 23-07-2023
 */

namespace Modules\OpenAI\Filters;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class SpeechFilter extends Filter
{

    /**
     * Filter by userId query string
     *
     * @param string $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function userId(string $value): EloquentBuilder|QueryBuilder
    {
        return $this->query->whereHas('metas', function($q) use ($value) {
            $q->where('key', 'speech_to_text_creator_id')->where('value', $value);
        });
    }
    
    /**
     * Filter by language query string
     *
     * @param  string  $name
     * @return EloquentBuilder|QueryBuilder
     */
    public function language(string $name): EloquentBuilder|QueryBuilder
    {
        return $this->query->whereHas('metas', function($q) use ($name) {
            $q->where('key', 'language')->where('value', $name);
        });
    }


    /**
     * Filter by search query string
     *
     * @param $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function search($value)
    {
        // Ensure relevant columns are indexed in the database

        $value = xss_clean($value['value']);
        $likeValue = '%' . $value . '%';

        return $this->query->where(function ($query) use ($likeValue) {
            $query->where('archives.title', 'LIKE', $likeValue)
                ->orWhere('meta_language.value', 'LIKE', $likeValue)
                ->orWhere('creators.name', 'LIKE', $likeValue);
        });

    }
}
