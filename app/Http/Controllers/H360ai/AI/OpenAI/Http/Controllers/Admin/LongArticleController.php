<?php

namespace Modules\OpenAI\Http\Controllers\Admin;

use Modules\OpenAI\DataTables\LongArticlesDataTable;
use Modules\OpenAI\Exports\LongArticleExport;
use Modules\OpenAI\Entities\ContentTypeMeta;
use Illuminate\Contracts\Support\Renderable;
use Modules\OpenAI\Entities\Archive;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Excel, DB;

class LongArticleController extends Controller
{
    public function index(LongArticlesDataTable $dataTable)
    {
        $data['users'] = User::status('Active')->get();
        $data['longArticleProviders'] = json_decode(ContentTypeMeta::where('key', 'long_article_providers')->pluck('value', 'key')->first());
        $data['longArticleModels'] = json_decode(ContentTypeMeta::where('key', 'long_article_models')->pluck('value', 'key')->first());
        return $dataTable->render('openai::admin.long-article.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $archives = Archive::find($id);

        if (! $archives) {
            \Session::flash('fail', __('The action that you are trying to perform is not available.'));
            return redirect()->back();
        }
        $data['archive'] = $archives->toArray();
        return view('openai::admin.long-article.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $data = ['status' => 'fail', 'message' => __('The action that you are trying to perform is not available.')];

        $archive = Archive::find($id);

        if (empty($archive)) {
            \Session::flash($data['status'], $data['message']);
            return redirect()->back();
        }

        $archive->title = $request->title;
        $archive->article_title = $request->title;
        $archive->filtered_content = $request->content;
        $archive->article_value = $request->content;
        $archive->save();

        \Session::flash('success', __('Long article update successfully!'));
        return redirect()->route('admin.long_article.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param Request $request
     * @return Renderable
     */
    public function destory($longArticleId)
    {
        $data = [ 'status' => 'failed', 'message' => __('The data you are looking for is not found')];

        $longArticle = Archive::where(['id' => $longArticleId, 'type' => 'long_article'])->first();

        $longArticleTitles = Archive::with('metas')->where(['parent_id' => $longArticleId, 'type' => 'long_article_title'])->get();
        $longArticleOutlines = Archive::with('metas')->where(['parent_id' => $longArticleId, 'type' => 'long_article_outline'])->get();
        $longArticleArticles = Archive::with('metas')->where(['parent_id' => $longArticleId, 'type' => 'long_article_article'])->get();

        if ($longArticle) {
            DB::beginTransaction();

            try {
                foreach ($longArticleTitles as  $longArticleTitle) {
                    $titleMetaData =  $longArticleTitle->toArray();
                    $titleMetaKeys = array_keys($titleMetaData['meta_data']);
                    $longArticleTitle->unsetMeta($titleMetaKeys);
                    $longArticleTitle->save();
                    $longArticleTitle->delete();
                }
    
                foreach ($longArticleOutlines as  $longArticleOutline) {
                    $outlineMetaData =  $longArticleOutline->toArray();
                    $outlineMetaKeys = array_keys($outlineMetaData['meta_data']);
                    $longArticleOutline->unsetMeta($outlineMetaKeys);
                    $longArticleOutline->save();
                    $longArticleOutline->delete();
                }
    
                foreach ($longArticleArticles as  $longArticleArticle) {
                    $articleMetaData =  $longArticleArticle->toArray();
                    $articleMetaKeys = array_keys($articleMetaData['meta_data']);
                    $longArticleArticle->unsetMeta($articleMetaKeys);
                    $longArticleArticle->save();
                    $longArticleArticle->delete();
                }
               
                $metaData =  $longArticle->toArray();
                $metaKeys = array_keys($metaData['meta_data']);
                $longArticle->unsetMeta($metaKeys);
                $longArticle->save();
                $longArticle->delete();
                DB::commit();
                $data = ['status' => 'success','message' => __('The :x has been successfully deleted', ['x' => 'article'])];

            } catch (Exception $e) {
                DB::rollBack();
                throw new Exception($e->getMessage());
            }
        }

        \Session::flash($data['status'], $data['message']);
        return redirect()->route('admin.long_article.index');
    }
    
    /**
     * Long article list csv
     *
     * @return void
     */
    public function csv()
    {
        return Excel::download(new LongArticleExport(), 'long_articles_' . time() . '.csv');
    }
    
    /**
     * Long article list pdf
     *
     * @return void
     */
    public function pdf()
    {
        $data['longArticles'] = Archive::with(['user', 'user.metas', 'metas'])->whereHas('metas', function($q) {
            $q->where('key', 'completed_step')->where('value', 3);
       })->whereType('long_article')->get();

        return printPDF($data, 'long_articles_' . time() . '.pdf', 'openai::admin.long-article.pdf', view('openai::admin.long-article.pdf', $data), 'pdf');
    }
}
