<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\BelongsToThrough;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Resources\Client as ClientResource;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class UserAuthorization extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\UserAuthorization::class;

    public static $searchRelations = [
        'user' => ['name', 'email'],
    ];

    public static $search = [
    ];

    public static $title = 'authorizable_type';

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            Text::make('Class', function ($model) {
                $instance = (new $model->authorizable_type)->find($model->authorizable_id);
                if (! $instance) {
                    return null;
                }
                $url = url('/resources/'.strtolower(Str::plural(class_basename($model->authorizable_type))).'/'.$model->authorizable_id);

                return "<a class='link-default' href='{$url}'>{$instance->name}</a>";
            })->asHtml(),

            QRBelongsTo::make('Authorization', 'authorization', Authorization::class),

            QRBelongsTo::make('User', 'user', User::class),

            BelongsToThrough::make('Client', function () {
                return $this->user
                            ->client;
            }, ClientResource::class),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
