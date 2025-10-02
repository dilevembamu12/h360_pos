<?php
/**
 * @package ChatBotFilter
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <shakib.techvill@gmail.com>
 * @created 29-07-2023
 */

namespace Modules\OpenAI\Filters;

use App\Filters\Filter;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class ChatBotFilter extends Filter
{
   /**
     * Filter by usecase query string
     *
     * @param string $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function chatCategory($value)
    {
        return $this->query->where('chat_bots.chat_category_id', $value);
    }

    /**
     * Filter by status query string
     *
     * @param string $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function status($value)
    {
        return $this->query->where('chat_bots.status', $value);
    }

    /**
     * Filter by userId query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function userId($id)
    {
        return $this->query->where('chat_bots.user_id', $id);
    }

    /**
     * Sort the query results by the specified column and order.
     *
     * @param string $value The column to sort by
     * @return EloquentBuilder|QueryBuilder The query builder instance
     */
    public function sortBy($value)
    {
        if ($value == 'name') {
            return $this->query->orderBy($value, 'asc');
        }

        return $this->query->orderBy('created_at', $value);
    }

    /**
     * Filter by search query string
     *
     * @param  string|array  $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function search($value)
    {
        $value = gettype($value) == 'array' ? $value['value'] : $value;
        $value = xss_clean($value);

        return $this->query->where(function ($query) use ($value) {
            $query->whereLike('status', $value)
                ->orWhereLike('chat_bots.name', $value)
                ->orWhereLike('message', $value)
                ->orWhereHas('chatCategory', function($q) use ($value) {
                    $q->whereLike('name', $value);
                });
        });

    }
}
