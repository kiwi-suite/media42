<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Command;

use Core42\Command\AbstractCommand;
use Core42\Db\Transaction\TransactionManager;
use Media42\Command\ImageResizeCommand;
use Media42\MediaEvent;
use Media42\MediaOptions;
use Media42\Model\Media;
use Media42\TableGateway\MediaTableGateway;

abstract class AbstractAddCommand extends AbstractCommand
{
    /**
     * @var string
     */
    protected $category;

    /**
     * @var string|null
     */
    protected $resizeOriginalDimension;

    /**
     * @var MediaOptions
     */
    protected $mediaOptions;

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
     * @param string $resizeOriginalDimension
     * @return $this
     */
    public function setResizeOriginalDimension($resizeOriginalDimension)
    {
        $this->resizeOriginalDimension = $resizeOriginalDimension;
        return $this;
    }

    /**
     * @param string $destination
     * @return boolean
     */
    abstract protected function moveFile($destination);

    protected function addMedia($filename, $source)
    {
        $directory = $this->getTargetDir();
        $destination = $this->mediaOptions->getPath() . $directory . $filename;

        /* @var TransactionManager $tx */
        $tx = $this->getServiceManager()->get(TransactionManager::class);
        $tx->begin();

        $dateTime = new \DateTime();
        $finfo = finfo_open(FILEINFO_MIME_TYPE);

        $media = new Media();
        $media->setFilename($filename)
            ->setTitle($filename)
            ->setMeta(json_encode([]))
            ->setDirectory($directory)
            ->setMimeType(finfo_file($finfo, $source))
            ->setSize(sprintf("%u", filesize($source)))
            ->setUpdated($dateTime)
            ->setCreated($dateTime);

        if (!empty($this->category)) {
            $media->setCategory($this->category);
        }

        $this->getTableGateway(MediaTableGateway::class)->insert($media);

        if (!$this->moveFile($destination)) {
            $tx->rollback();
            return null;
        }

        if ($this->resizeOriginalDimension !== null) {



        }


        $this
            ->getServiceManager()
            ->get('Media42\EventManager')
            ->trigger(MediaEvent::EVENT_ADD, $media);


        if (substr($media->getMimeType(), 0, 6) == "image/") {
            foreach (array_keys($this->mediaOptions->getDimensions()) as $dimension) {
                /* @var ImageResizeCommand $cmd */
                $cmd = $this->getCommand(ImageResizeCommand::class);
                $cmd->setMedia($media)
                    ->setDimensionName($dimension)
                    ->run();
            }
        }

        $tx->commit();

        return $media;
    }

    /**
     * @return string
     */
    protected function getTargetDir()
    {
        $targetDir = implode('/', str_split(substr(md5(uniqid()), 0, 6), 2)) . '/';
        if (!is_dir($this->mediaOptions->getPath() . $targetDir)) {
            mkdir($this->mediaOptions->getPath() . $targetDir, 0777, true);
        }
        return $targetDir;
    }
}
