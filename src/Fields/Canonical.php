<?php

namespace QRFeedz\Admin\Fields;

use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class Canonical extends Text
{
    public function __construct($name = 'Canonical', $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        // Get the current request
        $request = resolve(NovaRequest::class);

        // Extract the resource name from the request
        $resourceName = $request->route('resource');

        // Get the resource instance from the resource name
        $resource = Nova::resourceInstanceForKey($resourceName);

        // Get the associated model
        $model = $resource->newModel();

        // Retrieve the table name from the model
        $tableName = $model->getTable();

        // Set the rules
        $this->rules('required', 'max:255', 'unique:'.$tableName.',canonical');
    }
}