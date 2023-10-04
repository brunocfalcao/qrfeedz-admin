<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class PageInstance extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\PageInstance::class;

    public static $search = [];

    public function title()
    {
        return 'Page '.$this->page->name.' instance';
    }

    public static function defaultOrderings($query)
    {
        return $query->orderBy('questionnaire_id', 'desc')
                     ->orderBy('index', 'asc');
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            QRUUID::make(),

            BelongsTo::make('Questionnaire', 'questionnaire', Questionnaire::class)
                     ->withoutTrashed(),

            Text::make('Name')
                ->rules('required'),

            BelongsTo::make('Page', 'page', Page::class)
                     ->withoutTrashed(),

            Text::make('View Component')
                ->readonly(),

            Text::make('View component override', 'view_component_override')
                ->onlyOnForms(),

            Number::make('Index'),

            Text::make('Group'),

            Text::make('# Questions', function () {
                return $this->questionInstances->count();
            }),

            KeyValue::make('Data'),

            new Panel('Timestamps', $this->timestamps($request)),

            HasMany::make('Question instances', 'questionInstances', QuestionInstance::class)
                   ->collapsedByDefault(),
        ];
    }
}
