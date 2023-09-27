<?php

namespace QRFeedz\Admin\Resources;

use Brunocfalcao\NovaAddressFinder\NovaAddressFinder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasManyThrough;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\FKLink;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;

class Client extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\Client::class;

    public static $search = [
        'name', 'vat_number', 'address', 'locality', 'postal_code',
    ];

    public function title()
    {
        return $this->name.' ('.$this->locality.', '.$this->country->name.')';
    }

    public function subtitle()
    {
        $total = $this->questionnaires()->count();

        return $total.' '.Str::plural('questionnaire', $total);
    }

    public static function indexQuery(NovaRequest $request, $query)
    {
        $user = $request->user();
        $modelInstance = static::newModel();

        // Super admin? Done.
        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Affiliates can only see their own clients.
        if ($user->isAffiliate()) {
            return $query->where('user_affiliate_id', $user->id);
        }

        // Client admins can only see its own client.
        if ($user->isAuthorizedAs($modelInstance, 'client-admin')) {
            return $query->where('id', $user->client->id);
        }
    }

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            Text::make('Name')
                ->rules('required', 'max:255'),

            NovaAddressFinder::make('Address')
                             ->rules('max:255'),

            Text::make('Postal Code')
                ->rules('max:255'),

            Text::make('Locality')
                ->rules('max:255'),

            BelongsTo::make('Country', 'country')
                     ->withoutTrashed(),

            Text::make('VAT number')
                ->rules('required', 'max:255'),

            BelongsTo::make('Affiliate', 'affiliate', User::class)
                     ->nullable()
                     ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                         return $query->asAffiliate();
                     })
                     ->canSee(function ($request) {
                         return
                             // User is super admin. So we can add an affiliate.
                             $request->user()->isSuperAdmin() ||

                             // There is an affiliate for this client.
                             optional($this->affiliate)->exists();
                     })
                    ->readonly(function ($request) {
                        return ! $request->user()->isSuperAdmin();
                    })
                    ->withoutTrashed(),

            BelongsTo::make('Locale', 'locale', Locale::class)
                     ->withoutTrashed()
                     ->helpInfo('This is not only the default client locale but also a default locale for the questionnaires'),

            new Panel('Timestamps', $this->timestamps($request)),

            HasMany::make('Locations', 'locations', Location::class)
                   ->collapsedByDefault(),

            HasMany::make('Users', 'users', User::class)
                   ->collapsedByDefault(),

            BelongsToMany::make('Tags', 'tags', Tag::class)
                         ->collapsedByDefault(),

            HasManyThrough::make('Questionnaires', 'questionnaires', Questionnaire::class),

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
