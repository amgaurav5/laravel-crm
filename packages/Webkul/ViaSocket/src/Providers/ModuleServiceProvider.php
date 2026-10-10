<?php

namespace Webkul\ViaSocket\Providers;

use Webkul\Core\Providers\BaseModuleServiceProvider;
use Webkul\ViaSocket\Models\ViaSocketConnection;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        ViaSocketConnection::class,
    ];
}
