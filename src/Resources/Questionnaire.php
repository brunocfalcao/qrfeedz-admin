<?php

namespace QRFeedz\Admin\Resources;

use Brunocfalcao\LaravelNovaHelpers\Fields\UUID;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRHasMany;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Fields\QRImage;
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

    public static $searchRelations = [
        'location' => ['name'],
        'locale' => ['name'],
        'category' => ['name'],
    ];

    public function subtitle()
    {
        return $this->location->locality.', '.$this->location->country->name;
    }

    public static function relatableQuery(NovaRequest $request, $query)
    {
        $user = $request->user();

        if ($user->isSystemAdminLike()) {
            return $query;
        }

        // The user can see filtered questionnaires by his client.
        $query->upTo('questionnaires')
              ->upTo('locations')
              ->upTo('clients')
              ->bring('users');

        $clients = ClientAuthorizations::getWhere('user_id', $user->id);
        $questionnaires = QuestionnaireAuthorization::getWhere('user_id', $user->id);

        if ($clients) {
            $query->whereIn('clients.id', $clients->pluck('id'));
        }

        if ($questionnaires) {
            $query->whereIn('questionnaires.id', $questionnaires->pluck('id'));
        }

        if ($user->isAffiliate()) {
            $affiliatedClients = Client::getWhere('affiliate_user_id', $user->id);
            $query->whereIn('clients.id', $affiliatedClients->pluck('id'));
        }

        return $query;
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            UUID::make(),

            QRImage::make('Logo', 'logo_file'),

            Text::make('Name')
                ->rules('required'),

            Text::make('Title')
                ->rules('required'),

            // Relationship ID: 26
            QRBelongsTo::make('Location', 'location', Location::class),

            Textarea::make('Description'),

            Boolean::make('Has Splash Screen?', 'has_splash_screen')
                   ->hideFromIndex(),

            Boolean::make('Active?', 'is_active'),

            Color::make('Primary color', 'color_primary')
                 ->hideFromIndex(),

            Color::make('Secondary color', 'color_secondary')
                 ->hideFromIndex(),

            new Panel('Timestamps', $this->timestamps($request)),

            KeyValue::make('Data', 'data'),

            DateTime::make('Starts at', 'starts_at')
                    ->hideFromIndex(),

            DateTime::make('Ends at', 'ends_at')
                     ->hideFromIndex(),

            // Relationship ID: 14
            QRBelongsTo::make('Default locale', 'locale', Locale::class),

            // Relationship ID: 6
            QRBelongsTo::make('Category', 'category', Category::class),

            // Relationship ID: 24
            QRHasMany::make('Question instances', 'questionInstances', QuestionInstance::class)
                   ->nullable(),

            // Relationship ID: 18
            HasOne::make('OpenAI Prompt', 'openAIPrompt', OpenAIPrompt::class)
                  ->exceptOnForms(),

            // Relationship ID: 13
            QRHasMany::make('Tags', 'tags', Tag::class)
                   ->nullable(),

            // Relationship ID: 31
            QRHasMany::make('Authorizations', 'authorizations', QuestionnaireAuthorization::class),
        ];
    }
}
