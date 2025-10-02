<?php

namespace Modules\OpenAI\Exports;

use Modules\OpenAI\Entities\Archive;
use Maatwebsite\Excel\Concerns\{
    FromCollection,
    WithHeadings,
    WithMapping
};

class LongArticleExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * [Here we need to fetch data from data source]
     * @return [Database Object] [Here we are fetching data from archive table and also user table through Eloquent Relationship]
     */
    public function collection()
    {
        return Archive::with(['user', 'user.metas', 'metas'])->whereHas('metas', function($q) {
            $q->where('key', 'completed_step')->where('value', 3);
       })->whereType('long_article')->get();
    }

    /**
     * [Here we are putting Headings of The CSV]
     * @return [array] [Excel Headings]
     */
    public function headings(): array
    {
        return[
            'Title',
            'Content',
            'Generator',
            'Provider',
            'Model',
            'Created At'
        ];
    }
    /**
     * [By adding WithMapping you map the data that needs to be added as row. This way you have control over the actual source for each column. In case of using the Eloquent query builder]
     * @param $longArticle $longArticle  [It has archive table info of long article]
     * @return array [comma separated value will be produced]
     */

    public function map($longArticle): array
    {
        return[
            $longArticle->title,
            $longArticle->filtered_content ?? str_replace('**', '', $longArticle->content),
            $longArticle->user?->name,
            $longArticle->provider,
            $longArticle->generation_options['model'] ?? '',
            formatDate($longArticle->created_at) . ' ' . timeZoneGetTime($longArticle->created_at),
        ];
    }
}
