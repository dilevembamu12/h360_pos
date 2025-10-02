<?php

/**
 * @package UseCaseCategoriesController
 * @author TechVillage <support@techvill.org>
 * @contributor Soumik Datta <soumik.techvill@gmail.com>
 * @created 22-02-2023
 */

namespace Modules\OpenAI\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\OpenAI\Entities\UseCaseCategory;
use Modules\OpenAI\DataTables\UseCaseCategoryDataTable;
use Modules\OpenAI\Transformers\UseCaseCategoryResource;
use Modules\OpenAI\Exports\UseCaseCategoryExport;

class UseCaseCategoriesController extends Controller
{
    /**
     * Company Setting Constructor
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        //this middleware should be for POST request only
        if ($request->isMethod('post')) {
            $this->middleware('checkForDemoMode')->only('create', 'edit');
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  UseCaseCategoryDataTable  $dataTable
     * @return mixed
     */
    public function index(UseCaseCategoryDataTable $dataTable)
    {
        return $dataTable->render('openai::admin.use-case-category.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        if (request()->isMethod('POST')) {
            $this->store();
            return to_route('admin.use_case.category.list');
        }

        return view('openai::admin.use-case-category.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return mixed
     */
    public function store()
    {
        $response = ['status' => 'fail', 'message' => __('The :x has not been saved. Please try again', ['x' => __('Use case')])];
        $request = app('Modules\OpenAI\Http\Requests\UseCaseCategoryRequest')->safe();

        if (UseCaseCategory::create($request->all())) {
            $response = ['status' => 'success', 'message' => __('Use case category created successfully!')];
        }

        $this->setSessionValue($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return mixed
     */
    public function edit($id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }

        if (request()->isMethod('POST')) {
            $this->update($id);
            return to_route('admin.use_case.category.list');
        }

        if ($useCaseCategory = UseCaseCategory::find($id)) {
            return view('openai::admin.use-case-category.edit', compact('useCaseCategory'));
        }

        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int|string  $id
     * @return mixed
     */
    public function update($id)
    {
        try {
            $request = app('Modules\OpenAI\Http\Requests\UseCaseCategoryRequest')->safe();
            $useCaseCategory = UseCaseCategory::find($id);

            if (!$useCaseCategory) {
                throw new \Exception(__('Use Case category not found'));
            }

            if ($useCaseCategory->slug == 'others') {
                unset($request['slug']);
            }

            $useCaseCategory->update($request->all());

            $this->setSessionValue(['status' => 'success', 'message' => __('Use case category updated successfully!')]);
        } catch (\Exception $e) {
            $this->setSessionValue(['status' => 'fail', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int|string  $id
     * @return mixed
     */
    public function destroy($id)
    {

        if (!is_numeric($id)) {
            abort(404);
        }

        $useCaseCategory = UseCaseCategory::find($id);

        if ($useCaseCategory->slug === "others") {

            return redirect()->back()->withFail(__('Invalid Request!'));

        }

        if ($useCaseCategory) {
            try {
                DB::beginTransaction();

                $id = UseCaseCategory::where('slug', 'others')->value('id');
                $useCaseCategory->useCases()->update(['use_case_category_id' => $id]);
                $useCaseCategory->delete();

                DB::commit();
                $response = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Use Case Category')])];
            } catch (\Exception $e) {
                DB::rollBack();
                $response = ['status' => 'fail', 'message' => $e->getMessage()];
            }

            $this->setSessionValue($response);
            return to_route('admin.use_case.category.list');
        }

        abort(404);
    }

    /**
     * Find users
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function searchCategory(Request $request)
    {
        $users = UseCaseCategory::whereLike('name', $request->q)->limit(10)->get();

        return UseCaseCategoryResource::collection($users);
    }

    /**
     * User list pdf
     *
     * @return mixed
     */
    public function pdf()
    {
        $data['useCaseCategories'] = UseCaseCategory::get();

        return printPDF($data, 'use_case_category_list_' . time() . '.pdf', 'openai::admin.use-case-category.use_case_category_list_pdf', view('openai::admin.use-case-category.use_case_category_list_pdf', $data), 'pdf');
    }

    /**
     * User list csv
     *
     * @return mixed
     */
    public function csv()
    {
        return Excel::download(new UseCaseCategoryExport(), 'use_case_category_list_' . time() . '.csv');
    }
}
