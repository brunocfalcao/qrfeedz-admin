<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\FKLink;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Fields\UUID;
use QRFeedz\Admin\Resources\User as UserResource;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Cube\Models\User;
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

    public static function availableForNavigation(Request $request)
    {
        $user = $request->user();

        return
            // The user is an affiliate.
            $user->isAffiliate() ||

            // The user is a super admin.
            $user->isSuperAdmin() ||

            // The user has at least one "client admin" authorization.
            $user->isAtLeastAuthorizedAs('client-admin');
    }

    public static function softDeletes()
    {
        return false;
    }

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            UUID::make(),

            Image::make('Logo', 'file_logo')
                 ->disableDownload()
                 ->acceptedTypes('image/*'),

            Text::make('Name')
                ->rules('required'),

            Text::make('Title')
                ->rules('required'),

            BelongsTo::make('Location', 'location', Location::class)
                     ->withoutTrashed(),

            Textarea::make('Description'),

            Boolean::make('Active?', 'is_active'),

            Color::make('Primary color', 'color_primary'),

            Color::make('Secondary color', 'color_secondary'),

            new Panel('Timestamps', $this->timestamps($request)),

            KeyValue::make('Data', 'data'),

            DateTime::make('Started at', 'starts_at')
                    ->hideFromIndex(),

            DateTime::make('Ending at', 'ends_at')
                     ->hideFromIndex(),

            BelongsTo::make('Default locale', 'locale', Locale::class)
                     ->withoutTrashed(),

            BelongsTo::make('Category', 'category', Category::class)
                     ->withoutTrashed(),

            HasMany::make('Page instances', 'pageInstances', PageInstance::class),

            HasOne::make('OpenAI Prompt', 'OpenAIPrompt', OpenAIPrompt::class),

            MorphToMany::make('Tags', 'tags', Tag::class)
                       ->nullable()
                       ->collapsedByDefault(),

            BelongsToMany::make('Authorizations')
                        ->fields(function ($request, $relatedModel) {
                            return [
                                Select::make('User', 'user_id')->options(
                                    User::all()->pluck('name', 'id')
                                )->onlyOnForms(),

                                FKLink::make('User', 'user_id', UserResource::class),
                            ];
                        }),
        ];
    }
}
