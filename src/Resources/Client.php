<?php

namespace QRFeedz\Admin\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\HasManyThrough;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\MorphToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\BelongsToStrict;
use QRFeedz\Admin\Fields\IDSuperAdmin;
use QRFeedz\Admin\Resources\Country as CountryResource;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Cube\Models\Country;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;
use Trinityrank\GoogleMapWithAutocomplete\TRAddress;
use Trinityrank\GoogleMapWithAutocomplete\TRCity;
use Trinityrank\GoogleMapWithAutocomplete\TRCountry;
use Trinityrank\GoogleMapWithAutocomplete\TRMap;
use Trinityrank\GoogleMapWithAutocomplete\TRZipCode;

class Client extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\Client::class;

    public static $search = [
        'name', 'vat_number', 'address', 'city', 'postal_code',
    ];

    public function title()
    {
        return $this->name.' ('.$this->city.', '.$this->country->name.')';
    }

    public function subtitle()
    {
        $total = $this->questionnaires()->count();

        return $total.' '.Str::plural('questionnaire', $total);
    }

    public function fields(NovaRequest $request)
    {
        return [
            IDSuperAdmin::make(),

            Image::make('Logo', 'file_logo')
                 ->disableDownload()
                 ->acceptedTypes('image/*'),

            Text::make('Name')
                ->rules('required', 'max:255'),

            TRAddress::make('Address')
                     ->rules('required'),

            TRZipCode::make('Zip Code', 'postal_code')
                     ->hideFromIndex(),

            TRCity::make('City')
                  ->hideFromIndex(),

            BelongsToStrict::make('Country', 'country', CountryResource::class)
                           ->withoutTrashed()
                           ->exceptOnForms(),

            TRCountry::make('Country', 'country_id')
                     ->resolveUsing(function ($value) {
                         return Country::firstWhere('id', $value)?->name;
                     })
                     ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                         $model->{$attribute} = Country::firstWhere('name', $request->input($attribute))->id;
                     })
                     ->onlyOnForms()
                     ->rules('required'),

            TRMap::make('Map')
                 ->hideLatitude()
                 ->hideLongitude()
                 ->onlyOnForms(),

            Text::make('VAT number')
                ->rules('max:255'),

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
                           BelongsTo::make('User', 'user', AuthorizationUser::class)
                                 ->sortable(),
                       ])
                       ->nullable()
                       ->canSee(function ($request) {
                           return $request->user()->isSuperAdmin();
                       })
                       ->collapsedByDefault(),
        ];
    }
}
