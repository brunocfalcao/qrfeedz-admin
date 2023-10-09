<?php

namespace QRFeedz\Admin\Traits;

use QRFeedz\Admin\Fields\QRBelongsTo;
use QRFeedz\Admin\Resources\Country as CountryResource;
use QRFeedz\Cube\Models\Country;
use Trinityrank\GoogleMapWithAutocomplete\TRAddress;
use Trinityrank\GoogleMapWithAutocomplete\TRCity;
use Trinityrank\GoogleMapWithAutocomplete\TRCountry;
use Trinityrank\GoogleMapWithAutocomplete\TRMap;
use Trinityrank\GoogleMapWithAutocomplete\TRZipCode;

trait HasAddressFields
{
    protected function addressFields($viaResource = [], $fieldsRequired = false)
    {
        $rule = $fieldsRequired ? 'required' : null;

        return [
            TRAddress::make('Address')
                     ->rules($rule),

            TRZipCode::make('Zip Code', 'postal_code')
                     ->hideFromIndex(),

            TRCity::make('City')
                  ->hideFromIndex(),

            // Relationship ID: 9
            QRBelongsTo::make('Country', 'country', CountryResource::class)
                       ->readonlyIfViaResource($viaResource)
                       ->exceptOnForms(),

            TRCountry::make('Country', 'country_id')
                     ->resolveUsing(function ($value) {
                         return Country::firstWhere('id', $value)?->name;
                     })
                     ->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
                         $model->{$attribute} = Country::firstWhere('name', $request->input($attribute))->id;
                     })
                     ->onlyOnForms()
                     ->rules($rule),

            TRMap::make('Map')
                 ->hideLatitude()
                 ->hideLongitude()
                 ->onlyOnForms(),
        ];
    }
}
