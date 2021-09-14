<?php

namespace nilsenpaul\bitlyconnect\elements;

use Craft;
use craft\base\Element;
use craft\elements\db\ElementQueryInterface;
use nilsenpaul\bitlyconnect\helpers\Bitly;
use nilsenpaul\bitlyconnect\elements\Bitlink;
use nilsenpaul\bitlyconnect\elements\db\BitlinkQuery;

class Bitlink extends Element
{
    public $id;
    public $longUrl;
    public $bitlyId;
    public $link;
    public $group;
    public $totalClicks;

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
                ->update('{{%bitlyconnect_links}}', [
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

    public function getClicks()
    {
        $cacheIdentifier = 'bitly-connect--totalClicks--' . $this->id;

        if (!Craft::$app->getCache()->get($cacheIdentifier)) {
            $cacheDuration = rand(900, 1800);

            $bitly = new Bitly();

            $this->totalClicks = $bitly->getTotalClicksForBitlink($this);

            if (Craft::$app->getElements()->saveElement($this)) {
                Craft::$app->getCache()->set($cacheIdentifier, $this->totalClicks, $cacheDuration);
            }
        }

        return Craft::$app->getCache()->get($cacheIdentifier);
    }
}
