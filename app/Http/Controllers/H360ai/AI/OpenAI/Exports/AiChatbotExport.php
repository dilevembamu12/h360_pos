<?php
namespace Modules\OpenAI\Exports;

use Modules\OpenAI\Entities\{
    ChatBot,
    Archive
};
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping
};

class AiChatbotExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * [Here we need to fetch data from data source]
     * @return [Database Object] [Here we are fetching data from User table and also role table through Eloquent Relationship]
     */
    public function collection(): collection
    {
        return ChatBot::with(['metas','user', 'user.metas'])->whereType('widgetChatBot')
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
    }

    /**
     * [Here we are putting Headings of The CSV]
     * @return [array] [Excel Headings]
     */
    public function headings(): array
    {
        return[
            'Name',
            'Description',
            'Total Conversations',
            'Total Visitors',
            'Status',
            'Created At'
        ];
    }
    /**
     * [By adding WithMapping you map the data that needs to be added as row. This way you have control over the actual source for each column. In case of using the Eloquent query builder]
     * @param [object] $userList [It has users table info and roles table info]
     * @return [array]            [comma separated value will be produced]
     */
    public function map($data): array
    {
        $chatBot = $data['chatBot'];
        return[
            $chatBot->name,
            $chatBot->description,
            $data['conversationCount'],
            $data['totalVisitors'],
            $chatBot->status,
            timeZoneFormatDate($chatBot->created_at),

        ];
    }
}
