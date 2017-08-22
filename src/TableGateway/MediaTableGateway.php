<?php

/*
 * media42
 *
 * @package media42
 * @link https://github.com/kiwi-suite/media42
 * @copyright Copyright (c) 2010 - 2017 kiwi suite (https://kiwi-suite.com)
 * @license MIT License
 * @author kiwi suite <dev@kiwi-suite.com>
 */

namespace Media42\TableGateway;

use Core42\Db\TableGateway\AbstractTableGateway;

class MediaTableGateway extends AbstractTableGateway
{
    /**
     * @var string
     */
    protected $table = 'media42_media';

    /**
     * @var array
     */
    protected $primaryKey = ['id'];

    /**
     * @var array
     */
    protected $databaseTypeMap = [
        'id' => 'integer',
        'directory' => 'string',
        'filename' => 'string',
        'category' => 'string',
        'mimeType' => 'string',
        'size' => 'integer',
        'meta' => 'json',
        'updated' => 'dateTime',
        'created' => 'dateTime',
    ];

    /**
     * @var string
     */
    protected $modelPrototype = 'Media42\\Model\\Media';
}
