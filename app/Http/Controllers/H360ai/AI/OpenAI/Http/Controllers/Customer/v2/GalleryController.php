<?php

namespace Modules\OpenAI\Http\Controllers\Customer\v2;

use Modules\OpenAI\Transformers\Api\v2\Image\SingleImageResources;
use Modules\OpenAI\Entities\Archive;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\OpenAI\Services\v2\ImageService;

class GalleryController extends Controller
{

    public function gallery(Request $request)
    {
        $data['images'] = SingleImageResources::collection(
            Archive::query()
                ->select('archives.*')
                ->whereIn('archives.type', ['image_variant', 'video'])
                ->with(['metas'])
                ->join('archives_meta', function ($join) {
                    $join->on('archives.id', '=', 'archives_meta.owner_id')
                        ->where(function ($query) {
                            $query->where(function ($metaQuery) {
                                $metaQuery->where('archives_meta.key', 'image_creator_id')
                                            ->where('archives_meta.value', auth()->id());
                            })->orWhere(function ($metaQuery) {
                                $metaQuery->where('archives_meta.key', 'video_creator_id')
                                            ->where('archives_meta.value', auth()->id());
                            });
                        });
                })
                ->latest('archives.created_at')
                ->paginate(preference('row_per_page'))
        );
       
        $data['userFavoriteImages'] = auth()->user()->image_favorites ?? [];

        $data['currentImage'] = [];
        $data['relatedImages'] = [];
        $data['variants'] = [];

        if ($request->ajax()) {
            $imageItems = (new ImageService())->prepareImageData($data['images'], $data['userFavoriteImages'],  'medium');

            return response()->json([
                'items' =>  $imageItems,
                'nextPageUrl' => $data['images']->nextPageUrl()
            ]);
        }

        return view('openai::blades.v2.images.gallery.gallery', $data);
    }

}
