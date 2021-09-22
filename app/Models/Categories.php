<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use SoftDeletes;
    public $table = 'categories';
    protected $fillable = ['name','supplier_id','create_by','last_update_by'];

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function subcategories(){
        return $this->hasMany(Subcategories::class, 'category_id');
    }

}
