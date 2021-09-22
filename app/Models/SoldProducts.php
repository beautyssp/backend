<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SoldProducts extends Model
{
    use SoftDeletes;
    public $table = 'sold_products';
    protected $fillable = ['quantity','total','bill_id','product_id'];
}
