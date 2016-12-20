<?php
namespace Media42\Command;

use Core42\Command\AbstractCommand;
use Core42\Command\ConsoleAwareTrait;
use Core42\Stdlib\Symlink;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Generator\ValueGenerator;
use ZF\Console\Route;

class MediaSetupCommand extends AbstractCommand
{
    use ConsoleAwareTrait;

    /**
     * @return mixed
     */
    protected function execute()
    {
        if (!file_exists('data/media')) {
            @mkdir('data/media', 0777, true);
        }

        $this->consoleOutput("<info>Create media symlink</info>");

        $config = [
            'media' => [
                'url'         => '/media',
            ],
        ];

        $valueGenerator = new ValueGenerator();
        $valueGenerator->setValue($config);
        $valueGenerator->setType(ValueGenerator::TYPE_ARRAY_SHORT);

        $filegenerator = new FileGenerator();
        $filegenerator->setBody("return " . $valueGenerator->generate() .  ";" . PHP_EOL);

        $this->consoleOutput("<info>config written to 'config/autoload/local.media.config.php'</info>");
        file_put_contents("config/autoload/local.media.config.php", $filegenerator->generate());

        $filesystem = new Filesystem(new Local(getcwd()));
        $filesystem->addPlugin(new Symlink());

        if (!file_exists('public/media')) {
            $filesystem->symlink('data/media', 'public/media');
        }
    }

    /**
     * @param Route $route
     * @return void
     */
    public function consoleSetup(Route $route)
    {
        // TODO: Implement consoleSetup() method.
    }
}
