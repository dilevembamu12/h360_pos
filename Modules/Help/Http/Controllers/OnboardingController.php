<?php
namespace Modules\Help\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Help\Entities\OnboardingStep;
use Yajra\DataTables\Facades\DataTables;

class OnboardingController extends Controller
{
    public function index()
    {
        // Si c'est une requête AJAX (venant du DataTable), on renvoie les données.
        if (request()->ajax()) {
            $steps = OnboardingStep::select(['id', 'name', 'type', 'tour_id', 'url_matcher', 'scope', 'points', 'is_active']);

            return Datatables::of($steps)
                ->addColumn('action', function($row){
                    // ... (le code de cette fonction ne change pas)
                    $html = '<div class="btn-group">';
                    $html .= '<button type="button" class="btn btn-xs btn-primary btn-modal" data-href="' . action([\Modules\Help\Http\Controllers\OnboardingController::class, 'edit'], [$row->id]) . '" data-container=".onboarding_step_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</button>';
                    $html .= '<button type="button" class="btn btn-xs btn-danger delete_onboarding_step" data-href="' . action([\Modules\Help\Http\Controllers\OnboardingController::class, 'destroy'], [$row->id]) . '"><i class="glyphicon glyphicon-trash"></i> ' . __("messages.delete") . '</button>';
                    $html .= '</div>';
                    return $html;
                })
                ->editColumn('is_active', function($row) {
                    return $row->is_active ? '<span class="label bg-green">Actif</span>' : '<span class="label bg-red">Inactif</span>';
                })
                ->removeColumn('id')
                ->rawColumns(['action', 'is_active'])
                ->make(true);
        }

        // **NOUVEAU** : Si ce n'est pas une requête AJAX, on redirige vers la page principale de l'Académie.
        return redirect()->action([\Modules\Help\Http\Controllers\AcademyController::class, 'index']);
    }


    public function create()
    {
        return view('help::onboarding.create');
    }

    public function store(Request $request)
    {
        try {
            $input = $request->validate([
                'name' => 'required|string',
                'type' => 'required|in:flow,checklist,launcher',
                'tour_id' => 'required|string',
                'url_matcher' => 'required|string',
                'scope' => 'required|in:business,user',
                'points' => 'required|integer',
            ]);
            $input['is_active'] = !empty($request->input('is_active'));
            OnboardingStep::create($input);
            $output = ['success' => true, 'msg' => __("lang_v1.added_success")];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }

    public function edit($id)
    {
        $step = OnboardingStep::findOrFail($id);
        return view('help::onboarding.edit', compact('step'));
    }

    public function update(Request $request, $id)
    {
        try {
            $input = $request->validate([
                'name' => 'required|string',
                'type' => 'required|in:flow,checklist,launcher',
                'tour_id' => 'required|string',
                'url_matcher' => 'required|string',
                'scope' => 'required|in:business,user',
                'points' => 'required|integer',
            ]);
            $input['is_active'] = !empty($request->input('is_active'));

            $step = OnboardingStep::findOrFail($id);
            $step->update($input);

            $output = ['success' => true, 'msg' => __('lang_v1.updated_success')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
        }
        return $output;
    }

    public function destroy($id)
    {
        if (request()->ajax()) {
            try {
                OnboardingStep::findOrFail($id)->delete();
                $output = ['success' => true, 'msg' => __("lang_v1.deleted_success")];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
                $output = ['success' => false, 'msg' => __("messages.something_went_wrong")];
            }
            return $output;
        }
    }
}