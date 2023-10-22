<?php

namespace QRFeedz\Admin\Fields;

use Laravel\Nova\Fields\MorphedByMany;

class QRMorphedByMany extends MorphedByMany
{
    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute, $resource);

        $this->collapsedByDefault();
    }
}
