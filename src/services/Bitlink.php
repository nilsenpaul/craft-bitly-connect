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
        $settings = Plugin::$instance->getSettings();

        $this->checkConfig();

        $longUrl = $this->sanitizeUrl($longUrl);

        // Check if this is a valid URL
        if (filter_var($longUrl, FILTER_VALIDATE_URL) === false) {
            Plugin::warning($longUrl . ' is not a valid URL, so no Bitlink could be made');

            return null;
        }

        // Check if this URL exists
        if ($existingBitlink = $this->getBitlinkForUrl($longUrl)) {
            Plugin::info('The existing Bitlink {bitlink} was found for the URL {url}, no new Bitlink was created', [
                'bitlink' => $existingBitlink->link,
                'url' => $longUrl,
            ]);

            return $existingBitlink;
        }

        // Should we use a default custom domain?
        if ($domain === null && !empty($settings->domain)) {
            $domain = $settings->domain;
        }

        // Should we use a default group?
        if ($group === null && !empty($settings->group)) {
            $group = $settings->group;
        }

        // Create a new Bitlink
        $bitly = new Bitly();
        $result = $bitly->shorten(
            $longUrl,
            $domain,
            $group
        );

        if ($result !== null) {
            $bitlink = new BitlinkElement();
            $bitlink->longUrl = $longUrl;
            $bitlink->bitlyId = $result['id'];
            $bitlink->link = $result['link'];
            $bitlink->group = self::getGroupHashFromUrl($result['references']['group']);

            if (Craft::$app->getElements()->saveElement($bitlink)) {
                Plugin::info('The new Bitlink {bitlink} was created for URL {url}', [
                    'bitlink' => $bitlink->link,
                    'url' => $longUrl,
                ]);

                return $bitlink;
            } else {
                Plugin::error('Something went wrong while saving your new Bitlink: {errors}', [
                    'errors' => json_encode($bitlink->errors),
                ]);
            }
        }

        return null;
    }

    protected function checkConfig()
    {
        $settings = Plugin::$instance->getSettings();

        if (empty($settings->accessToken)) {
            Plugin::error('No Bitly access token was configured, so no Bitlinks will be created');

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
