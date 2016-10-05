<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
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
