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

class AiChatbotDataTable extends DataTable
{
    /**
     * Display ajax response
     *
     * @return JsonResponse
     */
    public function ajax(): JsonResponse
    {
        $bot = $this->query();

        return DataTables::eloquent($bot)
        ->editColumn('picture', function ($bot) {
            return '<img class="object-fit-cover" src="' . objectStorage()->url($bot->image['url']) . '" alt="' . __('image') . '" width="50" height="50">';
        })
        ->editColumn('name', function ($bot) {
            return ucfirst($bot->name);
        })
        ->editColumn('description', function ($bot) {
            return '<span class="cursor-pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="' . e($bot->description) . '">'
                . trimWords($bot->description, 60) .
            '</span>';
        })
        ->editColumn('total_conversation', function ($bot) {
            
            $code = $bot->code;
            $conversationIds = Archive::whereType('chatbot_chat')
                    ->whereHas('metas', function ($query) use ($code) {
                        $query->where(['key' => 'chatbot_code', 'value' => $code]);
                    })
                    ->pluck('id')
                    ->unique()
                    ->toArray();

            return count($conversationIds);
        })
        ->editColumn('total_visitors', function ($bot) {
            
            $code = $bot->code;
            $archives = Archive::with('metas')
                    ->whereType('chatbot_chat')
                    ->whereHas('metas', function ($query) use ($code) {
                        $query->where(['key' => 'chatbot_code', 'value' => $code]);
                    })
                    ->whereHas('metas', function ($query) {
                        $query->where('key', 'visitor_id');
                    })
                    ->get()
                    ->unique(function ($archive) {
                        return $archive->metas->where('key', 'visitor_id')->pluck('value');
                    });
            return $archives->count();

        })
        ->editColumn('status', function ($bot) {
            return statusBadges(lcfirst($bot->status));
        })
        ->editColumn('user_id', function ($bot) {
            return '<a href="' . route('users.edit', ['id' => $bot->user_id]) . '">' . wrapIt(optional($bot->user)->name, 10) . '</a>';
        })
        ->editColumn('created_at', function ($bot) {
            return timeZoneFormatDate($bot->created_at);
        })
        ->addColumn('action', function ($bot) {
    
            $edit = '<a data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="' . __('Edit') . '" title="' . __('Edit') . '" href="' . route('admin.features.ai_chatbot.edit', ['id' => $bot->id]) . '" class="action-icon"><i class="feather icon-edit"></i></a>&nbsp;';
            $delete = '<form method="POST" action="' . route('admin.features.ai_chatbot.delete', ['id' => $bot->id]) . '" id="delete-chatbot-' . $bot->id . '" accept-charset="UTF-8" class="display_inline">
                    ' . csrf_field() . '
                    ' . method_field('DELETE') . '
                    <a class="action-icon confirm-delete" type="button" data-id=' . $bot->id . ' data-delete="chatbot" data-label="Delete" data-bs-toggle="modal" data-bs-target="#confirmDelete" data-title="' . __("Delete :x :y", ["x" => $bot->name, "y" => __("Chatbot")]) . '" data-message="' . __('Are you sure to delete this?') . '">
                        <span data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="' . __('Delete') . '" title="' . __('Delete') . '">  
                            <i class="feather icon-trash"></i>
                        </span> 
                    </button>
                    </form>';

            return $edit . $delete;
        })
        ->rawColumns([ 'picture', 'description', 'status', 'user_id', 'action'])
        ->make(true);
    }

    /**
     * Get query source of dataTable.
     *
     * @return QueryBuilder
     */
    public function query(): QueryBuilder
    {

        $bots = ChatBot::with(['metas', 'user', 'user.metas'])
                ->select('chat_bots.name', 'chat_bots.status', 'chat_bots.id', 'chat_bots.code', 'chat_bots.type', 'chat_bots.user_id', 'chat_bots.created_at')
                ->leftJoin('chat_bots_meta as meta_description', function ($join) {
                    $join->on('chat_bots.id', '=', 'meta_description.owner_id')
                        ->where('meta_description.key', '=', 'description');
                })
                ->addSelect('meta_description.value as description')
                ->where('chat_bots.type', 'widgetChatBot');

        $bots = $bots->filter();
        return $this->applyScopes($bots);

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
            new Column(['data' => 'picture', 'name' => 'picture', 'title' => __('Picture'), 'orderable' => false, 'searchable' => false]),
            new Column(['data'=> 'name', 'name' => 'chat_bots.name', 'title' => __('Name'), 'searchable' => true, 'orderable' => true]),
            (new Column(['data'=> 'description', 'name' => 'meta_description.value', 'width' => '15%', 'title' => __('Description'), 'orderable' => true, 'searchable' => true, 'width' => '15%' ]))->addClass('text-center'),
            (new Column(['data'=> 'total_visitors', 'name' => 'total_visitors', 'title' => __('Total Visitors'), 'searchable' => false, 'orderable' => false]))->addClass('text-center'),
            (new Column(['data'=> 'total_conversation', 'name' => 'total_conversation', 'width' => '15%', 'title' => __('Total Conversations'), 'searchable' => false, 'orderable' => false]))->addClass('text-center'),
            (new Column(['data'=> 'user_id', 'name' => 'user_id', 'title' => __('Created By'), 'orderable' => true, 'searchable' => true, 'width' => '10%' ]))->addClass('text-center'),
            (new Column(['data'=> 'created_at', 'name' => 'created_at', 'title' => __('Created At'), 'orderable' => true, 'searchable' => false, 'width' => '10%' ]))->addClass('text-center'),
            (new Column(['data'=> 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => true, 'searchable' => true, 'width' => '10%' ]))->addClass('text-center'),
            new Column(['data'=> 'action', 'name' => 'action', 'title' => '', 'width' => '8%', 'visible' => true, 'orderable' => false, 'searchable' => false, 'className' => 'text-right align-middle']),
        ];
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
