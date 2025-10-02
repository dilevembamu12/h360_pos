<?php

namespace Modules\OpenAI\DataTables;

use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Facades\DataTables;
use Modules\OpenAI\Entities\Archive;
use Illuminate\Http\JsonResponse;
use App\DataTables\DataTable;

class LongArticlesDataTable extends DataTable
{
    /*
    * DataTable Ajax
    *
    * @return JsonResponse
    */
    public function ajax(): JsonResponse
    {
        $longArticle = $this->query();

        return DataTables::eloquent($longArticle)
            ->editColumn('title', function ($longArticle) {
                return trimWords($longArticle->article_title ?? $longArticle->where('parent_id', $longArticle->id)->latest()->first()?->title_topic, 30);
            })
            ->editColumn('content', function ($longArticle) {
                return trimWords(strip_tags($longArticle->filtered_content ?? preg_replace('/\*\*(.*?)\*\*/', '<br><br><h1 class="text-color-14 text-24 font-semibold font-RedHat dark:text-white">$1</h1>', $longArticle->content ?? '-')), 35);
            })
            ->editColumn('user_id', function ($longArticle) {
                return '<a href="' . route('users.edit', ['id' => $longArticle->user_id]) . '">' . $longArticle->user?->name. '</a>';
            })
            ->editColumn('provider', function ($longArticle) {
                return $longArticle->provider;
            })
            ->addColumn('model', function ($longArticle) {
                return $longArticle->generation_options['model'] ?? '';
            })
            ->editColumn('created_at', function ($longArticle) {
                return $longArticle->format_created_at_only_date . '<br><span class="text-muted">' . $longArticle->format_created_at_only_time . '</span>';
            })
            ->editColumn('status', function ($longArticle) {
                return statusBadges(lcfirst($longArticle->status));
            })
            ->addColumn('action', function ($longArticle) {
                
                $edit = '<a title="' . __('Edit') . '" href="' . route('admin.long_article.edit', ['id' => $longArticle->id]) . '" class="action-icon"><i class="feather icon-edit"></i></a>&nbsp;';
                $delete = '<form method="post" action="' . route('admin.long_article.destroy', ['id' => $longArticle->id]) . '" id="delete-longarticle-' . $longArticle->id . '" accept-charset="UTF-8" class="display_inline">
                        ' . csrf_field() . '
                        <input name="_method" value="delete" type="hidden">
                        <a title="' . __('Delete') . '" class="action-icon confirm-delete" type="button" data-id=' . $longArticle->id . ' data-delete="longarticle" data-label="Delete" data-bs-toggle="modal" data-bs-target="#confirmDelete" data-title="' . __('Delete :x', ['x' => __('Long Article')]) . '" data-message="' . __('Are you sure to delete this?') . '">
                        <i class="feather icon-trash"></i>
                        </a>
                        </form>';
                $str = '';

                if ($this->hasPermission(['Modules\OpenAI\Http\Controllers\Admin\LongArticleController@edit']) && $longArticle->status == 'Completed') {
                    $str .= $edit;
                }

                if ($this->hasPermission(['Modules\OpenAI\Http\Controllers\Admin\LongArticleController@destroy'])) {
                    $str .= $delete;
                }

                return $str;
            })
            ->rawColumns(['picture', 'user_id', 'role', 'content', 'status', 'created_at', 'action'])
            ->make(true);
    }

    /*
    * DataTable Query
    *
    * @return QueryBuilder
    */
    public function query(): QueryBuilder
    {
        $longArticles = Archive::with(['metas', 'user:id,name', 'user.metas'])->select('archives.id', 'archives.title', 'archives.content', 'archives.user_id', 'archives.provider', 'archives.status', 'archives.created_at')->whereType('long_article')->filter('Modules\\OpenAI\\Filters\\LongArticleFilter');

        return $this->applyScopes($longArticles);
    }

    /*
    * DataTable HTML
    *
    * @return HtmlBuilder
    */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->addColumn(['data' => 'id', 'name' => 'id', 'title' => __('Id'), 'visible' => false, 'width' => '0%'])
            ->addColumn(['data' => 'title', 'name' => 'title', 'title' => __('Title'), 'width' => '20%'])
            ->addColumn(['data' => 'content', 'name' => 'content', 'title' => __('Content'), 'className' => 'align-middle'])
            ->addColumn(['data' => 'user_id', 'name' => 'user.name', 'title' => __('Generator')])
            ->addColumn(['data' => 'provider', 'name' => 'provider', 'title' => __('Provider'), 'className' => 'align-middle'])
            ->addColumn(['data' => 'model', 'name' => 'metas.value', 'title' => __('Model'), 'className' => 'align-middle', 'sortable' => false])
            ->addColumn(['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'className' => 'align-middle'])
            ->addColumn(['data' => 'created_at', 'name' => 'created_at', 'title' => __('Generated'), 'className' => 'align-middle', 'width' => '11%'])
            ->addColumn([
                'data' => 'action', 'name' => 'action', 'title' => '', 'width' => '12%',
                'visible' => $this->hasPermission(['Modules\OpenAI\Http\Controllers\Admin\LongArticleController@edit', 'Modules\OpenAI\Http\Controllers\Admin\LongArticleController@destroy']),
                'orderable' => false, 'searchable' => false, 'className' => 'text-right align-middle',
            ])
            ->parameters(dataTableOptions(['dom' => 'Bfrtip']));
    }

    public function setViewData()
    {
        $statusCounts = $this->query()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $this->data['groups'] = ['All' => $statusCounts->sum()] + $statusCounts->toArray();
    }
}
