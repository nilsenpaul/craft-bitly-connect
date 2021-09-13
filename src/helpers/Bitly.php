<?php

namespace nilsenpaul\bitlyconnect\helpers;

use Craft;
use craft\helpers\Json;
use nilsenpaul\bitlyconnect\Plugin;

class Bitly
{
    public $apiUrl = 'https://api-ssl.bitly.com/v4/';
    protected $_client = null;

    public function shorten(string $url, string $domain = null, string $group = null): ?array
    {
        $data = [
            'long_url' => $url,
        ];

        if (!empty($domain)) {
            $data['domain'] = $domain;
        }

        if (!empty($group)) {
            $data['group'] = $group;
        }

        try {
            $response = $this->getClient()->request(
                'POST',
                'shorten/',
                [
                    'json' => $data,
                ]
            );

            return Json::decode($response->getBody(), true);
        } catch (\Exception $e) {
            // TODO: error logging
            var_dump($e->getMessage());
        }

        return null;
    }

    protected function getClient()
    {
        if ($this->_client === null) {
            $settings = Plugin::$instance->getSettings();

            $this->_client = new \GuzzleHttp\Client([
                'base_uri' => $this->apiUrl,
                'timeout' => 10,
                'headers' => [
                    'Authorization' => 'Bearer ' . Craft::parseEnv($settings->accessToken),
                ],
            ]);
        }

        return $this->_client;
    }
}
