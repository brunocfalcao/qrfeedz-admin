<?php

use Laravel\Nova\Fields\DateTime;

function timestamp_fields()
{
    return [
        DateTime::make('Created At')
            ->displayUsing(function ($value) {
                return $value->diffForHumans();
            })
            ->readonly(),

        DateTime::make('Updated At')
            ->displayUsing(function ($value) {
                return $value->diffForHumans();
            })
            ->readonly(),

        DateTime::make('Deleted At')
            ->displayUsing(function ($value) {
                return $value?->diffForHumans();
            })
            ->readonly(),
    ];
}
