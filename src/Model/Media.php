<?php
namespace Media42\Model;

use Core42\Model\AbstractModel;

/**
 * @method Media setId() setId(int $id)
 * @method int getId() getId()
 * @method Media setDirectory() setDirectory(string $directory)
 * @method string getDirectory() getDirectory()
 * @method Media setFilename() setFilename(string $filename)
 * @method string getFilename() getFilename()
 * @method Media setCategory() setCategory(string $category)
 * @method string getCategory() getCategory()
 * @method Media setTitle() setTitle(string $title)
 * @method string getTitle() getTitle()
 * @method Media setDescription() setDescription(string $description)
 * @method string getDescription() getDescription()
 * @method Media setKeywords() setKeywords(string $keywords)
 * @method string getKeywords() getKeywords()
 * @method Media setMimeType() setMimeType(string $mimeType)
 * @method string getMimeType() getMimeType()
 * @method Media setSize() setSize(int $size)
 * @method int getSize() getSize()
 * @method Media setMeta() setMeta(string $meta)
 * @method string getMeta() getMeta()
 * @method Media setUpdated() setUpdated(\DateTime $updated)
 * @method \DateTime getUpdated() getUpdated()
 * @method Media setCreated() setCreated(\DateTime $created)
 * @method \DateTime getCreated() getCreated()
 */
class Media extends AbstractModel
{

    /**
     * @var array
     */
    public $properties = [
        'id',
        'directory',
        'filename',
        'category',
        'title',
        'description',
        'keywords',
        'mimeType',
        'size',
        'meta',
        'updated',
        'created',
    ];
}
