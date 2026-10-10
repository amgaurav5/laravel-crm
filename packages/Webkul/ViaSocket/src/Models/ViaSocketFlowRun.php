<?php

namespace Webkul\ViaSocket\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\ViaSocket\Contracts\ViaSocketFlowRun as ViaSocketFlowRunContract;

class ViaSocketFlowRun extends Model implements ViaSocketFlowRunContract
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'viasocket_flow_runs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'flow_id',
        'event',
        'ok',
        'status_code',
        'message',
    ];

    /**
     * The attributes that are castable.
     *
     * @var array
     */
    protected $casts = [
        'ok' => 'boolean',
    ];
}
