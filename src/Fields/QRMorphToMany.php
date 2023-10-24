<?php

namespace QRFeedz\Admin\Fields;

use Laravel\Nova\Fields\MorphToMany;

class QRMorphToMany extends MorphToMany
{
    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute, $resource);

        $this->collapsedByDefault();
    }
}
