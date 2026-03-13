<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\LinkMetaService;

class OpenGraphLinkMetaService implements LinkMetaService
{
    public function fetch($url)
    {
        return app(XPathLinkMetaService::class)->fetch($url);
    }
}
