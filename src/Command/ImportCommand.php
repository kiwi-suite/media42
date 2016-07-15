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
     * @var
     */
    protected $filename;

    /**
     * @var bool
     */
    protected $moveFile = false;

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
     * @param mixed $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @param boolean $moveFile
     * @return $this
     */
    public function setMoveFile($moveFile)
    {
        $this->moveFile = $moveFile;
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
        if (empty($this->filename)) {
            $this->filename = basename($this->file);
        }

        return $this->addMedia($this->filename, $this->file);
    }

    /**
     * @inheritDoc
     */
    protected function moveFile($destination)
    {
        if (!$this->moveFile) {
            return rename($this->file, $destination);
        }

        return copy($this->file, $destination);
    }
}
