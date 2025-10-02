<?php

/**
 * @package FolderService
 * @author TechVillage <support@techvill.org>
 * @contributor Md. Khayeruzzaman <[shakib.techvill@gmail.com]>
 * @created 19-10-2023
 */

namespace Modules\OpenAI\Services;

use ZipArchive;

use Modules\OpenAI\Entities\{
    Code,
    Folder,
    FolderItem,
    FolderMeta,
    Speech,
    Audio,
    Archive
};

class FolderService
{

    /**
     * Core Model
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function model()
    {
        return Folder::with(['user', 'folderItem', 'metaData'])->select('*')->selectRaw('"folder" as type')->selectRaw('folders.parent_id as parent_folder');
    }

    /**
     * Store folder 
     * @param array $data
     * @return boolean|array
     */
    public function store($data = [])
    {

        $folderId = Folder::insertGetId($data);
        $folder = $this->getFolderById($folderId);
        $parentFolder = self::model()->where('id', $data['parent_id'])->value('slug');

        if ( $folder) {

           $this->storeMeta($folderId);

           if ($data['parent_id'] != NULL) {
                $this->updateCount($data['parent_id'], Null);
           }

           return [
                'id' => $folderId,
                'view_route' => route('user.folderView', ['slug' => $folder->slug]),
                'items'=> $folder->item_count,
                'name' => trimWords($folder->name, 50),
                'creator' => $folder->user?->name,
                'date' => $folder->updated_at ? timeToGo($folder->updated_at, false, 'ago') : timeToGo($folder->created_at, false, 'ago'),
                'parent_route' => str_contains($parentFolder, 'drive-') ? true : false,
                'parent_id' => $folder->parent_id
            ];
        }

        return false;
    }

    /**
     * Get All folders
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Builder
     */
    public static function getAll()
    {
        $result = self::model();

        $result = $result->where('user_id', auth()->user()->id);
        return $result->whereNotNull('parent_id')->latest();
    }

    /**
     * Folder By Slug
     *
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Collection|array
     */
    public function folderBySlug($slug)
    {
        $folder = self::model()->where('slug', $slug);

        $folder = $folder->where('user_id', auth()->user()->id);

        $folder = $folder->first();

        if (!$folder) {
            abort(404);
        }

        $allFolders = $this->getAllFolders($folder);

        return $this->getFolderItems($allFolders, $folder);
    }

    /**
     * Get all folders
     *
     * @param mixed $folder
     *
     * @return \Illuminate\Support\Collection
     */
    private function getAllFolders($folder)
    {
        return self::model()->where('parent_id', $folder->id)->get();
    }

    /**
     * Get folder's Items
     *
     * @param mixed $allFolders
     * @param mixed $folder
     * @param string $roleType
     *
     * @return \Illuminate\Support\Collection
     */
    private function getFolderItems($allFolders, $folder)
    {
        $folderWithItems = $this->folderWithItems($folder->id);
        $skipItems = $folderWithItems->pluck("type", "id")->toArray();

        $result = $allFolders->concat($folderWithItems);
        $useId = auth()->user()?->id;
        
        if (str_contains($folder->slug, 'drive-')) {
            return $result->concat($this->allContents($useId, $folder->id, $skipItems));
        }

        if (!str_contains($folder->slug, 'drive-')) {
            return $result;
        }

        return $result;
    }

    /**
     * Get folder id By Slug
     *
     * @param string $slug
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getFolderIdBySlug($slug)
    {
        return self::model()->where('slug', $slug)->value('id');
    }

    /**
     * Get parent folders By Id
     *
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getParentFoldersById($id)
    {
        $result = self::model();

        $result = $result->where('user_id', auth()->user()->id);

        return $result->where('parent_id', $id)->latest();
    }

    /**
     * Move data to the specific folder
     *
     * @param array $data
     * @return boolean
     */
    public function move($data = [])
    {
        if (!is_array($data) || !is_array($data['items']) || !isset($data['items'], $data['folder_id'])) {
            return false; 
        }
        
        foreach ( $data['items'] as $item ) {
            [$type, $itemId] = explode('-', $item);
            $this->moveFolder($itemId, $type, $data['folder_id'], $data['parent_folder_id']);
        }

        return true;
    }

    /**
     * Move Multiple Folder & Content
     *
     * @param string $id
     * @param string $type
     * @param string $parentIds
     *
     * @return boolean
     */

    protected function moveFolder($id, $type, $currentFolderId, $prevFolderId)
    {
        $data = [
            'folder_id' => $currentFolderId,
            'item_id' => $id,
            'item_type' => $type
        ];

        if ($type == 'folder') {
            $success = self::model()->where('id', $id)->update(['parent_id' => $currentFolderId]);
        } else {
            $success = FolderItem::updateOrCreate(['item_id' => $id, 'item_type' => $type], $data);
        }

        if ($success) {
            $this->updateCount($currentFolderId, $prevFolderId);
            return true;
        }

        return false;
    }

    /**
     * Get item based on types
     *
     * @param string $type
     * @return [type]
     */
    public function getItems($type)
    {
        $items = FolderItem::where('item_type', $type)->pluck('folder_id', 'item_id')->toArray();
        $parentFolders = Folder::whereNull('parent_id')->pluck('id')->toArray();

        $items = array_filter($items, function ($itemValue) use ($parentFolders) {
            return !in_array($itemValue, $parentFolders);
        });

        return array_keys($items);
    }

    /**
     * Get Folder By Id
     *
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Builder|array
     */
    public function getFolderById($id)
    {
        $result = self::model();

        $result = $result->where('user_id', auth()->user()->id);

        return $result->where('id', $id)->first();
    }

    /**
     * Get Folders with Items
     *
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Builder|array
     */
    protected function folderWithItems($id) {

        return FolderItem::where('folder_items.folder_id', $id)
                ->leftJoin('audios', function ($join) {
                    $join->on('folder_items.item_id', '=', 'audios.id')
                        ->where('folder_items.item_type', 'audio');
                })
                ->leftJoin('archives', function ($join) {
                    $join->on('folder_items.item_id', '=', 'archives.id')
                        ->where(function ($query) {
                            $query->where(function ($subQuery) {
                                $subQuery->where('folder_items.item_type', 'long_article')
                                        ->where('archives.status', 'Completed');
                            })
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('folder_items.item_type', 'image_variant')
                                        ->whereNull('archives.user_id');
                            })
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('folder_items.item_type', 'video')
                                        ->where('archives.user_id', auth()->user()->id);
                            })
                            ->orWhere(function ($sub) {
                                $sub->where('folder_items.item_type', 'template')
                                        ->whereNull('archives.user_id');
                            })
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('folder_items.item_type', 'code_chat_reply')
                                        ->whereNull('archives.user_id');
                            })
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('folder_items.item_type', 'speech_to_text_chat_reply')
                                        ->whereNull('archives.user_id');
                            });
                        });
                })
                ->leftJoin('archives_meta', function ($join) {
                    $join->on('archives.id', '=', 'archives_meta.owner_id')
                        ->where(function ($query) {
                            $query->where(function ($subQuery) {
                                $subQuery->where('archives_meta.key', 'image_creator_id')
                                ->where('archives_meta.value', auth()->user()->id);
                            })
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('archives_meta.key', 'template_creator_id')
                                ->where('archives_meta.value', auth()->user()->id);
                            })
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('archives_meta.key', 'code_creator_id')
                                ->where('archives_meta.value', auth()->user()->id);
                            })
                            ->orWhere(function ($subQuery) {
                                $subQuery->where('archives_meta.key', 'speech_to_text_chat_reply')
                                ->where('archives_meta.value', auth()->user()->id);
                            });
                        });
                })
                ->selectRaw('
                    CASE
                        WHEN audios.id IS NOT NULL THEN ?
                        WHEN archives.id IS NOT NULL THEN ?
                        ELSE NULL
                    END as parent_folder', [$id, $id]
                )
                ->selectRaw('folder_items.item_type as type')
                ->selectRaw('COALESCE(audios.id, archives.id) as id')
                ->selectRaw('COALESCE(archives.content, audios.prompt, archives.title,
                    (SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "code_title")
                ) as name')
                ->selectRaw('COALESCE(audios.slug,
                    (
                        SELECT value FROM archives_meta
                        WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "slug"
                    )) as slug')
                ->selectRaw('COALESCE(
                    (SELECT name FROM users WHERE users.id = audios.user_id),
                    (SELECT name FROM users WHERE users.id = archives.user_id),
                    (SELECT name FROM users WHERE users.id = (SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "image_creator_id")),
                    (SELECT name FROM users WHERE users.id = (SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "template_creator_id")),
                    (SELECT name FROM users WHERE users.id = (SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "code_creator_id")),
                    (SELECT name FROM users WHERE users.id = (SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "speech_to_text_creator_id"))
                    ) as creator_name')
                ->selectRaw('COALESCE(audios.created_at, archives.created_at) as created_at')
                ->selectRaw('COALESCE(audios.updated_at, archives.updated_at) as updated_at')
                ->get();
    }

    /**
     * Get All content
     *
     * @param string $id
     * @return \Illuminate\Database\Eloquent\Builder|array|\Illuminate\Support\Collection
     */
    protected function allContents($userId, $folderId, $skipItems) {

        if (empty($folderId)) {
            $folderId = null;
        }

        $contents = self::archiveModel()->where('type', 'template')->whereNull('user_id')
        ->select(
            'archives.id',
            'archives.content as name',
            \DB::raw('(SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "slug") as slug'),
            'archives.created_at',
            'archives.updated_at',
            \DB::raw('(SELECT users.name FROM users where users.id =
                ( SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "template_creator_id" )) as creator_name'
            ),
            'archives.type',
            \DB::raw( $folderId . ' as parent_folder'),
            \DB::raw('"--" as item_count')
        );

        $images = self::archiveModel()->where('type', 'image_variant')->whereNull('user_id')
        ->select(
            'archives.id',
            'archives.title as name',
            \DB::raw('(SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "slug") as slug'),
            'archives.created_at',
            'archives.updated_at',
            \DB::raw('(SELECT users.name FROM users where users.id =
                ( SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "image_creator_id" )) as creator_name'
            ),
            'archives.type',
            \DB::raw( $folderId . ' as parent_folder'),
            \DB::raw('"--" as item_count')
        );

        $videos = self::archiveModel()->where('type', 'video')
        ->select(
            'archives.id',
            'archives.title as name',
            \DB::raw('(SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "slug") as slug'),
            'archives.created_at',
            'archives.updated_at',
            \DB::raw('(SELECT users.name FROM users where users.id =
                ( SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "video_creator_id" )) as creator_name'
            ),
            'archives.type',
            \DB::raw( $folderId . ' as parent_folder'),
            \DB::raw('"--" as item_count')
        );

        $codes = self::archiveModel()->where('type', 'code_chat_reply')->whereNull('user_id')->select(
            'archives.id',
            \DB::raw('
                ( SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "code_title" ) as name'
            ),
            \DB::raw('(SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "slug") as slug'),
            'archives.created_at',
            'archives.updated_at',
            \DB::raw('(SELECT users.name FROM users where users.id = 
                ( SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "code_creator_id" )) as creator_name'
            ),
            'archives.type',
            \DB::raw( $folderId . ' as parent_folder'),
            \DB::raw('"--" as item_count')
        );

        $speeches = self::archiveModel()->where('type', 'speech_to_text_chat_reply')->whereNull('user_id')->select(
            'archives.id',
            'archives.title as name',
            \DB::raw('NULL as slug'),
            'archives.created_at',
            'archives.updated_at',
            \DB::raw('(SELECT users.name FROM users where users.id = 
                ( SELECT value FROM archives_meta WHERE archives_meta.owner_id = archives.id AND archives_meta.key = "speech_to_text_creator_id" )) as creator_name'
            ),
            'archives.type',
            \DB::raw( $folderId . ' as parent_folder'),
            \DB::raw('"--" as item_count')
        );

        $audios = \DB::table('audios')
        ->join('users', 'audios.user_id', '=', 'users.id')
        ->select(
            'audios.id',
            'audios.prompt as name',
            'audios.slug',
            'audios.created_at',
            'audios.updated_at',
            'users.name as creator_name',
            \DB::raw('"audio" as type'),
            \DB::raw( $folderId . ' as parent_folder'),
            \DB::raw('"--" as item_count')
        );

        $archives = \DB::table('archives')
        ->join('users', 'archives.user_id', '=', 'users.id')
        ->where('archives.type' , 'long_article')
        ->where('archives.status' , 'Completed')
        ->select(
            'archives.id',
            'archives.title as name',
            \DB::raw('NULL as slug'),
            'archives.created_at',
            'archives.updated_at',
            'users.name as creator_name',
            'archives.type',
            \DB::raw( $folderId . ' as parent_folder'),
            \DB::raw('"--" as item_count')
        );

        if ($userId) {
            $contents->whereHas('metas', function($query) use($userId) {
                $query->where('key','template_creator_id')->where('value', $userId);
            });
            $images->whereHas('metas', function($query) use($userId) {
                $query->where('key','image_creator_id')->where('value', $userId);
            });
            $codes->whereHas('metas', function($query) use($userId) {
                $query->where('key','code_creator_id')->where('value', $userId);
            });
            $audios->where('user_id', $userId);
            $speeches->whereHas('metas', function($query) use($userId) {
                $query->where('key','speech_to_text_creator_id')->where('value', $userId);
            });
            $archives->where('user_id', $userId);
            $videos->where('user_id', $userId);
        }

        return $contents->union($images)->union($codes)->union($speeches)->union($audios)->union($archives)->union($videos)
                ->get()
                ->filter(function ($item) use ($skipItems) {
                    foreach ($skipItems as $id => $type) {
                        if ($item->type === $type && $item->id == $id) {
                            return false; // Skip this item
                        }
                    }
                    return true; // Keep this item
                });

    }

    /**
     * Store meta data
     * 
     * @param string $id
     * @return void
     */
    public function storeMeta($id)
    {
        $meta = [
            'folder_id' => $id,
            'key' => 'item_count',
            'value' => 0
        ];

        FolderMeta::insert($meta);
    }

    /**
     * Update meta data's count
     * 
     * @param string $id
     * @return void
     */
    protected function updateCount($currentFolderId, $prevFolderId){
        FolderMeta::where(['folder_id' => $currentFolderId, 'key' => 'item_count'])->increment('value');
        if ($prevFolderId != Null) {
            FolderMeta::where(['folder_id' => $prevFolderId, 'key' => 'item_count'])->decrement('value');
        }
    }


    /**
     * Delete Folder and its contents recursively
     *
     * @param Folder $folder
     * @return mixed
     */
    protected function deleteFolderAndContents($folder)
    {
        $items = FolderItem::where('folder_id', $folder->id)->get();

        foreach ($items as $item) {
            $this->deleteItem($item);
        }

        $subFolders = self::model()->where('parent_id', $folder->id)->get();

        foreach ($subFolders as $subFolder) {
            $this->deleteFolderAndContents($subFolder);
        }

        $folder->delete();
    }

    /**
     * Delete an item based on its type
     *
     * @param FolderItem $item
     * @return void
     */
    protected function deleteItem($item)
    {
        FolderItem::where(['item_id' => $item->item_id, 'item_type' => $item->item_type])->delete();

        $modelMap = [
            'template'        => self::archiveModel(),
            'code_chat_reply' => self::archiveModel(),
            'image_variant'   => self::archiveModel(),
            'long_article'    => self::archiveModel(),
            'video'           => self::archiveModel(),
            'speech_to_text_chat_reply'  => self::archiveModel(),
            'audio'           => new Audio(),
        ];
        
        if (isset($modelMap[$item->item_type])) {
            $modelMap[$item->item_type]->where('id', $item->item_id)->delete();
        }
    }

    /**
     * Get Breadcrumb Trail
     *
     * @param $slug
     * @return array
     */

    public static function getBreadcrumbTrail($slug)
    {
        $breadcrumbs = [];

        $currentFolder = self::model()->where('slug', $slug)->first();
        $parentFolderId = $currentFolder->id;

        while ($currentFolder) {
            $breadcrumbs[] = [
                'name' => $currentFolder->name,
                'slug' => $currentFolder->slug,
                'view' => route('user.folderView', ['slug' => $currentFolder->slug]),
                'parent_folder' => $parentFolderId
            ];

            $currentFolder = self::model()->where('id', $currentFolder->parent_id)->first();
            
        }

        $breadcrumbs = array_reverse($breadcrumbs);
        $userRole = auth()->user()->roles()->first();

        if ($userRole != 'user'){
            $breadcrumbs = array_map(function ($item) {
                if (str_contains($item["slug"], 'drive-')) {
                    $item["view"] = route('user.folderView', ['slug' => 'drive-' . auth()->user()->id]);
                }
                return $item;
            }, $breadcrumbs);
        }

        return $breadcrumbs;
    }

    /**
     * Update Folder and its contents
     *
     * @param array $data
     * @return array|boolean
     */
    public function update($data)
    {
        $folder = self::model()->where('id', $data['folder_id'])->first();

        if ($folder) {
            $folder->name = $data['name'];
            $folder->slug = $data['slug'];
            $folder->save();

            $data = [
                'id' =>  $folder->id,
                'view_route' => route('user.folderView', ['slug' => $folder->slug]),
                'items' => $folder->item_count ? $folder->item_count : '0',
                'name' => trimWords($folder->name, 50),
                'creator' => $folder->user?->name,
                'date' => $folder->updated_at ? timeToGo($folder->updated_at, false, 'ago') : timeToGo($folder->created_at, false, 'ago'),
            ];

            return $data;
        }

        
        return false;
    }

    /**
     * Get Breadcrumb Trail For Modal
     *
     * @param $slug
     * @param $parentFolder
     *
     * @return array
     */
    public static function getBreadcrumbTrailForModal($folder, $parentFolder)
    {
        $breadcrumbs = [];

        $currentFolder = $folder;

        while ($currentFolder) {
            $breadcrumbs[] = [
                'id' => $currentFolder->id,
                'name' => $currentFolder->name,
                'parent_folder' => $parentFolder
            ];

            $currentFolder = self::model()->where('id', $currentFolder->parent_id)->first();
        }

        return array_reverse($breadcrumbs);
    }

    /**
     * Toggle Bookmark for files
     *
     * @param array $data
     * @return array
     */
    public function toggleBookmark($data){
        $authUser = auth()->user();
        $favoritesArray = $authUser->files_bookmark ?? [];
        $type = $data['type'];
        $fileId = $data['file_id'];

        try {

            if ($data['toggle_state'] == 'true') {
               
                if ( isset($favoritesArray[$type]) ) {
                    $existingFileIds = explode(',', $favoritesArray[$type]);
                    
                    if (!in_array($fileId, $existingFileIds)) {
                        $existingFileIds[] = $fileId;
                        $favoritesArray[$type] = implode(',', $existingFileIds);
                    }
                } else {
                    $favoritesArray[$data['type']] = $data['file_id'];
                }

                $message = __("Successfully bookmarked!");

            } else {
                if (isset($favoritesArray[$type])) {
                    $existingFileIds = explode(',', $favoritesArray[$type]);
                    $existingFileIds = array_map('trim', $existingFileIds);
            
                    $existingFileIds = array_diff($existingFileIds, [$fileId]);
                    $favoritesArray[$type] = implode(',', $existingFileIds);
                }

                $message = __("Successfully removed from bookmark!");
            }

            $authUser->files_bookmark = $favoritesArray;
            $authUser->save();
        } catch (\Exception $e) {
            return response()->json(["success" => false, "message" => __("Failed to update bookmark! Please try again later.")], 500);
        }

        return response()->json(["success" => true, "message" => $message], 200);
    }

    /**
     * Download folder with files
     *
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadFolder($id) {
        $folder = self::model()->where('id', $id)->first();
        $data = ["status" => 'error', "message" => __('Folder doesn\'t exist.')];

        if ($folder) {
            $zipPath = storage_path('app/public/' . $folder->slug . '.zip');
            $zip = new ZipArchive;
            $zip->open($zipPath, ZipArchive::CREATE);

            $this->addFolderToZip($folder, $zip);
            $zip->close();

            $fileContents = file_get_contents($zipPath);
            $base64File = base64_encode($fileContents);

            unlink($zipPath);

            return response()->json(['file' => $base64File, 'name' => $folder->slug]);
        }

        return response()->json($data);

    }

    /**
     * Folder to ZIP
     *
     * @return void
     */
    protected function addFolderToZip($folder, $zip, $parentFolder = '') {
        $currentFolder = trim($parentFolder . '/' . $folder->name, '/');
        $zip->addEmptyDir($currentFolder);
        
        foreach ($folder->folderItem as $item) {
            $data = $this->getItem($item);
            if ($data) {
                $fileName = $currentFolder . '/' . $data['fileName'];
                if ($data['type'] == 'image_variant' || $data['type'] == 'audio' || $data['type'] == 'video') {
                    $contents = !empty($data['file']) ? file_get_contents(str_replace('\\', '/', $data['file'])) : '';
                } else {
                    $contents = $data['contents'];
                }
                
                $zip->addFromString($fileName, $contents);
            }
        }

        $subFolders = self::model()->where('parent_id', $folder->id)->get();
        if ( !empty($subFolders) ){
            foreach ($subFolders as $subfolder) {
                $this->addFolderToZip($subfolder, $zip, $currentFolder);
            }
        }
        
    }

    /**
     * Delete an item based on its type
     *
     * @param FolderItem $item
     * @return mixed
     */
    protected function getItem($item)
    {
        switch ($item->item_type) {
            case 'template':
                $content = self::archiveModel()->where('id', $item->item_id)->first();
                return [
                    'type' => $item->item_type,
                    'fileName' => $content->slug . '.doc',
                    'contents' => $content->content
                ];
            case 'code_chat_reply':
                $code = self::archiveModel()->where('id', $item->item_id)->first();
                return [
                    'type' => $item->item_type,
                    'fileName' => $code->slug . '.doc',
                    'contents' => $code->code
                ];
            case 'image_variant':
                $image = self::archiveModel()->where('id', $item->item_id)->first();
                return [
                    'type' => $item->item_type,
                    'fileName' => filterDownloadName($image->title, $image->original_name),
                    'file' => $image->imageUrl(),
                ];
            case 'long_article':
                $longArticle = self::archiveModel()->where('id', $item->item_id)->first();
                return [
                    'type' => $item->item_type,
                    'fileName' => $longArticle->title . '.doc',
                    'contents' => $longArticle->content
                ];
            
            case 'audio':
                $audio = self::archiveModel()->where('id', $item->item_id)->first();
                return [
                    'type' => $item->item_type,
                    'fileName' => filterDownloadName($audio->prompt, $audio->file_name),
                    'file' => $audio->googleAudioUrl(),
                ];
            case 'speech_to_text':
                $speech = Speech::where('id', $item->item_id)->first();
                return [
                    'type' => $item->item_type,
                    'fileName' => filterDownloadName($speech->content, 'speech.doc'),
                    'contents' => $speech->content,
                ];

            case 'video':
                $video = self::archiveModel()->where('id', $item->item_id)->first();
                return [
                    'type' => $item->item_type,
                    'fileName' => filterDownloadName($video->title, $video->original_name),
                    'file' => $video->videoUrl(),
                ];
        }
    }

    /**
     * Download content
     *
     * @param integer $id
     * @return string
     */
    public function downloadContent($id, $type) {

        switch($type) {
            case 'template':
                return self::archiveModel()->where('id', $id)->value('content');
            case 'code_chat_reply':
                $code = self::archiveModel()->where('id', $id)->first();
                return $code->code;
            case 'long_article':
                return self::archiveModel()->where('id', $id)->value('content');
            case 'speech_to_text_chat_reply':
                return self::archiveModel()->where('id', $id)->value('content');
            default:
                return '';
        }
    }

    /**
     * Delete Multiple Folder and its contents
     *
     * @param array $id 
     * @return boolean
     */
    public function multiDelete($ids)
    {
        $success = true;
        foreach ($ids as $id) {
            [$type, $itemId] = explode('-', $id, 2);
            $success = $this->deleteMultipleItem($itemId, $type) && $success;
        }

        return $success;
    }

    /**
     * Delete Multiple contents
     *
     * @param string $id 
     * @param string $type 
     * @return boolean
     */

    protected function deleteMultipleItem($id, $type)
    {
        FolderItem::where('item_id', $id)->where('item_type', $type)->delete();

        switch ($type) {
            case 'template':
                return self::archiveModel()->where('id', $id)->delete() ? true : false;
                
            case 'folder':
                $folder = self::model()->where('id', $id)->first();
                if ($folder) {
                    $this->deleteFolderAndContents($folder);
                    return true;
                }
                return false;

            case 'image_variant':
                return self::archiveModel()->where('id', $id)->delete() ? true : false;

            case 'code_chat_reply':
                return self::archiveModel()->where('id', $id)->delete() ? true : false;

            case 'speech_to_text_chat_reply':
                return self::archiveModel()->where('id', $id)->delete() ? true : false;

            case 'audio':
                return Audio::where('id', $id)->delete() ? true : false;

            case 'long_article':
                return self::archiveModel()->where('id', $id)->delete() ? true : false;

            case 'video':
                return self::archiveModel()->where('id', $id)->delete() ? true : false;

            default:
                return false;
        }
    }

    /**
     * Get Folders
     *
     * @param array $items
     *
     * @return array
     */
    public function getFolders($items) {
        $data = [];

        if (!is_array($items)) {
            return $data;
        }

        foreach ($items as $item) {
            
            [$type, $itemId] = explode('-', $item, 2);

            if ($type == 'folder' && self::model()->where('id', $itemId)->exists()) {
                $data[] = $itemId;
            }
        }
        
        return $data;
    }

    /**
     * Filter All data base on moved data
     *
     * @param mixed $folders
     *
     * @return array
     */
    public function filterAllData($folders) {
        $currentUrl = url()->current();
        $name = explode('/user/folder/', $currentUrl)[1];

        $item = str_contains($name, 'drive-');

        $itemTypes = ['template', 'image', 'code_chat_reply', 'speech_to_text_chat_reply', 'audio', 'long_article', 'image_variant', 'video'];
        $items = [];

        foreach ($itemTypes as $type) {
            $items[$type] = [];

            if ($item) {
                $items[$type] = $this->getItems($type);
            }
        }

        foreach ($folders as $key => $folder) {
            if ($folder->type === 'folder') {
                continue;
            }
        
            if ( in_array($folder->id, $items[$folder->type]) || is_null($folder->id) ) {
                unset($folders[$key]);
            }
        }

        return $folders;
    }

    /**
     * Create folder while registration
     *
     * @param int $items
     *
     * @return void
     */
    public function createFolder($id) {

        $folder = [
            'user_id' => $id,
            'name' => 'Drive',
            'slug' => 'drive-' . $id
        ];

        Folder::insert($folder);
    }

   /**
     * Get the Archive model with its metas relationship.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function archiveModel()
    {
        return Archive::with(['metas']);
    }
}
