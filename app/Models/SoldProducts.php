<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SoldProducts extends Model
{
    use SoftDeletes;
    public $table = 'sold_products';
    protected $fillable = ['quantity','total','discount','bill_id','product_id'];

    public function product(){
        return $this->belongsTo(Products::class);
    }

}
