<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Barcodes extends Model
{
    use SoftDeletes;
    public $table = 'barcodes';
    protected $fillable = ['path_barcode','product_id'];

    public function product(){
        return $this->belongsTo(Products::class);
    }

}
