<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class UserAuthorization extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\UserAuthorization::class;

    public static $search = [
    ];

    public static $title = 'authorizable_type';

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            Text::make('authorizable_type'),

            Text::make('authorizable_id'),

            QRBelongsTo::make('Authorization', 'authorization', Authorization::class),

            QRBelongsTo::make('User', 'user', User::class),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
