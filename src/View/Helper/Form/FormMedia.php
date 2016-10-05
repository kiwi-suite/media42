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

        $elementData['mediaUrl'] = $urlHelper('admin/media', ['referrer' => 'modal', 'category' => 'default']);
        return $elementData;
    }
}
