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

class ImportCommand extends AbstractAddCommand
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @param $file
     * @return $this
     */
    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     *
     */
    protected function preExecute()
    {
        $this->mediaOptions = $this->getServiceManager()->get(MediaOptions::class);
    }

    /**
     * @return mixed|void
     */
    protected function execute()
    {
        $originalFilename = basename($this->file);
        return $this->addMedia($originalFilename, $this->file);
    }

    /**
     * @inheritDoc
     */
    protected function moveFile($destination)
    {
        copy($this->file, $destination);
    }
}
