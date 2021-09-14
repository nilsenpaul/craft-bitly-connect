<?php

namespace nilsenpaul\bitlyconnect\widgets;

use Craft;
use craft\base\Widget;
use craft\helpers\ArrayHelper;
use nilsenpaul\bitlyconnect\elements\Bitlink;
use nilsenpaul\bitlyconnect\Plugin;

class Bitlinks extends Widget
{
    const DEFAULT_LIMIT = 10;
    const DEFAULT_ORDER = 'dateCreated_desc';

    protected static $availableSortTypes = [
        [
            'key' => 'dateCreated_asc',
            'sql' => 'elements.dateCreated asc',
            'label' => 'By date created, ascending',
        ],
        [
            'key' => 'dateCreated_desc',
            'sql' => 'elements.dateCreated desc',
            'label' => 'By date created, descending',
        ],
        [
            'key' => 'url_asc',
            'sql' => 'bitlyconnect_links.longUrl asc',
            'label' => 'By url, alfabeticaly, ascending',
        ],
        [
            'key' => 'url_desc',
            'sql' => 'bitlyconnect_links.longUrl desc',
            'label' => 'By url, alfabeticaly, descending',
        ],
    ];

    public static function displayName(): string
    {
        return Craft::t('app', 'Bitlinks');
    }

    public static function icon()
    {
        return Plugin::$instance->basePath . '/icon.svg';
    }

    public $limit = self::DEFAULT_LIMIT;
    public $orderBy = self::DEFAULT_ORDER;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function defineRules(): array
    {
        $rules = parent::defineRules();
        $rules[] = [['limit'], 'number', 'integerOnly' => true];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('bitly-connect/_widget/settings',
            [
                'widget' => $this,
                'orderOptions' => ArrayHelper::map(self::$availableSortTypes, 'key', 'label'),
            ]);
    }

    /**
     * @inheritdoc
     */
    public function getTitle(): string
    {
        return Craft::t('bitly-connect', 'Bitlinks');
    }

    /**
     * @inheritdoc
     */
    public function getBodyHtml()
    {
        $bitlinks = $this->_getBitlinks();

        return Craft::$app->view->renderTemplate('bitly-connect/_widget/body',
        [
            'bitlinks' => $bitlinks,
        ]);
    }

    /**
     * Returns the recent entries, based on the widget settings and user permissions.
     *
     * @return array
     */
    private function _getBitlinks(): array
    {
        $orderSql = $this->getOrderSql();

        if ($orderSql === null) {
            return [];
        }

        $query = Bitlink::find();
        $query->limit($this->limit ?: 100);
        $query->with(['author']);
        $query->orderBy($orderSql);

        return $query->all();
    }

    private function getOrderSql()
    {
        foreach (self::$availableSortTypes as $sortType) {
            if ($sortType['key'] == $this->orderBy) {
                return $sortType['sql'];
            }
        }

        return null;
    }
}
