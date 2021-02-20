<?php

namespace Knot\Services;

use OpenGraph;
use Knot\Contracts\LinkMetaService;

class OpenGraphLinkMetaService implements LinkMetaService {
    public function fetch($url) {
        $response = OpenGraph::fetch($url, true);

        return [
            'title' => $response['title'] ?? $response['site_name'],
            'description' => $response['description'],
            'image' => $response['image'],
        ];
    }
}
