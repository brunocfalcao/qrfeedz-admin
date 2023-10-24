<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;
use QRFeedz\LaravelNovaHelpers\Fields\BelongsToThrough;

class Response extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\Response::class;

    public static $globallySearchable = false;

    public static $searchRelations = [
        'locale' => ['name'],
        'country' => ['name'],
        'affiliate' => ['name'],
    ];

    public function title()
    {
        /**
         * If this response has question in english,
         * then show the english locale, else the first locale index.
         */
        $questionInstance = $this->questionInstance;

        return $questionInstance->captions->firstWhere('name', 'English')->pivot->caption ??
               $questionInstance->captions->first()->pivot->caption;
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $user = $request->user();

        // Admin-like? Return all responses.
        if ($user->isSystemAdminLike()) {
            return $query;
        }

        return $query
            ->upTo('question_instances')
            ->upTo('page_instances')
            ->upTo('questionnaires')
            ->upTo('locations')
            ->upTo('clients') // We need to reach here so we can attach the users.
            ->bring('users')
            ->when($user->isAtLeastAuthorizedAs('client-admin'), function ($query) use ($user) {
                // Obtain the clients where the user is client-admin.
                $query->whereIn(
                    'clients.id',
                    $user->authorizationsAs('client-admin')
                         ->pluck('model_id')
                );
            })
            ->when($user->isAtLeastAuthorizedAs('location-admin'), function ($query) use ($user) {
                // Obtain the clients where the user is location-admin.
                $query->whereIn(
                    'locations.id',
                    $user->authorizationsAs('location-admin')
                         ->pluck('model_id')
                );
            })
            ->where('users.id', $user->id);
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Computed relationship.
            BelongsToThrough::make('Questionnaire', function () {
                return $this->resource
                            ->questionInstance
                            ->pageInstance
                            ->questionnaire;
            }, Questionnaire::class),

            // Computed relationship.
            BelongsToThrough::make('Page instance', function () {
                return $this->questionInstance
                            ->pageInstance;
            }, PageInstance::class),

            // Relationship ID: 28
            QRBelongsTo::make('Widget instance', 'widgetInstance', WidgetInstance::class),

            Text::make('Question caption', function () {
                $questionInstance = $this->questionInstance;

                return $questionInstance->captions->firstWhere('name', 'English')->pivot->caption ??
                       $questionInstance->captions->first()->pivot->caption;
            }),

            // Relationship ID: 19
            QRBelongsTo::make('Question instance', 'questionInstance', QuestionInstance::class)
                     ->hideFromIndex(),

            Text::make('Computed value(s)', function () {
                return array_values($this->value);
            }),

            KeyValue::make('Value dataset', 'value'),

            new Panel('Timestamps', $this->timestamps($request)),
        ];
    }
}
