<?php

namespace QRFeedz\Admin\Resources;

use Brunocfalcao\LaravelNovaHelpers\Fields\UUID;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Questionnaire extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\Questionnaire::class;

    public static $title = 'name';

    public static $search = [
        'name', 'title', 'description',
    ];

    public function subtitle()
    {
        return $this->location->locality.', '.$this->location->country->name;
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $user = $request->user();
        $modelInstance = static::newModel();

        // Super admin? Done.
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Affiliates can only see questionnaires from their own clients.
        if ($user->isAffiliate()) {
            return $query->join(
                'clients',
                'questionnaires.client_id',
                '=',
                'clients.id'
            )
             ->where('clients.id', $user->client_id);
        }

        // Client admins can only see its own questionnaires.
        if ($user->isAuthorizedAs($modelInstance, 'client-admin')) {
            return $query->where('client_id', $user->client->id);
        }
    }

    public static function softDeletes()
    {
        return false;
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            UUID::make(),

            Image::make('Logo', 'logo_file')
                 ->disableDownload()
                 ->acceptedTypes('image/*'),

            Text::make('Name')
                ->rules('required'),

            Text::make('Title')
                ->rules('required'),

            // Relationship ID: 26
            QRBelongsTo::make('Location', 'location', Location::class)
                     ->readonlyIfViaResource('questionnaires'),

            Textarea::make('Description'),

            Boolean::make('Active?', 'is_active'),

            Color::make('Primary color', 'color_primary'),

            Color::make('Secondary color', 'color_secondary'),

            new Panel('Timestamps', $this->timestamps($request)),

            KeyValue::make('Data', 'data'),

            DateTime::make('Starts at', 'starts_at')
                    ->hideFromIndex(),

            DateTime::make('Ends at', 'ends_at')
                     ->hideFromIndex(),

            // Relationship ID: 14
            QRBelongsTo::make('Default locale', 'locale', Locale::class)
                     ->withoutTrashed(),

            // Relationship ID: 6
            QRBelongsTo::make('Category', 'category', Category::class)
                     ->withoutTrashed(),

            // Relationship ID: 21
            QRHasMany::make('Page instances', 'pageInstances', PageInstance::class),

            // Relationship ID: 18
            HasOne::make('OpenAI Prompt', 'OpenAIPrompt', OpenAIPrompt::class),

            // Relationship ID: 13
            MorphToMany::make('Tags', 'tags', Tag::class)
                       ->nullable()
                       ->collapsedByDefault(),

            // Relationship ID: 31
            QRHasMany::make('Authorizations', 'authorizations', QuestionnaireAuthorization::class),
        ];
    }
}
