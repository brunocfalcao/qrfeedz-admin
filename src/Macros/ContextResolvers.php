<?php

namespace QRFeedz\Admin\Macros;

use Laravel\Nova\Fields\Field;

Field::macro('resolveContextAware', function ($callbacks) {
    return $this->resolveUsing(function ($value, $resource, $attribute) use ($callbacks) {
        $url = request()->url();

        if (preg_match('#http://[^/]+/nova-api/[^/]+/\d+$#', $url) && isset($callbacks['detail'])) {
            return $callbacks['detail']($value, $resource, $attribute);
        }

        if (preg_match('#http://[^/]+/nova-api/[^/]+$#', $url) && isset($callbacks['index'])) {
            return $callbacks['index']($value, $resource, $attribute);
        }

        if (preg_match('#http://[^/]+/nova-api/[^/]+/creation-fields$#', $url) && isset($callbacks['create'])) {
            return $callbacks['create']($value, $resource, $attribute);
        }

        if (preg_match('#http://[^/]+/nova-api/[^/]+/\d+/update-fields$#', $url) && isset($callbacks['update'])) {
            return $callbacks['update']($value, $resource, $attribute);
        }

        return $value;
    });
});
