<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Location extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\Location::class;

    public static $title = 'name';

    public static $search = [
        'name', 'address', 'postal_code', 'locality',
    ];

    public function subtitle()
    {
        return $this->client->name;
    }

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            BelongsTo::make('Client', 'client', Client::class),

            Text::make('Name')
                ->rules('required', 'max:255'),

            Text::make('Address')
                ->rules('required', 'max:255'),

            Text::make('Postal Code')
                ->rules('required', 'max:255'),

            Text::make('Locality')
                ->rules('required', 'max:255'),

            BelongsTo::make('Country', 'country')
                     ->withoutTrashed(),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
