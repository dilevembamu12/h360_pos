<?php

namespace Modules\OpenAI\DataTables;

use App\DataTables\DataTable;
use Yajra\DataTables\Html\Column;
use Illuminate\Http\JsonResponse;
use Modules\OpenAI\Entities\Archive;
use Modules\OpenAI\Entities\ChatBot;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;

class ImageToVideoDataTable extends DataTable
{
    /**
     * Display ajax response
     *
     * @return JsonResponse
     */
    public function ajax(): JsonResponse
    {
        $video = $this->query();

        return DataTables::eloquent($video)
            ->addColumn('image', function ($video) {
                $url = $video->videoUrl();
                $imageUrl = objectStorage()->url("public/uploads/aiVideos/{$video->uploaded_file_name}");
                return sprintf(
                    '<a href="%s" target="_blank"><img class="data-table-image" src="%s" alt="Video Thumbnail"></a>',
                    htmlspecialchars($url, ENT_QUOTES, 'UTF-8'),
                    htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8')
                );
                
            })
            ->editColumn('name', function ($video) {
                return trimWords($video->title, 40);
            })
            ->editColumn('user_id', function ($video) {
                return '<a href="' . route('users.edit', ['id' => $video->video_creator_id]) . '">' . optional($video->imageToVideoCreator)->name . '</a>';
            })

            ->editColumn('created_at', function ($video) {
                return timeZoneFormatDate($video->created_at);
            })
            ->addColumn('action', function ($video) {
                $downlaod = '<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="' . __('Download') . '" title="' . __('Download') . '" href="' . $video->videoUrl() . '" download="'.  str_replace('.', '', $video->title) .'" class="action-icon"><i class="feather icon-download"></i></a>&nbsp;';
                $delete = '<form method="POST" action="' . route('admin.features.image-to-video.delete', ['id' => $video->id]) . '" id="delete-video-' . $video->id . '" accept-charset="UTF-8" class="display_inline">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <a class="action-icon confirm-delete" type="button" data-id=' . $video->id . ' data-delete="video" data-label="Delete" data-bs-toggle="modal" data-bs-target="#confirmDelete" data-title="' . __('Delete :x', ['x' => __('Video')]) . '" data-message="' . __('Are you sure to delete this?') . '">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="' . __('Delete') . '" title="' . __('Delete') . '">  
                            <i class="feather icon-trash"></i>
                        </span> 
                    </button>
                    </form>';

                return $downlaod . $delete;

            })
            ->rawColumns(['image', 'user_id', 'name', 'created_at', 'action'])
            ->make(true);
    }

    /**
     * Get query source of dataTable.
     *
     * @return QueryBuilder
     */
    public function query(): QueryBuilder
    {

        $videos = Archive::with('metas', 'imageToVideoCreator', 'imageToVideoCreator.metas') // Eager load relationships
            ->leftJoin('archives_meta as meta_creator', function ($join) {
                $join->on('archives.id', '=', 'meta_creator.owner_id')
                    ->where('meta_creator.key', '=', 'video_creator_id');
            })
            ->leftJoin('users as creators', 'meta_creator.value', '=', 'creators.id')
            ->select([
                'archives.*',
                'creators.name as creator_name',
            ])
            ->where('archives.type', 'video')
            ->filter('Modules\OpenAI\Filters\v2\VideoFilter');

        return $this->applyScopes($videos);

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
            new Column(['data'=> 'title', 'name' => 'title', 'title' => __('Name'), 'searchable' => true, 'orderable' => true, 'width'=>'40%']),
            (new Column(['data'=> 'user_id', 'name' => 'creators.name', 'title' => __('Creator'), 'orderable' => true, 'searchable' => true, 'width'=>'20%']))->addClass('text-center'),
            (new Column(['data'=> 'created_at', 'name' => 'created_at', 'title' => __('Created At'), 'orderable' => true, 'searchable' => false, 'width'=>'10%']))->addClass('text-center'),
            new Column(['data'=> 'action', 'name' => 'action', 'title' => '', 'width' => '8%', 'visible' => true, 'orderable' => false, 'searchable' => false, 'className' => 'text-right align-middle'])
        ];
    }

}
