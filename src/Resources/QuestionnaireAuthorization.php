<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class QuestionnaireAuthorization extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\QuestionnaireAuthorization::class;

    public static $title = 'id';

    public static $searchRelations = [
        'questionnaire' => ['name'],
        'user' => ['name'],
        'authorization' => ['name'],
    ];

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 31
            BelongsTo::make('Questionnaire', 'questionnaire', Questionnaire::class)
                     ->readonlyIfViaResource('questionnaires'),

            // Relationship ID: 32
            BelongsTo::make('User', 'user', User::class)
                     ->readonlyIfViaResource('users'),

            // Relationship ID: 29
            BelongsTo::make('Authorization', 'authorization', Authorization::class)
                     ->readonlyIfViaResource('authorizations'),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
