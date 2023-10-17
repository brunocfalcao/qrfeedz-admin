<?php

namespace QRFeedz\Admin\Fields;

use Laravel\Nova\Fields\BelongsTo;

class QRBelongsTo extends BelongsTo
{
    public function __construct($name, $attribute = null, $resource = null)
    {
        parent::__construct($name, $attribute, $resource);

        $this->withoutTrashed();
    }
}
