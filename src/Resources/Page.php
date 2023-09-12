<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\Canonical;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Page extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\Page::class;

    public static $title = 'name';

    public static $search = [
        'name', 'description',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            Text::make('Name')
                ->rules('required'),

            Canonical::make()
                ->rules('required'),

            Text::make('Description')
                ->rules('required')
                ->helpWarning('An easy way to remember what is page is used for'),

            Text::make('View component namespace', 'view_component_namespace')
                ->rules('required')
                ->helpWarning('Cascades to the page instance, if not defined there'),

            new Panel('Timestamps', $this->timestamps($request)),

            HasMany::make('Page instances', 'pageInstances', PageInstance::class),
        ];
    }
}
