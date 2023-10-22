<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class ClientAuthorization extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\ClientAuthorization::class;

    public static $searchRelations = [
        'client' => ['name'],
        'user' => ['name'],
        'authorization' => ['name'],
    ];

    public function title()
    {
        return $this->authorization->name.
               ' for '.
               $this->user->name.
               ' ('.
               $this->client->name.
               ')';
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 34
            QRBelongsTo::make('Client', 'client', Client::class)
                     ->readonlyIfViaResource('clients'),

            // Relationship ID: 33
            QRBelongsTo::make('User', 'user', User::class)
                     ->readonlyIfViaResource('users'),

            // Relationship ID: 4
            QRBelongsTo::make('Authorization', 'authorization', Authorization::class)
                     ->readonlyIfViaResource('authorizations'),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
