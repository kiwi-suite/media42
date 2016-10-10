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

namespace Media42\Form;

use Admin42\FormElements\Form;
use Media42\FormElements\File;

class UploadForm extends Form
{
    /**
     *
     */
    public function init()
    {
        $this->add([
            'type' => 'text',
            'name' => 'category',
        ]);

        $this->add([
            'type' => File::class,
            'name' => 'file',
            'required' => true,
        ]);
    }
}
