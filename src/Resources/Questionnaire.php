<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\KeyValue;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\FKLink;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Fields\UUID;
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
        return request()->user()->isSuperAdmin();
    }

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            UUID::make(),

            Text::make('Name')
                ->rules('required'),

            Text::make('Title')
                ->rules('required'),

            BelongsTo::make('Client', 'client', Client::class)
                     ->withoutTrashed(),

            BelongsTo::make('Location', 'location', Location::class)
                     ->withoutTrashed(),

            Textarea::make('Description'),

            Image::make('Logo', 'file_logo'),

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

            MorphToMany::make('Authorizations', 'authorizations', Authorization::class)
                       ->fields(fn () => [
                           FKLink::make('User', 'user_id', User::class)
                                 ->sortable(),
                       ])
                       ->nullable()
                       ->collapsedByDefault(),
        ];
    }
}
