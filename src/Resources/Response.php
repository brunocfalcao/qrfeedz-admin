<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use QRFeedz\Admin\Fields\BelongsToThrough;
use QRFeedz\Admin\Fields\FKLink;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Response extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\Response::class;

    public static $globallySearchable = false;

    public function title()
    {
        /**
         * If this response has question in english,
         * then show the english locale, else the first index.
         */
        $questionInstance = $this->questionInstance;

        return $questionInstance->captions->firstWhere('name', 'English')->pivot->caption ??
               $questionInstance->captions->first()->pivot->caption;
    }

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            BelongsToThrough::make('Test', function () {
                return $this->resource->questionInstance->pageInstance->questionnaire;
            }, Questionnaire::class),

            Text::make('Page instance type', function () {
                return $this->questionInstance->pageInstance->page->name;
            }),

            Text::make('Widget instance', function () {
                return $this->widgetInstance->widget->name;
            }),

            Text::make('Question caption', function () {
                $questionInstance = $this->questionInstance;

                return $questionInstance->captions->firstWhere('name', 'English')->pivot->caption ??
                       $questionInstance->captions->first()->pivot->caption;
            }),

            BelongsTo::make('Question instance', 'questionInstance', QuestionInstance::class)
                     ->hideFromIndex(),

            Text::make('Computed value(s)', function () {
                return array_values($this->value);
            }),

            KeyValue::make('Value dataset', 'value'),
        ];
    }
}
