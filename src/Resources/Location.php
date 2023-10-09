<?php

namespace QRFeedz\Admin\Resources;

use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Fields\QRID;
use QRFeedz\Admin\Resources\Country as CountryResource;
use QRFeedz\Admin\Traits\DefaultDescPKSorting;
use QRFeedz\Cube\Models\Country;
use QRFeedz\Foundation\Abstracts\QRFeedzResource;
use Trinityrank\GoogleMapWithAutocomplete\TRAddress;
use Trinityrank\GoogleMapWithAutocomplete\TRCity;
use Trinityrank\GoogleMapWithAutocomplete\TRCountry;
use Trinityrank\GoogleMapWithAutocomplete\TRMap;
use Trinityrank\GoogleMapWithAutocomplete\TRZipCode;

class Location extends QRFeedzResource
{
    use DefaultDescPKSorting;

    public static $model = \QRFeedz\Cube\Models\Location::class;

    public static $title = 'name';

    public static $search = [
        'name', 'address', 'postal_code', 'city',
    ];

    public function subtitle()
    {
        return $this->client->name;
    }

    public function fields(NovaRequest $request)
    {
        return [
            QRID::make(),

            // Relationship ID: 5
            QRBelongsTo::make('Client', 'client', Client::class)
                       ->readonlyIfViaResource('locations'),

            Text::make('Name')
                ->rules('required', 'max:255'),

            TRAddress::make('Address')
                     ->rules('required'),

            TRZipCode::make('Zip Code', 'postal_code')
                     ->hideFromIndex(),

            TRCity::make('City')
                  ->hideFromIndex(),

            // Relationship ID: 25
            QRBelongsTo::make('Country', 'country', CountryResource::class)
                     ->readonlyIfViaResource('locations')
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

            new Panel('Timestamps', $this->timestamps($request)),

            // Relationship ID: 26
            HasMany::make('Questionnaires', 'questionnaires', Questionnaire::class),
        ];
    }
}
