<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class WidgetInstance extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\WidgetInstance::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 20
            QRBelongsTo::make('Question instance', 'questionInstance', QuestionInstance::class),

            // Relationship ID: 22
            QRBelongsTo::make('Widget', 'widget', Widget::class),

            // Relationship ID: 10
            HasMany::make('Child Widget instances', 'childWidgetInstances', WidgetInstance::class),

            // Relationship ID: 35
            QRBelongsTo::make('Parent Widget instance', 'parentWidgetInstance', WidgetInstance::class),

            // Relationship ID: 28
            HasMany::make('Responses', 'responses', Response::class),

            // Relationship ID: 23
            MorphToMany::make('Captions', 'captions', Locale::class),
        ];
    }
}
