<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Command;

use Media42\MediaEvent;
use Media42\Model\Media;
use Core42\Command\AbstractCommand;
use Core42\Command\ConsoleAwareTrait;
use Core42\Db\ResultSet\ResultSet;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Point;
use Admin42\Command\Tag\SaveCommand;
use Media42\TableGateway\MediaTableGateway;
use Zend\Json\Json;
use ZF\Console\Route;

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
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $keywords;

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
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string $keywords
     * @return $this
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;
        return $this;
    }

    /**
     * @param array $uploadData
     * @return $this
     */
    public function setUploadData($uploadData)
    {
        $this->uploadData = $uploadData;
        return $this;
    }

    /**
     * @param array $values
     */
    public function hydrate(array $values)
    {
        $this->setTitle($values['title']);
        $this->setDescription($values['description']);
        $this->setKeywords($values['keywords']);
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
            $this->addError("media", "invalid media");
        }

        if (empty($this->title)) {
            $this->addError("title", "title can't be empty");
        }
    }

    /**
     * @return Media
     */
    protected function execute()
    {
        $this->media->setTitle($this->title)
            ->setDescription((!empty($this->description) ? $this->description : null))
            ->setKeywords($this->keywords)
            ->setUpdated(new \DateTime());

        $this
            ->getServiceManager()
            ->get('Media42\EventManager')
            ->trigger(MediaEvent::EVENT_EDIT_PRE, $this->media);

        $this->getTableGateway(MediaTableGateway::class)->update($this->media);

        if (!empty($this->keywords)) {
            /* @var SaveCommand $cmd */
            $cmd = $this->getCommand(SaveCommand::class);
            $cmd->setTags($this->keywords)
                ->run();
        }
        $this
            ->getServiceManager()
            ->get('Media42\EventManager')
            ->trigger(MediaEvent::EVENT_EDIT_POST, $this->media);

        if (!empty($this->uploadData)) {
            /* @var UploadCommand $cmd */
            /*
            $cmd = $this->getCommand(UploadCommand::class);
            $cmd->setMedia($this->media)
                ->setUploadData('')
                ->run();
            */
        }

        $this->getServiceManager()->get('Cache\Media')->removeItem('media_'. $this->media->getId());

        return $this->media;
    }
}
