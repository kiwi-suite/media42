<?php

/*
 * media42
 *
 * @package media42
 * @link https://github.com/raum42/media42
 * @copyright Copyright (c) 2010 - 2016 raum42 (https://www.raum42.at)
 * @license MIT License
 * @author raum42 <kiwi@raum42.at>
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

        $angularHelper->addJsonTemplate('mediaConfig', [
            'baseUrl' => $this->getUrl(),
            'dimensions' => $this->getDimensions(),
        ], false);
    }
}
