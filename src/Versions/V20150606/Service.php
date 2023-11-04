<?php

declare(strict_types=1);

namespace Dew\Mns\Versions\V20150606;

use Dew\Mns\Versions\V20150606\Results\OpenServiceResult;

final class Service extends Api
{
    /**
     * Activate MNS service.
     */
    public function openService(): OpenServiceResult
    {
        $response = $this->post('/commonbuy/openservice');

        return new OpenServiceResult($response, $this->xml());
    }
}
