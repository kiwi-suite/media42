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

namespace Media42\Form;

use Admin42\FormElements\Form;
use Media42\FormElements\File;
use Media42\MediaOptions;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Validator\File\MimeType;

class UploadForm extends Form implements InputFilterProviderInterface
{
    /**
     * @var MediaOptions
     */
    protected $mediaOptions;

    /**
     * UploadForm constructor.
     * @param MediaOptions $mediaOptions
     * @param null $name
     * @param array $options
     */
    public function __construct(MediaOptions $mediaOptions, $name = null, array $options = [])
    {
        $this->mediaOptions = $mediaOptions;

        parent::__construct($name, $options);
    }

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

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        $allowedMimeTypes = $this->mediaOptions->getAllowedMimeTypes();
        if (empty($allowedMimeTypes)) {
            return [];
        }
        return [
            'file' => [
                'required' => true,
                'validators' => [
                    [
                        'name' => MimeType::class,
                        'options' => [
                            'mimeType' => $allowedMimeTypes,
                        ]
                    ]
                ]
            ]
        ];
    }
}
