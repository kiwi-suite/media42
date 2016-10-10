<?php
namespace Media42\View\Helper\Form;

use Admin42\FormElements\AngularAwareInterface;
use Admin42\View\Helper\Form\FormHelper;

class FormMedia extends FormHelper
{
    /**
     * @param AngularAwareInterface $element
     * @param bool $angularNameRendering
     * @return array
     */
    public function getElementData(AngularAwareInterface $element, $angularNameRendering = true)
    {
        $this
            ->getAngularHelper()
            ->addHtmlPartial(
                'element/form/media-modal.html',
                'partial/admin42/form/media-modal'
            );

        $elementData = parent::getElementData($element, $angularNameRendering);

        $urlHelper = $this->getView()->plugin('url');

        $elementData['mediaUrl'] = $urlHelper('admin/media', [
            'referrer' => 'modal',
            'categorySelection' => $element->getCategorySelection(),
            'typeSelection' => $element->getTypeSelection(),
        ]);

        $this->getView()->plugin('mediaOptions')->addAngularMediaConfig();

        return $elementData;
    }

    public function getValue(AngularAwareInterface $element)
    {
        $value = [
            'id' => null,
            'directory' => null,
            'filename' => null,
            'mimeType' => null,
            'size' => null,
        ];
        if ($element->getValue() > 0) {
            $mediaHelper = $this->getView()->plugin('media');
            $media = $mediaHelper($element->getValue());
            if (!empty($media)) {
                $value = [
                    'id' => $media->getId(),
                    'directory' => $media->getDirectory(),
                    'filename' => $media->getFilename(),
                    'mimeType' => $media->getMimeType(),
                    'size' => $media->getSize(),
                ];
            }
        }

        return $value;
    }
}
