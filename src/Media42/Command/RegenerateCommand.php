<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Command;

use Media42\Command\ImageResizeCommand;
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
     * @var ResultSet
     */
    protected $result;

    /**
     * @var bool
     */
    protected $force = false;

    /**
     * @var string|null
     */
    protected $dimension = null;

    /**
     * @var string|null
     */
    protected $category = null;

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
     * @param null|string $dimension
     * @return $this
     */
    public function setDimension($dimension)
    {
        $this->dimension = $dimension;
        return $this;
    }

    /**
     * @param null|string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @throws \Exception
     */
    protected function preExecute()
    {
        $select = $this->getTableGateway(MediaTableGateway::class)->getSql()->select();
        if ($this->category !== null) {
            $select->where->equalTo('category', $this->category);
        }

        $select->order('id');

        // praktisch wenns mal nen Fehler gibt...
        $select->offset(0);
        
        $this->result = $this->getTableGateway(MediaTableGateway::class)->selectWith($select);
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        /* @var MediaOptions $mediaOptions */
        $mediaOptions = $this->getServiceManager()->get(MediaOptions::class);

        $this->consoleOutput('Found ' . $this->result->count() . " images to regenerate");

        $count = 0;
        foreach ($this->result as $media) {
            /** @var Media $media */

            if (substr($media->getMimeType(), 0, 6) !== "image/") {
                continue;
            }

            if ($this->force === true) {
                $dir = scandir($mediaOptions->getPath() . $media->getDirectory());
                foreach ($dir as $_entry) {
                    if ($_entry == ".." || $_entry == ".") {
                        continue;
                    }

                    if ($_entry == $media->getFilename()) {
                        continue;
                    }

                    unlink($mediaOptions->getPath() . $media->getDirectory() . $_entry);
                }
            }

            if ($this->dimension !== null) {
                /* @var ImageResizeCommand $cmd */
                $cmd = $this->getCommand(ImageResizeCommand::class);
                $cmd->setMedia($media)
                    ->setDimensionName($this->dimension)
                    ->run();
            } else {
                foreach ($mediaOptions->getDimensions() as $dimension) {
                    /* @var ImageResizeCommand $cmd */
                    $cmd = $this->getCommand(ImageResizeCommand::class);
                    $cmd->setMedia($media)
                        ->setDimension($dimension)
                        ->run();
                }
            }

            $count++;
            $this->consoleWrite("\rgenerated $count");
        }
        $this->consoleWrite("\n");
    }

    /**
     * @param Route $route
     * @return void
     */
    public function consoleSetup(Route $route)
    {
        $route->getMatchedParam('category');

        $this->force = $route->getMatchedParam('force', false);

        $category = $route->getMatchedParam('category');
        if (!empty($category)) {
            $this->category = $category;
        }

        $dimension = $route->getMatchedParam('dimension');
        if (!empty($category)) {
            $this->dimension = $dimension;
        }
    }
}
