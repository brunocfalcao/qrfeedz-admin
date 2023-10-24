<?php

namespace QRFeedz\Admin\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;

class Widget extends Resource
{
    public static $model = \QRFeedz\Cube\Models\Widget::class;

    public static $title = 'name';

    public static $globallySearchable = false;

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 22
            QRHasMany::make('Widget instances', 'widgetInstances', WidgetInstance::class),
        ];
    }
}
