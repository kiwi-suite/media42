<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
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
