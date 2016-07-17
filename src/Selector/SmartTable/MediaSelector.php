<?php
/**
 * media42 (www.raum42.at)
 *
 * @link http://www.raum42.at
 * @copyright Copyright (c) 2010-2016 raum42 OG (http://www.raum42.at)
 *
 */

namespace Media42\Selector\SmartTable;

use Admin42\Selector\SmartTable\AbstractSmartTableSelector;
use Core42\Db\ResultSet\ResultSet;
use Media42\TableGateway\MediaTableGateway;
use Zend\Db\Sql\Select;

class MediaSelector extends AbstractSmartTableSelector
{
    /**
     * @return Select|string|ResultSet
     */
    protected function getSelect()
    {
        $gateway = $this->getTableGateway(MediaTableGateway::class);

        $select = $gateway->getSql()->select();

        $where = $this->getWhere();
        if (!empty($where)) {
            $select->where($where);
        }

        $order = $this->getOrder();
        if (!empty($order)) {
            $select->order($order);
        } else {
            $select->order('created DESC');
        }

        return $select;
    }

    /**
     * @return array
     */
    protected function getDatabaseTypeMap()
    {
        return [
            'id'      => 'Integer',
            'updated' => 'DateTime',
            'created' => 'DateTime',
        ];
    }

    /**
     * @return array
     */
    protected function getSearchAbleColumns()
    {
        return ['id', 'filename', 'category'];
    }

    /**
     * @return array
     */
    protected function getSortAbleColumns()
    {
        return ['id', 'filename', 'created'];
    }

    /**
     * @return array
     */
    protected function getDisplayColumns()
    {
        return ['id', 'directory', 'filename', 'mimeType', 'size', 'meta', 'updated', 'created'];
    }
}
