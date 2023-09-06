<?php

namespace QRFeedz\Admin\Traits;

/**
 * Sorts the resource in the index view by it's default PK (ascending).
 */
trait DefaultAscPKSorting
{
    public static function defaultOrderings($query)
    {
        return $query->orderBy($query->getModel()->getKeyName(), 'asc');
    }
}
