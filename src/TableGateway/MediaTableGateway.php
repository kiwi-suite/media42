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
        'id' => 'Integer',
        'directory' => 'String',
        'filename' => 'String',
        'category' => 'String',
        'title' => 'String',
        'description' => 'String',
        'keywords' => 'String',
        'mimeType' => 'String',
        'size' => 'Integer',
        'meta' => 'String',
        'updated' => 'DateTime',
        'created' => 'DateTime',
    ];

    /**
     * @var string
     */
    protected $modelPrototype = 'Media42\\Model\\Media';
}
