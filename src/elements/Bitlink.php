<?php

namespace nilsenpaul\bitlyconnect\elements;

use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use nilsenpaul\bitlyconnect\elements\db\BitlinkQuery;

class Bitlink extends Element
{
    public $id;
    public $longUrl;
    public $bitlyId;
    public $link;
    public $group;

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return 'Bitlink';
    }

    /**
     * @inheritdoc
     */
    public static function pluralDisplayName(): string
    {
        return 'Bitlinks';
    }

    public function afterSave(bool $isNew)
    {
        if ($isNew) {
            \Craft::$app->db->createCommand()
                ->insert('{{%bitlyconnect_links}}', [
                    'id' => $this->id,
                    'longUrl' => $this->longUrl,
                    'bitlyId' => $this->bitlyId,
                    'link' => $this->link,
                    'group' => $this->group,
                ])
                ->execute();
        } else {
            \Craft::$app->db->createCommand()
                ->update('{{%products}}', [
                    'longUrl' => $this->longUrl,
                    'bitlyId' => $this->bitlyId,
                    'link' => $this->link,
                    'group' => $this->group,
                ], ['id' => $this->id])
                ->execute();
        }

        parent::afterSave($isNew);
    }

    public static function find(): ElementQueryInterface
    {
        return new BitlinkQuery(static::class);
    }
}
