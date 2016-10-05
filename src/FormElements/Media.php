<?php
namespace Media42\FormElements;

use Admin42\FormElements\AngularAwareInterface;
use Admin42\FormElements\ElementTrait;
use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;

class Media extends Element implements AngularAwareInterface, InputProviderInterface
{
    use ElementTrait;

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        return $this;
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInput()}.
     *
     * @return array
     */
    public function getInputSpecification()
    {
        return [
            'name' => $this->getName(),
            'required' => $this->isRequired(),
        ];
    }
}
