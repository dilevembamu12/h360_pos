<?php

namespace Modules\OpenAI\DataTables\v2;

use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Facades\DataTables;
use App\DataTables\DataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Modules\OpenAI\Entities\Archive;

class ImageDataTable extends DataTable
{
    /**
     * Display ajax response
     *
     * @return JsonResponse
     */
    public function ajax(): JsonResponse
    {
        $image = $this->query();

        return DataTables::eloquent($image)
            ->addColumn('image', function ($image) {
                return '<img src="' .  $image->imageUrl(['thumbnill' => true, 'size' => 'small'])  . '" alt="' . __('image') . '" class ="data-table-image">';
            })
            ->editColumn('name', function ($image) {
                return trimWords($image->title, 40);
            })
            ->editColumn('image_creator_id', function ($image) {
                return '<a href="' . route('users.edit', ['id' => $image->image_creator_id]) . '">' . wrapIt(optional($image->imageCreator)->name, 10) . '</a>';
            })
            ->editColumn('size', function ($image) {
                $options = json_decode($image->generation_options);
                return $options->size;
            })

            ->editColumn('created_at', function ($image) {
                return timeZoneFormatDate($image->created_at);
            })
            ->addColumn('action', function ($image) {
                $html = '';
                $show = '<a title="' . __('Download') . '" href="' . $image->imageUrl() . '" download="'.  $image->title .'" class="btn btn-xs btn-outline-dark"><i class="feather icon-download"></i></a>&nbsp';
                $delete ='<form method="post" action="' . route('admin.features.admin.image.destroy', ['id' => $image->id]) . '" id="delete-image-'. $image->id . '" accept-charset="UTF-8" class="display_inline">
                                ' . csrf_field() . '
                                <input name="_method" value="delete" type="hidden">
                                <button title="' . __('Delete :x', ['x' => __('Image')]) . '" class="btn btn-xs btn-danger confirm-delete" type="button" data-id=' . $image->id . ' data-label="Delete" data-delete="image" data-bs-toggle="modal" data-bs-target="#confirmDelete" data-title="' . __('Delete :x', ['x' => __('Image')]) . '" data-message="' . __('Are you sure to delete this?') . '">
                                    <i class="feather icon-trash-2"></i>
                                </button>
                            </form>';

                    $html .= $delete . $show;

                return $html;
            })
            ->rawColumns(['image', 'image_creator_id', 'name', 'size', 'created_at', 'action'])
            ->make(true);
    }

    /**
     * Get query source of dataTable.
     *
     * @return QueryBuilder
     */
    public function query(): QueryBuilder
    {
        $images = Archive::query()->where('archives.type', 'image_variant')
        ->leftJoin('archives_meta as meta_creator', function ($join) {
            $join->on('archives.id', '=', 'meta_creator.owner_id')
                ->where('meta_creator.key', '=', 'image_creator_id')
                ->where('meta_creator.key', '=', 'generation_options');
        })
        ->leftJoin('users as creators', 'meta_creator.value', '=', 'creators.id')
        ->with([
            'metas' => function ($query) {
            $query->select('owner_id', 'key', 'value');
        }, 'imageCreator.metas'])
        ->select([
            'archives.id',
            'archives.title'
        ])
        ->filter('Modules\\OpenAI\\Filters\\v2\\ImageFilter');

        return $this->applyScopes($images);
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
            new Column(['data'=> 'image', 'name' => 'metas.url', 'title' => __('Image'), 'orderable' => false, 'searchable' => false]),
            new Column(['data'=> 'title', 'name' => 'title', 'title' => __('Name'), 'searchable' => true, 'orderable' => true]),
            (new Column(['data'=> 'image_creator_id', 'name' => 'creators.name', 'title' => __('Creator'), 'orderable' => true, 'searchable' => true]))->addClass('text-center'),
            (new Column(['data'=> 'size', 'name' => 'size', 'title' => __('Size'), 'orderable' => false, 'searchable' => false]))->addClass('text-center'),
            (new Column(['data'=> 'created_at', 'name' => 'archives.created_at', 'title' => __('Created At'), 'orderable' => true, 'searchable' => false]))->addClass('text-center'),
            new Column(['data'=> 'action', 'name' => 'action', 'title' => __('Action'), 'visible' => true, 'orderable' => false, 'searchable' => false])
        ];
    }

}
