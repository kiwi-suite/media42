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
use Core42\Stdlib\DateTime;
use Dflydev\ApacheMimeTypes\PhpRepository;
use Media42\Event\MediaEvent;
use Media42\MediaOptions;
use Media42\Model\Media;
use Core42\Command\AbstractCommand;
use Media42\TableGateway\MediaTableGateway;
use Zend\Stdlib\Glob;

class EditCommand extends AbstractCommand
{
    /**
     * @var int
     */
    protected $mediaId;

    /**
     * @var Media
     */
    protected $media;

    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $oldFilename;

    /**
     * @var array
     */
    protected $uploadData;

    /**
     * @param int $mediaId
     * @return $this
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = (int) $mediaId;

        return $this;
    }

    /**
     * @param Media $media
     * @return $this
     */
    public function setMedia($media)
    {
        $this->media = $media;

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
     * @param array $values
     */
    public function hydrate(array $values)
    {
        $this->setFilename($values['filename']);
    }

    /**
     * @throws \Exception
     */
    protected function preExecute()
    {
        if (!empty($this->mediaId)) {
            $this->media = $this->getTableGateway(MediaTableGateway::class)->selectByPrimary($this->mediaId);
        }

        if (!($this->media instanceof Media)) {
            $this->addError('media', 'invalid media');
        }

        if (empty($this->filename)) {
            $this->addError('title', "filename can't be empty");
        }

        $this->oldFilename = $this->media->getFilename();
    }

    /**
     * @return Media
     */
    protected function execute()
    {
        $this->media->setFilename($this->filename);

        if ($this->media->hasChanged()) {
            $this->media->setUpdated(new DateTime());

            if ($this->media->hasChanged('filename')) {
                $this->renameFiles();
            }

            $this->getTableGateway(MediaTableGateway::class)->update($this->media);

            $this
                ->getServiceManager()
                ->get('Media42\EventManager')
                ->trigger(MediaEvent::EVENT_EDIT, $this->media);

            $this->getCache('media')->deleteItem($this->media->getId());
        }



        return $this->media;
    }

    protected function renameFiles()
    {
        $filenameParts = pathinfo($this->media->getFilename());

        $filename = $this
            ->getServiceManager()
            ->get(Slugify::class)
            ->slugify($filenameParts['filename']);

        $extension = $filenameParts['extension'];
        $mimeTypeRepository = new PhpRepository();

        $availableExtensions = $mimeTypeRepository->findExtensions($this->media->getMimeType());
        if (count($availableExtensions) > 0) {
            $extension = current($availableExtensions);
        }

        $this->media->setFilename($filename . '.' . $extension);

        $oldFilenameParts = pathinfo($this->oldFilename);
        $filenameParts = pathinfo($this->media->getFilename());

        $mediaOptions = $this->getServiceManager()->get(MediaOptions::class);
        $globPath = $mediaOptions->getPath() . $this->media->getDirectory() . $oldFilenameParts['filename'] . '*';
        var_dump($globPath);
        $entries = Glob::glob($globPath);
        foreach ($entries as $file) {
            $currentFilenameParts = pathinfo($file);

            $newFilename = str_replace(
                $oldFilenameParts['filename'],
                $filenameParts['filename'],
                $currentFilenameParts['filename']
            );
            $newFilename = $currentFilenameParts['dirname'] . '/' . $newFilename;
            $newFilename .= '.' . $currentFilenameParts['extension'];

            rename($file, $newFilename);
        }
    }
}
