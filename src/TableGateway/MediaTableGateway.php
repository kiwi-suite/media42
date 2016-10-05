<?php
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
        'title' => 'string',
        'description' => 'string',
        'keywords' => 'string',
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
