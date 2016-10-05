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
use Zend\Db\Sql\Predicate\PredicateSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

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

        if (!empty($this->search['typeSearch']) && in_array($this->search['typeSearch'], ['images', 'pdf'])) {
            $typeSearchWhere = new Where();
            if ($this->search['typeSearch'] == 'images') {
                $typeSearchWhere->in('mimeType', [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'image/tiff',
                    'image/bmp',
                    'image/bmp',
                ]);
            } elseif ($this->search['typeSearch'] == 'pdf') {
                $typeSearchWhere->in('mimeType', [
                    'application/pdf',
                ]);
            }

            if (empty($where)) {
                $where = $typeSearchWhere;
            } else {
                $predicateSet = new PredicateSet([$where, $typeSearchWhere], PredicateSet::COMBINED_BY_AND);
                $where = $predicateSet;
            }
        }

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
            'id'      => 'integer',
            'updated' => 'dateTime',
            'created' => 'dateTime',
        ];
    }

    /**
     * @return array
     */
    protected function getSearchAbleColumns()
    {
        return ['id', 'filename', 'category', 'mimeType'];
    }

    /**
     * @return array
     */
    protected function getSortAbleColumns()
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getDisplayColumns()
    {
        return ['id', 'directory', 'filename', 'mimeType', 'size'];
    }
}
