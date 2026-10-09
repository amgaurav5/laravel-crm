<?php

namespace Webkul\ViaSocket\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\ViaSocket\Contracts\ViaSocketFlow as ViaSocketFlowContract;

class ViaSocketFlow extends Model implements ViaSocketFlowContract
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'viasocket_flows';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'flow_id',
        'user_id',
        'title',
        'webhook_url',
        'status',
        'payload',
    ];

    /**
     * The attributes that are castable. The flow URL runs the user's apps, so it is stored encrypted.
     *
     * @var array
     */
    protected $casts = [
        'webhook_url' => 'encrypted',
        'payload' => 'array',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'webhook_url',
    ];
}
