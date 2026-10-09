<?php

namespace Webkul\ViaSocket\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\ViaSocket\Contracts\ViaSocketFlow;

class ViaSocketFlowRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ViaSocketFlow::class;
    }
}
