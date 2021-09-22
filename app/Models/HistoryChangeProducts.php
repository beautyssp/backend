<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class HistoryChangeProducts extends Model
{
    use SoftDeletes;
    public $table = 'history_change_products';
    protected $fillable = ['quantity','product_id','warehouse_from','warehouse_to','create_by'];

    public function product(){
        return $this->hasOne(Products::class, 'id', 'product_id');
    }

    public function warehouse_from(){
        return $this->hasOne(Warehouses::class, 'id', 'warehouse_from');
    }

    public function warehouse_to(){
        return $this->hasOne(Warehouses::class, 'id', 'warehouse_to');
    }
}
