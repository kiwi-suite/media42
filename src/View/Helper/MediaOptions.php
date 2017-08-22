<?php

/*
 * media42
 *
 * @package media42
 * @link https://github.com/kiwi-suite/media42
 * @copyright Copyright (c) 2010 - 2017 kiwi suite (https://kiwi-suite.com)
 * @license MIT License
 * @author kiwi suite <dev@kiwi-suite.com>
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

        $baseUrl = "";
        if ($this->getPrependBasePath() === true) {
            $basePath = $this->getView()->plugin('basePath');
            $baseUrl = $basePath();
        }

        if (strlen($this->getUrl())) {
            $baseUrl .= $this->getUrl();
        }

        if (empty($baseUrl)) {
            $baseUrl = null;
        }
        $angularHelper->addJsonTemplate('mediaConfig', [
            'baseUrl' => $baseUrl,
            'dimensions' => $this->getDimensions(),
        ], false);
    }
}
