<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Controller;

use Media42\Command\DeleteCommand;
use Media42\Command\EditCommand;
use Admin42\Mvc\Controller\AbstractAdminController;
use Core42\Stdlib\MaxUploadFileSize;
use Core42\View\Model\JsonModel;
use Media42\Command\ImageCropCommand;
use Media42\Command\StreamCommand;
use Media42\Command\UploadCommand;
use Media42\Form\EditForm;
use Media42\Form\UploadForm;
use Media42\MediaOptions;
use Media42\Selector\SmartTable\MediaSelector as SmartTableSelector;
use Media42\TableGateway\MediaTableGateway;
use Zend\Http\PhpEnvironment\Response;
use Zend\Json\Json;
use Zend\View\Model\ViewModel;

class MediaController extends AbstractAdminController
{
    /**
     * @return array|mixed
     */
    public function indexAction()
    {
        if ($this->getRequest()->isXmlHttpRequest() && $this->params("referrer") == "index") {

            return $this->getSelector(SmartTableSelector::class)->getResult();
        }

        $mediaOptions = $this->getServiceLocator()->get(MediaOptions::class);

        $viewModel = new ViewModel([
            'uploadForm' => $this->getForm(UploadForm::class),
            'maxFileSize' => MaxUploadFileSize::getSize(),
            'mediaCategories' => $mediaOptions->getCategories(),
            'uploadHost' => $mediaOptions->getUploadHost(),
        ]);

        if ($this->params('referrer') === "modal") {
            $viewModel->setTerminal(true);
        }

        return $viewModel;
    }

    /**
     * @return array|\Zend\Http\Response
     * @throws \Exception
     */
    public function editAction()
    {
        $editForm = $this->getForm(EditForm::class);

        $prg = $this->prg();
        if ($prg instanceof Response) {
            return $prg;
        }

        if ($prg !== false) {
            $editForm->setData($prg);
            if ($editForm->isValid()) {

                /* @var EditCommand $cmd */
                $cmd = $this->getCommand(EditCommand::class);
                $cmd->setMediaId($this->params('id'));

                $formCommand = $this->getFormCommand();
                $media = $formCommand->setForm($editForm)
                    ->setCommand($cmd)
                    ->setData($prg)
                    ->run();

                $this->getTableGateway(MediaTableGateway::class)->update($media);
                $this->flashMessenger()->addSuccessMessage([
                    'title' => 'toaster.media.detail.title.success',
                    'message' => 'toaster.media.detail.message.success',
                ]);

                return $this->redirect()->toRoute('admin/media/edit', ['id' => $media->getId()]);
            }
        } else {
            $media = $this->getTableGateway(MediaTableGateway::class)->selectByPrimary((int) $this->params("id"));
            $editForm->setData($media->toArray());
        }

        $mediaOptions = $this->getServiceLocator()->get(MediaOptions::class);

        $imageSize = null;
        if (substr($media->getMimeType(), 0, 6) == "image/") {
            $imagine = $this->getServiceLocator()->get('Imagine');
            $box = $imagine->open($media->getDirectory() . $media->getFilename())->getSize();
            $imageSize = [
                'width' => $box->getWidth(),
                'height' => $box->getHeight(),
            ];
        }

        return [
            'editForm' => $editForm,
            'media' => $media,
            'dimensions' => $mediaOptions->getDimensions(false),
            'imageSize' => $imageSize,
            'maxFileSize' => MaxUploadFileSize::getSize()
        ];
    }

    /**
     * @return JsonModel
     */
    public function cropAction()
    {
        $data = Json::decode($this->getRequest()->getContent(), Json::TYPE_ARRAY);

        /* @var ImageCropCommand $cmd */
        $cmd = $this->getCommand(ImageCropCommand::class)
            ->setMediaId($this->params("id"))
            ->setBoxWidth($data['width'])
            ->setBoxHeight($data['height'])
            ->setOffsetX($data['x'])
            ->setOffsetY($data['y'])
            ->setDimensionName($this->params("dimension"));
        $cmd->run();

        if ($cmd->hasErrors()) {
            return new JsonModel(['success' => false]);
        }

        return new JsonModel(['success' => true]);
    }

    /**
     * @return \Zend\Http\Response\Stream
     */
    public function streamAction()
    {
        return $this->getCommand(StreamCommand::class)
            ->setMediaId($this->params("id"))
            ->setDimension($this->params("dimension"))
            ->run();
    }

    /**
     * @return JsonModel
     * @throws \Exception
     */
    public function uploadAction()
    {
        $jsonModel = new JsonModel();
        $form = $this->getForm(UploadForm::class);
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $form->setData($post);
            if ($form->isValid()) {
                $this->getFormCommand()
                    ->setCommand($this->getCommand(UploadCommand::class))
                    ->setForm($form)
                    ->enableAutomaticFormFill(false)
                    ->run();
                $jsonModel->answer = "File transfer completed";
            }
        }

        return $jsonModel;
    }

    /**
     * @return JsonModel
     */
    public function deleteAction()
    {
        /* @var DeleteCommand $deleteCmd */
        $deleteCmd = $this->getCommand(DeleteCommand::class);

        if ($this->getRequest()->isDelete()) {

            $deleteParams = [];
            parse_str($this->getRequest()->getContent(), $deleteParams);

            $deleteCmd->setMediaId((int) $deleteParams['id'])
                ->run();

            return new JsonModel([
                'success' => true,
            ]);
        } elseif ($this->getRequest()->isPost()) {


            $deleteCmd->setMediaId((int) $this->params()->fromPost('id'))
                ->run();

            $this->flashMessenger()->addSuccessMessage([
                'title' => 'toaster.media.delete.title.success',
                'message' => 'toaster.media.delete.message.success',
            ]);

            return new JsonModel([
                'redirect' => $this->url()->fromRoute('admin/media')
            ]);
        }

        return new JsonModel([
            'redirect' => $this->url()->fromRoute('admin/media')
        ]);
    }
}
