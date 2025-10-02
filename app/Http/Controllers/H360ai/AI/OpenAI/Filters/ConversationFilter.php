<?php
/**
 * @package ConversationFilter
 * @author TechVillage <support@techvill.org>
 * @contributor kabir Ahmed <kabir.techvill@gmail.com>
 * @created 14-07-2024
 */

namespace Modules\OpenAI\Filters;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ConversationFilter extends Filter
{
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
            $query->OrWhereHas('metas', function($q) use ($value) {
                $q->where('key', 'chatbot_id')->Where('value', 'LIKE', "%$value%");
            })
            ->OrWhereHas('metas', function($q) use ($value) {
                $q->where('key', 'visitor_id')->Where('value', 'LIKE', "%$value%");
            });
        });
    }
}
