<?php
namespace nilsenpaul\bitlyconnect\elements\db;

use craft\elements\db\ElementQuery;
use craft\helpers\Db;

class BitlinkQuery extends ElementQuery
{
    public $id;
    public $longUrl;
    public $bitlyId;
    public $link;
    public $group;

    public function longUrl($value)
    {
        $this->longUrl = $value;

        return $this;
    }

    public function bitlyId($value)
    {
        $this->bitlyId = $value;

        return $this;
    }

    public function link($value)
    {
        $this->link = $value;

        return $this;
    }

    public function group($value)
    {
        $this->group = $value;

        return $this;
    }

    protected function beforePrepare(): bool
    {
        // join in the products table
        $this->joinElementTable('bitlyconnect_links');

        // select the price column
        $this->query->select([
            'bitlyconnect_links.longUrl',
            'bitlyconnect_links.bitlyId',
            'bitlyconnect_links.link',
            'bitlyconnect_links.group',
        ]);

        if ($this->longUrl) {
            $this->subQuery->andWhere(Db::parseParam('bitlyconnect_links.longUrl', $this->longUrl));
        }

        if ($this->bitlyId) {
            $this->subQuery->andWhere(Db::parseParam('bitlyconnect_links.bitlyId', $this->bitlyId));
        }

        if ($this->link) {
            $this->subQuery->andWhere(Db::parseParam('bitlyconnect_links.link', $this->link));
        }

        if ($this->group) {
            $this->subQuery->andWhere(Db::parseParam('bitlyconnect_links.group', $this->group));
        }

        return parent::beforePrepare();
    }
}
