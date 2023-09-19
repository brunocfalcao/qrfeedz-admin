<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\FKLink;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Location extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\Location::class;

    public static $title = 'name';

    public static $search = [
        'name', 'address', 'postal_code', 'locality',
    ];

    public function subtitle()
    {
        return $this->client->name;
    }

    public static function availableForNavigation(Request $request)
    {
        $user = $request->user();

        return
            // The user is allowed admin access (client-admin, etc).
            $user->isAllowedAdminAccess();
    }

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            BelongsTo::make('Client', 'client', Client::class),

            Text::make('Name')
                ->rules('required', 'max:255'),

            Text::make('Address')
                ->rules('required', 'max:255'),

            Text::make('Postal Code')
                ->rules('required', 'max:255'),

            Text::make('Locality')
                ->rules('required', 'max:255'),

            BelongsTo::make('Country', 'country')
                     ->withoutTrashed(),

            new Panel('Timestamps', $this->timestamps($request)),

            HasMany::make('Questionnaires', 'questionnaires', Questionnaire::class),

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
