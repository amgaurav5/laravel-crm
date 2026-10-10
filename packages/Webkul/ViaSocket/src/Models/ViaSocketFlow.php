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
        'user_id',
        'script_id',
        'title',
        'trigger',
        'steps',
        'status',
    ];

    /**
     * The attributes that are castable. The flow id runs the user's apps, so it is stored encrypted.
     *
     * @var array
     */
    protected $casts = [
        'script_id' => 'encrypted',
        'trigger' => 'array',
        'steps' => 'array',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'script_id',
    ];
}
