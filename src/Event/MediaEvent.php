<?php

/*
 * media42
 *
 * @package media42
 * @link https://github.com/raum42/media42
 * @copyright Copyright (c) 2010 - 2016 raum42 (https://www.raum42.at)
 * @license MIT License
 * @author raum42 <kiwi@raum42.at>
 */

namespace Media42\Event;

use Zend\EventManager\Event;

class MediaEvent extends Event
{
    const EVENT_EDIT = 'event_edit';
    const EVENT_ADD = 'event_add';
    const EVENT_DELETE = 'event_delete';
    const EVENT_CROP = 'event_crop';
}
