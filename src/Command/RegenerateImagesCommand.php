<?php
namespace Media42\Command;

use Core42\Command\AbstractCommand;
use Core42\Command\ConsoleAwareTrait;
use Core42\Db\ResultSet\ResultSet;
use Media42\MediaOptions;
use Media42\Model\Media;
use Media42\Selector\MediaSelector;
use Media42\TableGateway\MediaTableGateway;
use ZF\Console\Route;

class RegenerateImagesCommand extends AbstractCommand
{
    use ConsoleAwareTrait;

    /**
     * @var int
     */
    protected $mediaId;

    /**
     * @param int $mediaId
     * @return $this
     */
    public function setMediaId($mediaId)
    {
        $this->mediaId = $mediaId;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function execute()
    {
        if ($this->mediaId > 0) {
            $media = $this->getTableGateway(MediaTableGateway::class)->selectByPrimary((int) $this->mediaId);
            if ($media instanceof Media) {
                $this->regenerateMedia($media);
            }

            return;
        }

        $mediaResultSet = $this->getTableGateway(MediaTableGateway::class)->select();
        /** @var Media $media */
        foreach ($mediaResultSet as $media) {
            if (\substr($media->getMimeType(), 0, 6) != 'image/' ||
                \substr($media->getMimeType(), 0, 9) == 'image/svg'
            ) {
                continue;
            }

            $cmd = PHP_BINARY . ' vendor/fruit42/core42/bin/fruit media-regenerate --media=' . $media->getId();

            passthru($cmd);
        }
    }

    protected function regenerateMedia(Media $media)
    {
        /* @var MediaOptions $mediaOptions */
        $mediaOptions = $this->getServiceManager()->get(MediaOptions::class);

        $this->consoleOutput("<info>Start regenerating #{$media->getId()}: {$media->getFilename()}</info>");
        $baseDir = $mediaOptions->getPath() . $media->getDirectory();
        $dir = scandir($baseDir);
        foreach ($dir as $_entry) {
            if ($_entry == '..' || $_entry == '.') {
                continue;
            }

            if ($_entry === $media->getFilename()) {
                continue;
            }

            @unlink($baseDir . $_entry);
            $this->consoleOutput("Cleaning {$_entry}");
        }

        $this->getCommand(ImageSizeCommand::class)->setMedia($media)->run();

        foreach ($mediaOptions->getDimensions() as $dimension) {
            $this->consoleOutput("Regenerate {$dimension['width']}x{$dimension['height']}");

            /* @var ImageResizeCommand $cmd */
            $cmd = $this->getCommand(ImageResizeCommand::class);
            $cmd->setMedia($media)
                ->setDimension($dimension)
                ->run();
        }

        $this
            ->getSelector(MediaSelector::class)
            ->setDisableCache(true)
            ->setMediaId($media->getId())
            ->getResult();

        $this->consoleOutput("");
    }

    /**
     * @param Route $route
     * @return void
     */
    public function consoleSetup(Route $route)
    {
        $this->setMediaId($route->getMatchedParam('media', null));
    }
}
