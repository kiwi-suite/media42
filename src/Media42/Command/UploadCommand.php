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
     * @var string|null
     */
    private $filename;

    /**
     * @var bool
     */
    protected $checkFileUpload = true;
    
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
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @param boolean $checkFileUpload
     * @return $this
     */
    public function setCheckFileUpload($checkFileUpload)
    {
        $this->checkFileUpload = $checkFileUpload;
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

        if (empty($this->filename)) {
            $this->filename = $this->uploadData['name'];
        }
    }

    /**
     * @return Media
     */
    protected function execute()
    {
        $media = $this->addMedia($this->filename, $this->uploadData['tmp_name']);

        return $media;
    }

    /**
     * @inheritdoc
     */
    protected function moveFile($destination)
    {
        ErrorHandler::start();
        if ($this->checkFileUpload) {
            $result = move_uploaded_file($this->uploadData['tmp_name'], $destination);    
        } else {
            $result = rename($this->uploadData['tmp_name'], $destination);
        }
        $warningException = ErrorHandler::stop();
        if (!$result || null !== $warningException) {
            $this->addError('uploadData', 'error at moving uploaded file');
            return false;
        }

        return true;
    }
}
