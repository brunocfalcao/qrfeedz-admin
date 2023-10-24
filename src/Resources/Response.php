<?php

namespace QRFeedz\Admin\Resources;

use Brunocfalcao\LaravelNovaHelpers\Fields\BelongsToThrough;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Response extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\Response::class;

    public static $globallySearchable = false;

    public function title()
    {
        /**
         * If this response has question in english,
         * then show the english locale, else the first locale index.
         */
        $questionInstance = $this->questionInstance;

        return $questionInstance->captions->firstWhere('name', 'English')->pivot->caption ??
               $questionInstance->captions->first()->pivot->caption;
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Computed relationship.
            BelongsToThrough::make('Questionnaire', function () {
                return $this->resource
                            ->questionInstance
                            ->pageInstance
                            ->questionnaire;
            }, Questionnaire::class),

            // Computed relationship.
            BelongsToThrough::make('Page instance', function () {
                return $this->questionInstance
                            ->pageInstance;
            }, PageInstance::class),

            // Relationship ID: 28
            QRBelongsTo::make('Widget instance', 'widgetInstance', WidgetInstance::class),

            Text::make('Question caption', function () {
                $questionInstance = $this->questionInstance;

                return $questionInstance->captions->firstWhere('name', 'English')->pivot->caption ??
                       $questionInstance->captions->first()->pivot->caption;
            }),

            // Relationship ID: 19
            QRBelongsTo::make('Question instance', 'questionInstance', QuestionInstance::class)
                     ->hideFromIndex(),

            Text::make('Computed value(s)', function () {
                return array_values($this->value);
            }),

            KeyValue::make('Value dataset', 'value'),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
