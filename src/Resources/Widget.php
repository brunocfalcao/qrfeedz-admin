<?php

namespace QRFeedz\Admin\Resources;

use App\Nova\Resource;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use QRFeedz\Admin\Fields\QRID;

class Widget extends Resource
{
    public static $model = \QRFeedz\Cube\Models\Widget::class;

    public static $title = 'name';

    public static $search = [
        'name', 'description',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 22
            HasMany::make('Widget instances', 'widgetInstances', WidgetInstance::class),
        ];
    }
}
