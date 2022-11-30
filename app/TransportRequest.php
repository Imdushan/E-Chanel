<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportRequest extends Model
{
    use SoftDeletes;
    protected $table = 'transport_request';
}
