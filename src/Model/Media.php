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

namespace Media42\Model;

use Core42\Model\AbstractModel;
use Core42\Stdlib\DateTime;

/**
 * @method Media setId() setId(int $id)
 * @method int getId() getId()
 * @method Media setDirectory() setDirectory(string $directory)
 * @method string getDirectory() getDirectory()
 * @method Media setFilename() setFilename(string $filename)
 * @method string getFilename() getFilename()
 * @method Media setCategory() setCategory(string $category)
 * @method string getCategory() getCategory()
 * @method Media setMimeType() setMimeType(string $mimeType)
 * @method string getMimeType() getMimeType()
 * @method Media setSize() setSize(int $size)
 * @method int getSize() getSize()
 * @method Media setMeta() setMeta(string $meta)
 * @method string getMeta() getMeta()
 * @method Media setUpdated() setUpdated(DateTime $updated)
 * @method DateTime getUpdated() getUpdated()
 * @method Media setCreated() setCreated(DateTime $created)
 * @method DateTime getCreated() getCreated()
 */
class Media extends AbstractModel
{
    /**
     * @var array
     */
    protected $properties = [
        'id',
        'directory',
        'filename',
        'category',
        'mimeType',
        'size',
        'meta',
        'updated',
        'created',
    ];
}
