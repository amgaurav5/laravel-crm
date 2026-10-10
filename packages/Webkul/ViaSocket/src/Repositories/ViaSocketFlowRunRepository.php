<?php

namespace Webkul\ViaSocket\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\ViaSocket\Contracts\ViaSocketFlowRun;

class ViaSocketFlowRunRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ViaSocketFlowRun::class;
    }
}
