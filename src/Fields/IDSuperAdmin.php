<?php

namespace QRFeedz\Admin\Fields;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;

class IDSuperAdmin extends ID
{
    public function __construct($name = 'ID', $attribute = 'id', $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->sortable();

        $this->canSee(function (NovaRequest $request) {
            return $request->user()->isSuperAdmin();
        });
    }
}
