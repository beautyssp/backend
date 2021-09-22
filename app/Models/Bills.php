<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Bills extends Model
{
    use SoftDeletes;
    public $table = 'bills';
    protected $fillable = ['client_id','warehouse_id','total','discounts','subtotal','create_by','last_update_by'];
}
