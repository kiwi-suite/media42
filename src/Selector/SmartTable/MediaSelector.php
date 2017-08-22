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

        if (!empty($this->search['typeSelection']) && in_array($this->search['typeSelection'], ['images', 'pdf'])) {
            $typeSearchWhere = new Where();
            if ($this->search['typeSelection'] == 'images') {
                $typeSearchWhere->in('mimeType', [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'image/tiff',
                    'image/bmp',
                    'image/bmp',
                ]);
            } elseif ($this->search['typeSelection'] == 'pdf') {
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

        if (!empty($this->search['categorySelection']) && $this->search['categorySelection'] !== '*') {
            $categorySearchWhere = new Where();

            $categorySearchWhere->equalTo('category', $this->search['categorySelection']);

            if (empty($where)) {
                $where = $categorySearchWhere;
            } else {
                $predicateSet = new PredicateSet([$where, $categorySearchWhere], PredicateSet::COMBINED_BY_AND);
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
        return ['filename'];
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
