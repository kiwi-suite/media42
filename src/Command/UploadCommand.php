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

namespace Media42\Command;

use Cocur\Slugify\Slugify;
use Core42\Command\AbstractCommand;
use Core42\Stdlib\DateTime;
use Dflydev\ApacheMimeTypes\PhpRepository;
use Media42\Event\MediaEvent;
use Media42\MediaOptions;
use Media42\Model\Media;
use Media42\TableGateway\MediaTableGateway;
use Ramsey\Uuid\Uuid;
use Zend\Stdlib\ErrorHandler;

class UploadCommand extends AbstractCommand
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
     * @var string
     */
    protected $category;

    /**
     * @var MediaOptions
     */
    protected $mediaOptions;

    /**
     * @var string
     */
    protected $mimeType;

    /**
     * @var string
     */
    protected $source;

    /**
     * @var int
     */
    protected $mediaId;

    /**
     * @var Media
     */
    protected $media;

    /**
     * @var Media
     */
    protected $oldMedia;

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

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
     * @param bool $checkFileUpload
     * @return $this
     */
    public function setCheckFileUpload($checkFileUpload)
    {
        $this->checkFileUpload = $checkFileUpload;

        return $this;
    }

    /**
     * @param $mediaId
     * @return $this
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    /**
     * @param Media $media
     * @return $this
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;

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
            $this->category = 'default';
        }

        $this->source = $this->uploadData['tmp_name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $this->mimeType = finfo_file($finfo, $this->source);

        if (empty($this->filename)) {
            $this->filename = $this->uploadData['name'];
        }

        $this->filename = $this->generateFileName($this->filename, $this->mimeType);

        if ($this->mediaId > 0) {
            $this->media = $this->getTableGateway(MediaTableGateway::class)->selectByPrimary((int) $this->mediaId);
        }

        if (!($this->media instanceof Media)) {
            $this->media = new Media();
        } else {
            $this->oldMedia = clone $this->media;
        }
    }

    /**
     * @return Media
     * @throws \Exception
     */
    protected function execute()
    {
        $directory = $this->getTargetDir();
        $destination = $this->mediaOptions->getPath() . $directory . $this->filename;

        $dateTime = new DateTime();

        $this->media->setFilename($this->filename)
            ->setMeta([])
            ->setDirectory($directory)
            ->setMimeType($this->mimeType)
            ->setSize(sprintf('%u', filesize($this->source)))
            ->setUpdated($dateTime)
            ->setCreated($dateTime);

        if (!empty($this->category)) {
            $this->media->setCategory($this->category);
        }

        if ($this->media->getId() > 0) {
            $this->getTableGateway(MediaTableGateway::class)->update($this->media);
        } else {
            $this->getTableGateway(MediaTableGateway::class)->insert($this->media);
        }


        if (!$this->moveFile($destination)) {
            throw new \Exception('cant move uploaded file');
        }

        if ($this->oldMedia !== null) {
            $this->getCommand(CleanupDataDirectory::class)->setMedia($this->oldMedia)->run();
        }

        $this
            ->getServiceManager()
            ->get('Media42\EventManager')
            ->trigger(MediaEvent::EVENT_ADD, $this->media);

        return $this->media;
    }

    /**
     * @param string $filename
     * @param string $mimeType
     * @return string
     */
    protected function generateFileName($filename, $mimeType)
    {
        $filenameParts = pathinfo($filename);

        $filename = $this
            ->getServiceManager()
            ->get(Slugify::class)
            ->slugify($filenameParts['filename']);

        $extension = $filenameParts['extension'];
        $mimeTypeRepository = new PhpRepository();

        $availableExtensions = $mimeTypeRepository->findExtensions($mimeType);
        if (count($availableExtensions) > 0) {
            $extension = current($availableExtensions);
        }

        return $filename . '.' . $extension;
    }

    /**
     * @return string
     */
    protected function getTargetDir()
    {
        do {
            $targetDir = implode('/', str_split(substr(md5(Uuid::uuid4()->toString()), 0, 8), 2)) . '/';

            //ad blocker will block the image when "ad" is part of the path
        } while (is_dir($this->mediaOptions->getPath() . $targetDir) || strpos($targetDir, 'ad') !== false);

        mkdir($this->mediaOptions->getPath() . $targetDir, 0777, true);

        return $targetDir;
    }

    /**
     * @param string $destination
     * @return bool
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
