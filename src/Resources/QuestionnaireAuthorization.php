<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class QuestionnaireAuthorization extends QRFeedzResource
{
    public static $model = \QRFeedz\Cube\Models\QuestionnaireAuthorization::class;

    public static $searchRelations = [
        'questionnaire' => ['name'],
        'user' => ['name'],
        'authorization' => ['name'],
    ];

    public function title()
    {
        return $this->authorization->name.
               ' for '.
               $this->user->name.
               ' ('.
               $this->questionnaire->name.
               ')';
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 31
            QRBelongsTo::make('Questionnaire', 'questionnaire', Questionnaire::class),

            // Relationship ID: 32
            QRBelongsTo::make('User', 'user', User::class),

            // Relationship ID: 29
            QRBelongsTo::make('Authorization', 'authorization', Authorization::class),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
