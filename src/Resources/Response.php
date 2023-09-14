<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Http\Requests\NovaRequest;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Response extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\Response::class;

    public static $title = 'id';

    public static $globallySearchable = false;

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

        ];
    }
}
