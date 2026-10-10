<?php

namespace Webkul\ViaSocket\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\ViaSocket\Contracts\ViaSocketConnection;

class ViaSocketConnectionRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ViaSocketConnection::class;
    }
}
