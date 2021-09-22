<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class QuantityProducts extends Model
{
    use SoftDeletes;
    public $table = 'quantity_products';
    protected $fillable = ['product_id','warehouse_id','quantity','price','create_by','last_update_by'];

    public function product(){
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    public function warehouse(){
        return $this->belongsTo(Warehouses::class, 'product_id');
    }
}
