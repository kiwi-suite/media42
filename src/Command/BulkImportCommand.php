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
use Core42\Command\ConsoleAwareTrait;
use ZF\Console\Route;

class BulkImportCommand extends AbstractCommand
{
    use ConsoleAwareTrait;

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string|null
     */
    protected $category;

    /**
     * @param string $directory
     * @return $this
     */
    public function setDirectory($directory)
    {
        $this->directory = rtrim($directory, '\\/') . '/';
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
     *
     */
    protected function preExecute()
    {
        if (!is_dir($this->directory)) {
            $this->addError('directory', 'no correct directory given');
        }
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        /* @var ImportCommand $cmd */
        $cmd = $this->getCommand(ImportCommand::class);
        $cmd->setCategory($this->category);

        $files = scandir($this->directory);

        foreach ($files as $file) {

            if ($file == '.' || $file == '..') {
                continue;
            }

            if (is_file($this->directory . $file)) {
                $cmd->setFile($this->directory . $file);
                $media = $cmd->run();

                $this->consoleOutput($file . ' - ' . $media->getId());
            }
        }
    }

    /**
     * @param Route $route
     * @return void
     */
    public function consoleSetup(Route $route)
    {
        $this->setDirectory($route->getMatchedParam('dir'));


    }
}
