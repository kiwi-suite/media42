<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Form;

use Zend\Form\Element\File;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\FileInput;
use Zend\InputFilter\InputFilter;

class UploadForm extends Form
{
    /**
     *
     */
    public function init()
    {
        $category = new Text("category");
        $this->add($category);

        $file = new File("file");
        $file->setAttribute("multiple", true);
        $this->add($file);

        $inputFilter = new InputFilter();

        $fileInput = new FileInput("file");
        $fileInput->setRequired(true);
        $inputFilter->add($fileInput);

        $this->setInputFilter($inputFilter);
    }
}
