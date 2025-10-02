<?php

namespace Modules\OpenAI\Http\Controllers\Customer;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use DB;

use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Modules\OpenAI\Services\FolderService;
use Illuminate\Pagination\LengthAwarePaginator;

class FolderController extends Controller
{
    /**
     * @param FolderService $folderService
     */

    public function __construct(
        protected FolderService $folderService
        ) {}

    /**
     * View to the specific folder
     * @param string $slug
     * @return \Illuminate\Contracts\View\View
     */
    public function view($slug)
    {
        $folders = $this->folderService->folderBySlug($slug);
        $filterData = $this->folderService->filterAllData($folders);

        $data['userFavoriteFiles'] = auth()->user()->files_bookmark ?? [];

        $perPage = preference('row_per_page');
        $page = request()->get('page', 1);

        $collection = new Collection($filterData);

        // Paginate the collection manually as data comes as arary foramt. We will refactor it later.
        $paginatedData = new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );


        $data['folders'] = $paginatedData;

        return view('openai::blades.drive.drive-list', $data);

    }

    /**
     * Folder create
     *
     * @param  Request  $request
     * @return array
     */
    public function create(Request $request): mixed
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];
        $request = app('Modules\OpenAI\Http\Requests\FolderStoreRequest')->safe();
        $folder = "";

        try {
            DB::beginTransaction();

            $folder =  $this->folderService->store($request->all());

            if (!$folder) {
                throw new \Exception(__('The attempt to create a folder was unsuccessful'));
            } else {
                DB::commit();
                $response = ['status' => 'success', 'message' => __('The :x has been successfully created.', ['x' => __('Folder')])];
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $response['message'] = $e->getMessage();
        }

        return response()->json([
            'data' => $folder,
            'response' => $response
        ]);
    }

    /**
     * View to the specific folder
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function fetchFolder(Request $request)
    {
        $parentFolderId = $request->parentId;
        $moveDataId = $request->moveDataId;
        $data['folders'] = $this->folderService->getParentFoldersById($request->id)->paginate(preference('row_per_page'));
        $data['excludedFolderIds'] = [$parentFolderId];
        $data['breadCrumbs'] = [];

        if ($data['folders']) {
            $folder = $this->folderService->getFolderById($request->id);
            $data['breadCrumbs'] = $this->folderService->getBreadcrumbTrailForModal($folder, $parentFolderId);
        }

        $html = view('openai::blades.drive.drive-content', $data)->render();

        return response()->json([
            'html' => $html,
            "button_disabled" => $request->id == $moveDataId ? true : false,
            'nextPageUrl' => $data['folders']->nextPageUrl()
        ]);
    }

    /**
     * Move data to the specific folder
     * 
     * @param  Request  $request
     * @return []
     */
    public function moveData(Request $request)
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];
        $request = app('Modules\OpenAI\Http\Requests\FolderItemStoreRequest')->safe();
        if ($this->folderService->move($request->all())) {
            $response = [
                'status' => 'success',
                'message' => __('The :x has been successfully moved.', ['x' => __('Items')])
            ];
        }

        return $response;
    }

    /**
     * Fetch All folders
     * 
     * @param  Request  $request
     * @return \Illuminate\Contracts\View\View
     */
    public function fetchAllFolder(Request $request)
    {
        $data['folders'] =  $this->folderService->getAll()->where('id', '!=', $request->id)->paginate(preference('row_per_page'));

        $data['breadCrumbs'] = $this->folderService->getBreadcrumbTrail('drive-' . auth()->user()->id);

        $folderId = $this->folderService->getFolderIdBySlug('drive-' . auth()->user()->id);

        $data['excludedFolderIds'] = $this->folderService->getFolders($request->items);

        $data['excludedFolderIds'] = array_merge($data['excludedFolderIds'], [$folderId]);

        $html = view('openai::blades.drive.drive-content', $data)->render();

        return response()->json([
            'html' => $html,
            'folder_id' => $folderId,
            'button_disabled' => $folderId == $request->parentId ? true : false,
            'nextPageUrl' => $data['folders']->nextPageUrl()
        ]);
    }

    /**
     * Folder update
     *
     * @param  Request  $request
     * @return array
     */
    public function update(Request $request): mixed
    {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];
        $request = app('Modules\OpenAI\Http\Requests\FolderStoreRequest')->safe();
        $folder = [];

        try {
            DB::beginTransaction();

            $folder =  $this->folderService->update($request->all());

            if (!$folder) {
                throw new \Exception(__('The :x does not exist.', ['x' => __('Folder')]));
            } else {
                DB::commit();
                $response = ['status' => 'success', 'message' => __('The :x has been successfully updated.', ['x' => __('Folder')])];
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $response['message'] = $e->getMessage();
        }

        return response()->json([
            'data' => $folder,
            'response' => $response
        ]);
    }

    /**
    * Delete folder
    * @param Request $request
    *
    * @return [type]
    */
    public function delete(Request $request) {
        $response = ['status' => 'fail', 'message' => __('Invalid Request')];

        if ($this->folderService->multiDelete($request->id)  ) {
            $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Items')])];
        }
        return $response;
        
    }

    /**
    * Toggle Bookmark Files
    * @param Request $request
    *
    * @return [type]
    */
    public function toggleBookmarkFiles(Request $request) {
        return $this->folderService->toggleBookmark($request->all());
    }

    /**
    * Download Files with Folder
    * @param integer $id
    *
    * @return [type]
    */
    public function download($id) {
        return $this->folderService->downloadFolder($id);
    }

    /**
    * Download content
    * @param Request $request
    *
    * @return [type]
    */
    public function downloadContent(Request $request) {
        return response()->json([
            'data' => $this->folderService->downloadContent($request->id, $request->type),
        ]);
    }
}
