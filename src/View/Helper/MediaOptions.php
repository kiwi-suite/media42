<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\View\Helper;

use Admin42\View\Helper\Angular;
use Core42\View\Helper\Proxy;

class MediaOptions extends Proxy
{
    public function addAngularMediaConfig()
    {
        /** @var Angular $angularHelper */
        $angularHelper = $this->getView()->plugin('angular');

        $angularHelper->addJsonTemplate("mediaConfig", [
            "baseUrl" => $this->getUrl(),
            "dimensions" => $this->getDimensions(),
        ], false);
    }
}
