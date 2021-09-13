<?php

namespace nilsenpaul\bitlyconnect\services;

use Craft;
use craft\helpers\ArrayHelper;
use yii\base\Component;
use nilsenpaul\bitlyconnect\Plugin;
use nilsenpaul\bitlyconnect\helpers\Bitly;
use nilsenpaul\bitlyconnect\elements\Bitlink as BitlinkElement;
use yii\base\InvalidConfigException;

class Bitlink extends Component
{
    protected $_existingBitlinks = null;

    public function createOrShowBitlink(string $longUrl, string $domain = null, string $group = null): ?BitlinkElement
    {
        $this->checkConfig();

        $longUrl = $this->sanitizeUrl($longUrl);

        // Check if this URL exists
        $existingBitlink = $this->getBitlinkForUrl($longUrl);

        if ($existingBitlink) {
            return $existingBitlink;
        }

        $bitly = new Bitly();
        $result = $bitly->shorten(
            $longUrl,
            $domain,
            $group
        );

        if ($result !== null) {
            // Create a new Bitlink element and save it
            $bitlink = new BitlinkElement();
            $bitlink->longUrl = $longUrl;
            $bitlink->bitlyId = $result['id'];
            $bitlink->link = $result['link'];
            $bitlink->group = self::getGroupHashFromUrl($result['references']['group']);

            if (Craft::$app->getElements()->saveElement($bitlink)) {
                return $bitlink;
            } else {
                // TODO: error logging
            }
        }

        return null;
    }

    protected function checkConfig()
    {
        $settings = Plugin::$instance->getSettings();

        if (empty($settings->accessToken)) {
            throw new InvalidConfigException('To use one of the Bitly filters, please enter your access token on the plugin\'s settings page.');
        }
    }

    protected static function getGroupHashFromUrl($url)
    {
        $bitly = new Bitly();
        return str_replace($bitly->apiUrl . 'groups/', '', $url);
    }

    protected function getBitlinkForUrl(string $longUrl): ?BitlinkElement
    {
        foreach ($this->getExistingBitlinks() as $bitlink) {
            if ($bitlink->longUrl == $longUrl) {
                return $bitlink;
            }
        }

        return null;
    }

    protected function getExistingBitlinks()
    {
        if ($this->_existingBitlinks === null) {
            $this->_existingBitlinks = ArrayHelper::index(BitlinkElement::find()->all(), 'longUrl');
        }

        return $this->_existingBitlinks;
    }

    protected function sanitizeUrl(string $url): string
    {
        return trim($url);
    }
}
