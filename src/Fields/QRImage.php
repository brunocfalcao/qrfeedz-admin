<?php

namespace QRFeedz\Admin\Fields;

use Brunocfalcao\LaravelHelpers\Rules\MaxUploadSize;
use Laravel\Nova\Fields\Image;

class QRImage extends Image
{
    public function __construct($name, $attribute = null, $disk = null, $storageCallback = null)
    {
        parent::__construct($name, $attribute, $disk, $storageCallback);

        $this->disableDownload()
             ->rules(new MaxUploadSize())
             ->acceptedTypes('image/*');
    }
}
