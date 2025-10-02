<?php

namespace Modules\OpenAI\Http\Controllers\Admin\v2;

use App\Models\User;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Modules\OpenAI\Entities\ChatBot;
use  Modules\OpenAI\Entities\Archive;
use Illuminate\Support\Facades\Session;
use Illuminate\Contracts\Support\Renderable;
use Modules\OpenAI\Exports\AiChatbotExport;
use Modules\OpenAI\Services\v2\ChatBotWidgetService;
use Modules\OpenAI\DataTables\AiChatbotDataTable;


class AiChatbotController extends Controller
{

    /**
     * Render the list of chatbots for admin.
     *
     * @param AiChatbotDataTable $dataTable
     * @return Renderable
     */
    public function index(AiChatbotDataTable $dataTable)
    {
        $data['users'] = User::get();
        return $dataTable->render('openai::admin.v2.ai-chatbot.index', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data = ['status' => 'fail', 'message' => __('The :x does not exist.', ['x' => __('Ai Chatbot')])];
        $data['chatBot'] = Chatbot::with(['metas','user', 'user.metas'])->whereType('widgetChatBot')->where('id', $id)->first();
        $data['languages'] = Language::where('status', 'Active')->get();

        if (empty($data['chatBot'])) {
            Session::flash($data['status'], $data['message']);
            return redirect()->back();
        }

        return view('openai::admin.v2.ai-chatbot.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        if (!is_numeric($id)) {
            abort(404);
        }
        
        $chatBot = Chatbot::with(['metas','user', 'user.metas'])->whereType('widgetChatBot')->where('id', $id)->first();

        if (empty($chatBot)) {
            $this->setSessionValue(['status' => 'fail', 'message' => __('The :x does not exist.', ['x' => __('Ai Chatbot')])]);
            return redirect()->back();
        }

        DB::beginTransaction();
        try {
            $allData = $request->only('name', 'description', 'language', 'status');

            foreach ($allData as $key => $value) {
                $chatBot->$key = $value;
            }
            $chatBot->save();

            DB::commit();
            $this->setSessionValue(['status' => 'success', 'message' => __('The :x has been successfully updated.', ['x' => __('Ai Chatbot')])]);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->setSessionValue(['status' => 'fail', 'message' => $e->getMessage()]);
        }

        return redirect()->back();
    }

    /**
     * Delete the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $data = ['status' => 'fail', 'message' => __('The :x does not exist.', ['x' => __('Ai Chatbot')])];

        if (!is_numeric($id)) {
            abort(404);
        }

        DB::beginTransaction();

        try {
            $chatBot = ChatBot::with(['metas','user', 'user.metas'])->whereType('widgetChatBot')->where('id', $id)->first();

            if (!$chatBot) {
                Session::flash($data['status'], $data['message']);
                return redirect()->back();
            }

            $chatBot->unsetMeta(array_keys($chatBot->getMeta()->toArray()));
            $chatBot->save();
            $chatBot->delete() ?: throw new Exception(__('Something went wrong, please try again.'));

            // Delete chatbot materials
            (new ChatBotWidgetService())->deleteMaterials($chatBot->code);

            DB::commit();
            $data = ['status' => 'success', 'message' => __('The :x has been successfully deleted.', ['x' => __('Ai Chatbot')])];

        } catch (Exception $e) {
            DB::rollBack();
            Session::flash($data['status'], $e->getMessage());
            return to_route('admin.features.ai_chatbot.index');
        }

        Session::flash($data['status'], $data['message']);
        return to_route('admin.features.ai_chatbot.index');
    }


    /**
     * Download the list of chatbots in a PDF file.
     *
     * @return mixed
     */
    public function pdf()
    {
        $data['chatBots'] = ChatBot::with(['metas','user', 'user.metas'])->whereType('widgetChatBot')
            ->get()
            ->map(function ($chatBot) {
                $code = $chatBot->code;
        
                // Fetch conversation IDs and total visitors in a single query
                $archives = Archive::whereType('chatbot_chat')
                    ->whereHas('metas', function ($query) use ($code) {
                        $query->where('key', 'chatbot_code')->where('value', $code);
                    })
                    ->with(['metas' => function ($query) {
                        $query->whereIn('key', ['chatbot_code', 'visitor_id']);
                    }])
                    ->get();
        
                // Unique conversation IDs
                $conversationIds = $archives->pluck('id')->unique()->toArray();
        
                // Unique visitor IDs
                $totalVisitors = $archives->flatMap(function ($archive) {
                    return $archive->metas->where('key', 'visitor_id')->pluck('value');
                })->unique()->count();
        
                return [
                    'chatBot' => $chatBot,
                    'conversationCount' => count($conversationIds),
                    'totalVisitors' => $totalVisitors,
                ];
            });

        return printPDF($data, 'ai_chatbot_list_' . time() . '.pdf', 'openai::admin.v2.ai-chatbot.ai_chatbot_list_pdf', view('openai::admin.v2.ai-chatbot.ai_chatbot_list_pdf', $data), 'pdf');
    }

    /**
     * Ai Chatbot list csv
     *
     * @return mixed
     */
    public function csv()
    {
        return Excel::download(new AiChatbotExport(), 'chat_assistant_list_' . time() . '.csv');
    }
}
