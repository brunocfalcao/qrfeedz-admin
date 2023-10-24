<?php

namespace QRFeedz\Admin\Resources;

use Brunocfalcao\LaravelNovaHelpers\Fields\UUID;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class PageInstance extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\PageInstance::class;

    public static $globallySearchable = false;

    public static $searchRelations = [
        'questionnaire' => ['name'],
        'Page' => ['name'],
    ];

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

            UUID::make(),

            // Relationship ID: 21
            QRBelongsTo::make('Questionnaire', 'questionnaire', Questionnaire::class),

            Text::make('Name')
                ->rules('required'),

            // Relationship ID: 16
            QRBelongsTo::make('Page', 'page', Page::class),

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

            // Relationship ID: 24
            QRHasMany::make('Question instances', 'questionInstances', QuestionInstance::class),
        ];
    }
}
