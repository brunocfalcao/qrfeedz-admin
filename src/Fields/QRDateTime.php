<?php

namespace QRFeedz\Admin\Fields;

use Laravel\Nova\Fields\Text;

class QRDateTime extends Text
{
    /**
     * Create a new QRDateTime field.
     *
     * @param  string|null  $name
     * @param  string|null  $attribute
     * @return void
     */
    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->readonly()
             ->hideFromIndex()
             ->hideWhenCreating()
             ->resolveUsing(function ($value) {
                 return $value ? \Carbon\Carbon::parse($value)->diffForHumans() : null;
             });
    }
}
