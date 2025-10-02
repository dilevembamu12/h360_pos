<?php

namespace Modules\OpenAI\DataTables;

use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Facades\DataTables;
use App\DataTables\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\OpenAI\Entities\Archive;

class CodeDataTable extends DataTable
{

    /**
     * Display ajax response
     *
     * @return JsonResponse
     */
    public function ajax(): JsonResponse
    {
        $contents = $this->query();

        return DataTables::eloquent($contents)
            ->editColumn('slug', function ($contents) {
                return '<a href="' . route('admin.features.code.view', ['slug' => $contents->slug]) . '">' . trimWords(ucfirst($contents->code_title), 60) . '</a>';
            })
            ->editColumn('user_id', function ($contents) {
                return '<a href="' . route('users.edit', ['id' => $contents->code_creator_id]) . '">' . optional($contents->codeCreator)->name . '</a>';
            })
            ->editColumn('code', function ($contents) {
                return trimWords(implode('', json_decode($contents->formated_code)), 60);
            })

            ->editColumn('model', function ($contents) {
                return isset($contents->code_model) && !empty($contents->code_model) ? ucfirst($contents->code_model) : '';
            })

            ->editColumn('created_at', function ($contents) {
                return timeZoneFormatDate($contents->created_at);
            })
            ->addColumn('action', function ($contents) {
                $html = '';
                $show = '<a title="' . __('Show') . '" href="' . route('admin.features.code.view', ['slug' => $contents->slug]) . '" class="btn btn-xs btn-outline-dark" data-toggle="modal" data-target="#exampleModalLong"><i class="feather icon-eye"></i></a>&nbsp';
                $delete = '<form method="post" action="' . route('admin.features.code.delete') . '" id="delete-code-'. $contents->id . '" accept-charset="UTF-8" class="display_inline">
                                <input type = "hidden" name = "codeId" value = '. $contents->parent_id. '>
                                ' . csrf_field() . '
                                <button title="' . __('Delete :x', ['x' => __('Code')]) . '" class="btn btn-xs btn-danger confirm-delete" type="button" data-id=' . $contents->id . ' data-label="Delete" data-delete="code" data-bs-toggle="modal" data-bs-target="#confirmDelete" data-title="' . __('Delete :x', ['x' => __('Code')]) . '" data-message="' . __('Are you sure to delete this?') . '">

                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </form>';

                if ($this->hasPermission(['Modules\OpenAI\Http\Controllers\Admin\OpenAIController@delete'])) {
                    $html .= $delete;
                }

                return $show . $html;
            })
            ->rawColumns(['code', 'slug', 'user_id', 'model', 'created_at', 'action'])
            ->make(true);
    }

    /**
     * Get query source of dataTable.
     *
     * @return QueryBuilder
     */
    public function query(): QueryBuilder
    {

        $codes = Archive::with(['metas', 'user', 'user.metas', 'codeCreator', 'codeCreator.metas'])
            ->leftJoin('archives_meta as meta_creator', function ($join) {
                $join->on('archives.id', '=', 'meta_creator.owner_id')
                    ->where('meta_creator.key', '=', 'code_creator_id');
            })
            ->leftJoin('users as creators', 'meta_creator.value', '=', 'creators.id')
            ->select([
                'archives.*',
                'creators.name as creator_name',
            ])
            ->where('archives.type', 'code_chat_reply')
            ->filter('Modules\\OpenAI\\Filters\\CodeFilter');  

        return $this->applyScopes($codes);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return HtmlBuilder
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('dataTableBuilder')
            ->minifiedAjax()
            ->selectStyleSingle()
            ->columns($this->getColumns())
            ->parameters(dataTableOptions(['dom' => 'Bfrtip']));
    }

    /**
     * Get the dataTable columns definition.
     *
     * @return array
     */
    public function getColumns(): array
    {
        return [
            new Column(['data'=> 'id', 'name' => 'id', 'title' => '', 'visible' => false, 'width' => '0%' ]),
            new Column(['data'=> 'slug', 'name' => 'metas.value', 'title' => __('Name'), 'searchable' => true, 'orderable' => false,'width' => '30%' ]),
            new Column(['data'=> 'code', 'name' => 'metas.value', 'title' => __('Code'), 'searchable' => true, 'orderable' => false, 'width' => '30%']),
            new Column(['data'=> 'model', 'name' => 'metas.value', 'title' => __('Model'), 'searchable' => true, 'orderable' => false]),
            (new Column(['data'=> 'user_id', 'name' => 'user.name', 'title' => __('Creator'), 'orderable' => true, 'searchable' => true]))->addClass('text-center'),
            (new Column(['data'=> 'created_at', 'name' => 'created_at', 'title' => __('Created At'), 'orderable' => true, 'searchable' => false]))->addClass('text-center'),
            (new Column(['data'=> 'action', 'name' => 'action', 'title' => __('Action'), 'visible' => true, 'orderable' => false, 'searchable' => false, 'width' => '10%']))->addClass('text-center'),
        ];
    }

}
