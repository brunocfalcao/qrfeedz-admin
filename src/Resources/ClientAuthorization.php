<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class ClientAuthorization extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\ClientAuthorization::class;

    public static $title = 'id';

    public static $searchRelations = [
        'client' => ['name'],
        'user' => ['name'],
        'authorization' => ['name'],
    ];

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 34
            BelongsTo::make('Client', 'client', Client::class)
                     ->readonlyIfViaResource('clients'),

            // Relationship ID: 33
            BelongsTo::make('User', 'user', User::class)
                     ->readonlyIfViaResource('users'),

            // Relationship ID: 4
            BelongsTo::make('Authorization', 'authorization', Authorization::class)
                     ->readonlyIfViaResource('authorizations'),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
