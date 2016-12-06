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

namespace Media42\FormElements;

use Admin42\FormElements\AngularAwareInterface;
use Admin42\FormElements\ElementTrait;
use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;

class Media extends Element implements AngularAwareInterface, InputProviderInterface
{
    use ElementTrait;

    /**
     * @var string
     */
    protected $categorySelection = '*';

    /**
     * @var string
     */
    protected $typeSelection = '*';

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        if (isset($options['categorySelection'])) {
            $this->setCategorySelection($options['categorySelection']);
        }

        if (isset($options['typeSelection']) && in_array($options['typeSelection'], ['*', 'pdf', 'images'])) {
            $this->setTypeSelection($options['typeSelection']);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCategorySelection()
    {
        return $this->categorySelection;
    }

    /**
     * @param string $categorySelection
     * @return Media
     */
    public function setCategorySelection($categorySelection)
    {
        $this->categorySelection = $categorySelection;

        return $this;
    }

    /**
     * @return string
     */
    public function getTypeSelection()
    {
        return $this->typeSelection;
    }

    /**
     * @param string $typeSelection
     * @return Media
     */
    public function setTypeSelection($typeSelection)
    {
        $this->typeSelection = $typeSelection;

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
