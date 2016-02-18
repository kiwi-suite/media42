<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Command;

use Media42\MediaOptions;
use Media42\Model\Media;
use Core42\Command\AbstractCommand;
use Core42\Command\ConsoleAwareTrait;
use Core42\Db\ResultSet\ResultSet;
use Imagine\Image\Point;
use Media42\TableGateway\MediaTableGateway;
use ZF\Console\Route;

class RegenerateCommand extends AbstractCommand
{
    use ConsoleAwareTrait;

    /**
     * @var bool
     */
    protected $force = false;

    /**
     * @var string|null
     */
    protected $dimension;

    /**
     * @var MediaOptions
     */
    protected $mediaOptions;

    /**
     * @var ResultSet
     */
    protected $result;

    /**
     * @param bool $force
     * @return $this
     */
    public function setForce($force)
    {
        $this->force = $force;
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function preExecute()
    {
        $this->mediaOptions = $this->getServiceManager()->get(MediaOptions::class);

        $this->result = $this->getTableGateway(MediaTableGateway::class)->select();

        if ($this->dimension !== null) {
            if (!$this->mediaOptions->hasDimension($this->dimension)) {
                $this->addError('dimension', 'invalid dimension');
            }
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        /** @var Media $media */
        foreach ($this->result as $media) {
            if (substr($media->getMimeType(), 0, 6) !== "image/") {
                continue;
            }

            if ($this->force === true) {
                $dir = scandir($media->getDirectory());
                foreach ($dir as $_entry) {
                    if ($_entry == ".." || $_entry == ".") {
                        continue;
                    }

                    if ($_entry == $media->getFilename()) {
                        continue;
                    }

                    unlink($media->getDirectory() . $_entry);
                }
            }

            if ($this->dimension === null) {
                foreach (array_keys($this->mediaOptions->getDimensions()) as $dimension) {
                    /* @var ImageResizeCommand $cmd */
                    $cmd = $this->getCommand(ImageResizeCommand::class);
                    $cmd->setMedia($media)
                        ->setDimensionName($dimension)
                        ->run();
                }
            } else {
                /* @var ImageResizeCommand $cmd */
                $cmd = $this->getCommand(ImageResizeCommand::class);
                $cmd->setMedia($media)
                    ->setDimensionName($this->dimension)
                    ->run();
            }
        }
    }

    /**
     * @param Route $route
     * @return void
     */
    public function consoleSetup(Route $route)
    {
    }
}
