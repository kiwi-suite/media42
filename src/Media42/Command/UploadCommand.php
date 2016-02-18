<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Command;

use Core42\Db\Transaction\TransactionManager;
use Media42\MediaOptions;
use Media42\Model\Media;
use Zend\Stdlib\ErrorHandler;

class UploadCommand extends AbstractAddCommand
{
    /**
     * @var array
     */
    private $uploadData;

    /**
     * @param array $uploadData
     * @return $this
     */
    public function setUploadData(array $uploadData)
    {
        $this->uploadData = $uploadData;

        return $this;
    }

    /**
     * @param array $values
     * @throws \Exception
     */
    public function hydrate(array $values)
    {
        $this->setUploadData($values['file']);
        $this->setCategory($values['category']);
    }

    /**
     *
     */
    protected function preExecute()
    {
        $this->mediaOptions = $this->getServiceManager()->get(MediaOptions::class);
        $categories = $this->mediaOptions->getCategories();
        $categories = array_keys($categories);

        if (!in_array($this->category, $categories)) {
            $this->category = "default";
        }
    }

    /**
     * @return Media
     */
    protected function execute()
    {
        $media = $this->addMedia($this->uploadData['name'], $this->uploadData['tmp_name']);

        return $media;
    }

    /**
     * @inheritdoc
     */
    protected function moveFile($destination)
    {
        ErrorHandler::start();
        $result = move_uploaded_file($this->uploadData['tmp_name'], $destination);
        $warningException = ErrorHandler::stop();
        if (!$result || null !== $warningException) {
            $this->addError('uploadData', 'error at moving uploaded file');
            return false;
        }

        return true;
    }
}
