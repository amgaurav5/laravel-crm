<?php

namespace Webkul\ViaSocket\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\ViaSocket\Contracts\ViaSocketConnection as ViaSocketConnectionContract;

class ViaSocketConnection extends Model implements ViaSocketConnectionContract
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'viasocket_connections';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'service_id',
        'auth_id',
    ];

    /**
     * The attributes that are castable. The connection id grants access to the user's app, so it is stored encrypted.
     *
     * @var array
     */
    protected $casts = [
        'auth_id' => 'encrypted',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'auth_id',
    ];
}
