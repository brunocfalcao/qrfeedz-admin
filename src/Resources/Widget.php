<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultAscPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Widget extends QRFeedzResource
{
    use DefaultAscPKSorting;

    public static $model = \QRFeedz\Cube\Models\Widget::class;

    public static $title = 'name';

    public static $globallySearchable = false;

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 22
            QRHasMany::make('Widget instances', 'widgetInstances', WidgetInstance::class),

            // Relationship ID: 23
            MorphToMany::make('Captions', 'captions', Locale::class),

            new Panel('Last data activity', $this->timestamps($request)),
        ];
    }
}
