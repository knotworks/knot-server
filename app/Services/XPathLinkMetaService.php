<?php

namespace Knot\Services;

use Knot\Contracts\LinkMetaService;
use Symfony\Component\DomCrawler\Crawler;

class XPathLinkMetaService implements LinkMetaService
{
    protected function getMetaForUrl(string $url)
    {
        $metaFields = [
            'title' => null,
            'description' => null,
            'twitter:title' => null,
            'twitter:description' => null,
            'twitter:image' => null,
            'og:title' => null,
            'og:description' => null,
            'og:image' => null,
            'author' => null,
        ];

        $html = file_get_contents($url);

        if (! $html) {
            return $metaFields;
        }

        $crawler = new Crawler($html);

        tap($crawler->filterXPath('//title'), function (Crawler $titleNode) use (&$metaFields) {
            $metaFields['title'] = (bool) $titleNode->text('') ?
                $titleNode->text() :
                null;
        });

        $crawler->filterXPath('//meta')->each(function (Crawler $node) use (&$metaFields) {
            $key = strtolower($node->attr('name') ?? $node->attr('property'));

            if (in_array($key, array_keys($metaFields))) {
                $metaFields[$key] = $node->attr('content');
            }
        });

        return $metaFields;
    }

    protected function getImageForMetaHash($hash)
    {
        return $hash['og:image'] ?: $hash['twitter:image'];
    }

    public function fetch($url)
    {
        $url = str_starts_with($url, 'http') ? $url : "http://{$url}";
        $meta = $this->getMetaForUrl($url);

        return [
            'title' => $meta['title'],
            'description' => $meta['description'],
            'image' => str_replace('http://', 'https://', $this->getImageForMetaHash($meta)),
        ];
    }
}
