<?php
/**
 * @package UseCaseFilter
 * @author TechVillage <support@techvill.org>
 * @contributor Soumik Datta <soumik.techvill@gmail.com>
 * @created 02-03-2023
 */

namespace Modules\OpenAI\Filters;

use App\Filters\Filter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class UseCaseFilter extends Filter
{
    /**
     * Auth User Type
     *
     * @var string
     */
    protected string $authUserType = 'guest';

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->authUserType = strtolower(Auth::user()->role()->type);
    }

    /**
     * Filter by status query string
     *
     * @param  string  $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function status($value)
    {
        if ($this->authUserType == 'admin') {
            return $this->query->where('status', $value);
        }

        return $this->query;
    }

    /**
     * Filter by useCaseId query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function useCaseId($id)
    {
        if ($this->authUserType == 'admin') {
            return $this->query->where('id', $id);
        }

        return $this->query;
    }

    /**
     * Filter by category_id query string
     *
     * @param  string  $id
     * @return EloquentBuilder|QueryBuilder
     */
    public function categoryId($id)
    {
        if (is_numeric($id) && $id > 0) {
            $this->query->whereHas('useCaseCategories', function ($query) use ($id) {
                $query->where('id', $id);
            });
        }

        return $this->query;
    }

    /**
     * Filter by is_favorites query string
     *
     * @param  string|boolean  $value
     * @return EloquentBuilder|QueryBuilder
     */
    public function isFavorites($value)
    {
        if ($value == 'true') {
            $this->query ->whereIn('id', auth()->user()->use_case_favorites);
        }

        return $this->query;
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
        if (gettype($value) == 'string') {
            $value = xss_clean($value);
        } else if (gettype($value) == 'array') {
            $value = xss_clean($value['value']);
        }

        return $this->query->where(function ($query) use ($value) {
            $query->where('name', 'LIKE', '%' . $value . '%')
                ->OrWhere('description', 'LIKE', '%' . $value . '%')
                ->orWhereHas('user', function ($query) use ($value) {
                    $query->where('name', 'LIKE', '%' . $value . '%');
                });
        });
    }
}
