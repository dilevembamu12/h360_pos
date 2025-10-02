<?php
/**
 * @author TechVillage <support@techvill.org>
 *
 * @contributor Kabir Ahmed <[kabir.techvill@gmail.com]>
 *
 * @created 26-01-2024
 */

namespace Modules\OpenAI\Http\Controllers\Api\v2\User;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Modules\OpenAI\Entities\Archive;
use Modules\OpenAI\Services\v2\ArchiveService;
use Modules\OpenAI\Services\v2\TemplateService;
use Modules\OpenAI\Http\Requests\v2\TemplateRequest;
use Modules\OpenAI\Http\Resources\v2\TemplateResource;
use Modules\OpenAI\Http\Requests\v2\ToggleBookmarkRequest;
use Modules\OpenAI\Http\Requests\v2\TemplateUpdateRequest;


class TemplateController extends Controller
{
    /**
     * The instance of the code service.
     *
     * @var TemplateService
     */
    protected $templateService;

    /**
     * Constructor method.
     *
     * Instantiates the class and sets up the vector service.
     *
     * @param  TemplateService  $templateService
     * 
     * @return void
     */
    public function __construct(TemplateService $templateService)
    {
        $this->templateService = $templateService;
    }

    /**
     * Returns a paginated list of template resources.
     * 
     * @param Request $request
     * 
     * @return TemplateResource|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request): TemplateResource|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $templates = (new Archive())
            ->with(['metas', 'user', 'useCase:id,name', 'templateCreator'])
            ->whereHas('metas', function ($q) {
                $q->where('key', 'template_creator_id')->where('value', auth()->id());
            })
            ->where('type', 'template')
            ->whereNull('user_id')
            ->filter('Modules\OpenAI\Filters\v2\TemplateFilter')->paginate(preference('row_per_page'));
        return TemplateResource::collection($templates);
    }

    /**
     * Generate template data based on the provided request.
     *
     *
     * @param  TemplateRequest  $request The request containing the template data.
     * @return JsonResponse The response containing the status, success flag, message, and template ID.
     */

    public function generate(TemplateRequest $request): JsonResponse
    {
        $checkSubscription = checkUserSubscription('word');

        if ($checkSubscription['status'] != 'success') {
            return response()->json(['error' => $checkSubscription['response']], Response::HTTP_FORBIDDEN);
        }

        try {
            \DB::beginTransaction();
            $this->templateService->initiate($request->validated());
            $id = $this->templateService->prepareData();
            if ($id) {
                \DB::commit();
                return response()->json(['data' => [
                    'templateId' => $id 
                ]], Response::HTTP_OK);
            }

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Process the streaming template data.
     *
     * This method initiates the template service with the request data, retrieves template data, and processes it.
     *
     * @return mixed The processed template data.
     */
    public function process()
    {
        try {
            $this->templateService->initiate(request()->all());
            $this->templateService->templateData();
            return $this->templateService->processData();

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $chatbot The ID of the chatbot to display.
     * @return JsonResponse|TemplateResource
     */
    public function show($id): JsonResponse|TemplateResource
    {
        if (!is_numeric($id)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }
    
        $history = Archive::where('id', $id)->whereType('template')->first();
    
        if (!$history) {
            return response()->json(['error' => __('No data found')], Response::HTTP_NOT_FOUND);
        }

        return response()->json(['data' => new TemplateResource($history)], Response::HTTP_OK);
    }

    /**
     * Delete a code chat reply by its ID.
     *
     * @param int $id The ID of the code chat reply to delete.
     * @return JsonResponse|null
     *         The JSON response indicating the success or failure of the deletion operation.
     */
    public function delete($id): JsonResponse|null
    {
        if (!is_numeric($id)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }

        try {
            ArchiveService::delete($id, 'template');
            return response()->json(['message' => __('The :x has been successfully deleted.', ['x' => __('document')])], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param TemplateUpdateRequest $request
     * @param int $id The ID of the template to update.
     * 
     * @return JsonResponse The JSON response indicating the success or failure of the update operation.
     */
    public function update(TemplateUpdateRequest $request, $id)
    {
        if (!is_numeric($id)) {
            return response()->json(['error' => __('Invalid Request.')], Response::HTTP_FORBIDDEN);
        }
    
        $template = Archive::with('metas')
                    ->where('id', $id)
                    ->whereType('template')
                    ->whereHas('metas', function($query) {
                        $query->where('key', 'template_creator_id')->where('value', auth('api')->id());
                    })->first();

        if (!$template) {
            return response()->json(['error' => __('No data found')], Response::HTTP_NOT_FOUND);
        }
    
        try {
            ArchiveService::update($request->except('_token'), $id);
            $template->refresh();
    
            return response()->json(['data' => new TemplateResource($template)], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode() ? $e->getCode() : Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Toggle favorite template.
     *
     * @param  ToggleBookmarkRequest  $request The request object with the content ID and toggle state.
     * 
     * @return mixed The JSON response indicating the success or failure of the favorite toggle operation.
     */
    public function toggleFavorite(ToggleBookmarkRequest $request): mixed
    {
        $authUser = auth()->user();
        $favoritesArray = $authUser->document_bookmarks_openai ?? [];
        
        $contentId = $request->content_id;
        $toggleState = $request->toggle_state;

        $content = Archive::where('id', $contentId)->whereType('template')->first();

        if (! $content) {
            return response()->json(['message' => __("No data found")], Response::HTTP_NOT_FOUND);
        }
        
        if ($toggleState === 'false' && !in_array($contentId, $favoritesArray)) {
            return response()->json(['message' => __("Invalid Request")], Response::HTTP_FORBIDDEN);
        }
        
        try {
            if ($toggleState === 'true') {
                $favoritesArray = array_unique([...$favoritesArray, $contentId]);
                $message = __("Successfully bookmarked!");
            } else {
                $favoritesArray = array_diff($favoritesArray, [$contentId]);
                $message = __("Successfully removed from bookmarks");
            }
        
            $authUser->document_bookmarks_openai = $favoritesArray;
            $authUser->save();
        } catch (Exception $e) {
            return response()->json(['error' => __("Failed to update bookmarks! Please try again later.")], $e->getCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return response()->json(['message' => $message], Response::HTTP_OK);
    }

}
