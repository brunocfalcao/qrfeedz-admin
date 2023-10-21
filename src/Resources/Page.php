<?php

namespace QRFeedz\Admin\Resources;

use Brunocfalcao\LaravelNovaHelpers\Fields\Canonical;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Page extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\Page::class;

    public static $title = 'name';

    public static $search = [
        'name', 'description',
    ];

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            Text::make('Name')
                ->rules('required'),

            Canonical::make()
                ->rules('required'),

            Text::make('Description')
                ->rules('required')
                ->helpInfo('An easy way to remember what is page is used for'),

            Text::make('View component namespace', 'view_component_namespace')
                ->rules('required')
                ->helpInfo('Cascades to the page instance, if not defined there'),

            new Panel('Timestamps', $this->timestamps($request)),

            // Relationship ID: 16
            QRHasMany::make('Page instances', 'pageInstances', PageInstance::class),
        ];
    }
}
