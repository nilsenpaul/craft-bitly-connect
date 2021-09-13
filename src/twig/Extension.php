<?php

namespace nilsenpaul\bitlyconnect\twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use nilsenpaul\bitlyconnect\Plugin;
use nilsenpaul\bitlyconnect\elements\Bitlink;

class Extension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('bitlink', [$this, 'bitlinkFilter']),
        ];
    }

    public function bitlinkFilter(string $longUrl, string $domain = null, string $group = null): string
    {
        return Plugin::$instance->bitlinks->createOrShowBitlink($longUrl, $domain, $group)->link ?? '';
    }
}
