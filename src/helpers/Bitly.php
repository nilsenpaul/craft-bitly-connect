<?php

namespace nilsenpaul\bitlyconnect\helpers;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use nilsenpaul\bitlyconnect\Plugin;
use nilsenpaul\bitlyconnect\elements\Bitlink;

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
            Plugin::error('Something went wrong while creating a new Bitlink: {errors}', [
                'errors' => json_encode($e->getMessage()),
            ]);
        }

        return null;
    }

    public function getDomains(): ?array
    {
        try {
            $response = $this->getClient()->request(
                'GET',
                'bsds/'
            );

            return Json::decode($response->getBody(), true)['bsds'];
        } catch (\Exception $e) {
            Plugin::error('Something went wrong while fetching the available domains for your Bitly account', [
                'errors' => json_encode($e->getMessage()),
            ]);
        }

        return null;
    }

    public function getGroups(): ?array
    {
        try {
            $response = $this->getClient()->request(
                'GET',
                'groups/'
            );

            $groups = Json::decode($response->getBody())['groups'];

            return ArrayHelper::map($groups, 'guid', 'name');
        } catch (\Exception $e) {
            Plugin::error('Something went wrong while fetching the available groups for your Bitly account', [
                'errors' => json_encode($e->getMessage()),
            ]);
        }

        return null;
    }

    public function getTotalClicksForBitlink(Bitlink $bitlink): int
    {
        $response = $this->getClient()->request(
            'GET',
            'bitlinks/' . $bitlink->bitlyId . '/clicks/summary?unit=month&units=-1'
        );

        if ($response) {
            $response = Json::decode($response->getBody());
            return $response['total_clicks'];
        }
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
