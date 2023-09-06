<?php

namespace QRFeedz\Admin\Macros;

use Illuminate\Support\Str;
use Laravel\Nova\Fields\Field;

Field::macro('charLimit', function ($limit) {
    return $this->displayUsing(fn ($value) => Str::limit($value, $limit, '...'));
});
