<?php

namespace QRFeedz\Admin\Fields;

use Laravel\Nova\Fields\Text;

class HumanDateTime extends Text
{
    /**
     * Create a new HumanDateTime field.
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
             ->resolveContextAware([
                 'index' => function ($value) {
                     return $value ? \Carbon\Carbon::parse($value)->diffForHumans() : null;
                 },

                 'detail' => function ($value) {
                     return $value ? \Carbon\Carbon::parse($value)->diffForHumans() : null;
                 },

                 'create' => function ($value) {
                     return $value ? \Carbon\Carbon::parse($value)->diffForHumans() : null;
                 },

                 'update' => function ($value) {
                     return $value ? \Carbon\Carbon::parse($value)->diffForHumans() : null;
                 },
             ]);
    }
}
