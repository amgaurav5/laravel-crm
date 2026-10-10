<?php

namespace Webkul\ViaSocket\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\ViaSocket\Models\ViaSocketConnection;
use Webkul\ViaSocket\Models\ViaSocketFlow;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        ViaSocketConnection::class,
        ViaSocketFlow::class,
    ];
}
