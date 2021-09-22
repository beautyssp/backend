<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use SoftDeletes;
    public $table = 'products';
    protected $fillable = ['ean', 'sku_plu', 'name', 'price', 'supplier_id', 'subcategory_id', 'brand', 'units', 'health_register_file_id', 'create_by','last_update_by'];

    public function subcategory(){
        return $this->hasOne(Subcategories::class, 'id', 'subcategory_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(){
        return $this->belongsTo(Warehouses::class);
    }

    public function health_register(){
        return $this->hasOne(FilesModel::class, 'id', 'health_register_file_id');
    }

}
