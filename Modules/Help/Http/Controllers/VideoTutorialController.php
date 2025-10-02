<?php

namespace Modules\Help\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Help\Entities\VideoTutorial;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Http;
use Log; // Assurez-vous d'importer le facade Log

class VideoTutorialController extends Controller
{
    // Affiche l'interface de gestion des vidéos
    public function index()
    {
        if (request()->ajax()) {
            $videos = VideoTutorial::select(['id', 'title', 'display_url', 'hashtags']);

            return Datatables::of($videos)
                ->addColumn('action', function($row){
                    $html = '<div class="btn-group">';
                    $html .= '<button type="button" class="btn btn-xs btn-primary btn-modal" data-href="' . action([\Modules\Help\Http\Controllers\VideoTutorialController::class, 'edit'], [$row->id]) . '" data-container=".video_tutorial_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</button>';
                    $html .= '<button type="button" class="btn btn-xs btn-danger delete_video_tutorial" data-href="' . action([\Modules\Help\Http\Controllers\VideoTutorialController::class, 'destroy'], [$row->id]) . '"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';
                    $html .= '</div>';
                    return $html;
                })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('help::videos.index');
    }

    // Affiche le formulaire de création
    public function create()
    {
        return view('help::videos.create');
    }

    // Enregistre une nouvelle vidéo (méthode corrigée)
    public function store(Request $request)
    {
        try {
            $input = $request->only(['youtube_url', 'display_url', 'hashtags', 'title', 'description']);

            // **CORRECTION : Méthode robuste pour extraire l'ID de la vidéo YouTube**
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $input['youtube_url'], $match);
            $video_id = $match[1] ?? null;

            if (is_null($video_id)) {
                return ['success' => false, 'msg' => 'Le lien YouTube est invalide.'];
            }
            
            $input['video_id'] = $video_id;

            // Si le titre ou la description sont vides, on essaie de les récupérer sur YouTube
            if (empty($input['title']) || empty($input['description'])) {
                try {
                    $response = Http::get("https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v={$video_id}&format=json");
                    
                    if ($response->successful()) {
                        $video_info = $response->json();
                        if (empty($input['title'])) {
                            $input['title'] = $video_info['title'];
                        }
                        if (empty($input['description'])) {
                            $input['description'] = substr($video_info['author_name'] . ' - ' . $video_info['title'], 0, 250);
                        }
                    } else {
                        // Si l'appel échoue, on utilise des valeurs par défaut pour ne pas bloquer
                        if (empty($input['title'])) $input['title'] = 'Titre non disponible';
                        if (empty($input['description'])) $input['description'] = 'Description non disponible';
                    }
                } catch (\Exception $e) {
                    // En cas d'erreur de connexion (ex: cURL error 60), on logue et on continue
                    Log::error("YouTube oEmbed request failed: " . $e->getMessage());
                    if (empty($input['title'])) $input['title'] = 'Titre non disponible (connexion impossible)';
                    if (empty($input['description'])) $input['description'] = 'Description non disponible';
                }
            }
            
            VideoTutorial::create($input);

            $output = ['success' => true, 'msg' => __("lang_v1.added_success")];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }


    // Affiche le formulaire d'édition (méthode corrigée)
    public function edit($id)
    {
        $video = VideoTutorial::findOrFail($id);
        return view('help::videos.edit')->with(compact('video'));
    }

    // Met à jour une vidéo (méthode corrigée)
    public function update(Request $request, $id)
    {
        try {
            $input = $request->only(['youtube_url', 'display_url', 'hashtags', 'title', 'description']);
            $video = VideoTutorial::findOrFail($id);

            // **CORRECTION : Méthode robuste pour extraire l'ID de la vidéo YouTube**
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $input['youtube_url'], $match);
            $video_id = $match[1] ?? null;

            if (is_null($video_id)) {
                return ['success' => false, 'msg' => 'Le lien YouTube est invalide.'];
            }

            $input['video_id'] = $video_id;

            $video->update($input);

            $output = ['success' => true, 'msg' => __('lang_v1.updated_success')];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }

        return $output;
    }


    // Supprime une vidéo
    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                VideoTutorial::findOrFail($id)->delete();
                $output = ['success' => true, 'msg' => __("lang_v1.deleted_success")];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
            }
            return $output;
        }
    }
    
    // API pour le frontend
    public function fetchVideosForUrl(Request $request)
    {
        $url = $request->query('url');
        if (empty($url)) {
            return response()->json([]);
        }

        $videos = VideoTutorial::where('display_url', $url)
                                ->orWhere('display_url', '*')
                                ->get(['title', 'description', 'video_id']);

        return response()->json($videos);
    }
}