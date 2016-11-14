<?php
namespace Media42\Mutator\Strategy;

use Core42\Hydrator\Mutator\Strategy\StrategyInterface;
use Media42\Stdlib\Media;
use Zend\ServiceManager\ServiceManager;

class MediaStrategy implements StrategyInterface
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    public function __construct(ServiceManager $serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function hydrate($value)
    {
        return $this->serviceManager->build(Media::class, ['mediaId' => (int) $value]);
    }
}
