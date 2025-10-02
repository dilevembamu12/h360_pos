<?php

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;

use App\Http\Controllers\Controller;
use Modules\OpenAI\Entities\Archive;
use Modules\OpenAI\Transformers\Api\v2\History\{
    ImageHistoryDetailResource,
    HistoryResource,
    HistoryDetailResource
};
use Illuminate\Http\Resources\Json\ResourceCollection;
use Modules\OpenAI\Services\v2\ArchiveService;
use Symfony\Component\HttpFoundation\Response;

class HistoryController extends Controller
{
    /**
     * Display a listing of the archive records for the authenticated user.
     *
     * @return ResourceCollection
     */
    public function index(): ResourceCollection
    {
        $archive = Archive::with('metas')->where('user_id', auth()->id())
                    ->whereNull('parent_id')->whereIn('type', ['chat', 'url', 'file', 'image', 'vision_chat'])
                    ->orderBy('updated_at', 'desc');
        $archive = $archive->filter('Modules\OpenAI\Filters\v2\HistoryFilter')->paginate(preference('row_per_page'));

        return HistoryResource::collection($archive);
    }

    /**
     * Display the specified history details.
     *
     * @param  int  $historyId
     * @return ResourceCollection|null
     */
    public function show($historyId)
    {
        if (!is_numeric($historyId)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }
    
        $history = Archive::with(['metas', 'childs', 'user'])->find($historyId);
    
        if (!$history) {
            return response()->json(['error' => __('No data found')], Response::HTTP_NOT_FOUND);
        }
    
        $historyDetails = Archive::with(['metas', 'childs', 'user', 'chatbot'])
            ->where('parent_id', $historyId)
            ->when($history->type === 'image', function ($query) {
                $query->where('type', 'image_chat');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(preference('row_per_page'));
    
        return HistoryDetailResource::collection($historyDetails);
    }


    /**
     * Delete a specified history record.
     *
     * @param int $historyId The ID of the history record to delete.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the result of the delete operation.
     */
    public function destroy($historyId)
    {
        if (! is_numeric($historyId)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }

        $history = Archive::where('id', $historyId)->first();
        
        if (empty($history)) {
            return response()->json(['error' => __('No data found')], Response::HTTP_NOT_FOUND);
        }

        try {
            // Attempt to delete the history record
            ArchiveService::delete($history->id, $history->type);
            return response()->json(['message' =>__('The selected :x has been successfully deleted.', ['x' => __('history')])], Response::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage() ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
