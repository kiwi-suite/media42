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

namespace Media42\FormElements;

use Admin42\FormElements\AngularAwareInterface;
use Admin42\FormElements\ElementTrait;
use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;

class File extends Element implements InputProviderInterface, AngularAwareInterface
{
    use ElementTrait;

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return [
            'type'     => 'Zend\InputFilter\FileInput',
            'name'     => $this->getName(),
            'required' => $this->isRequired(),
        ];
    }
}
