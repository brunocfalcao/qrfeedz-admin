<?php

namespace QRFeedz\Admin\Fields;

use Laravel\Nova\Fields\HasMany;

class QRHasMany extends HasMany
{
    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute, $resource);

        $this->collapsedByDefault();
    }
}
