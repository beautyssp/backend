<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Subcategories extends Model
{
    use SoftDeletes;
    public $table = 'subcategories';
    protected $fillable = ['name','category_id','create_by','last_update_by','delete_by'];

    public function category(){
        return $this->belongsTo(Categories::class, 'category_id', 'id');
    }
}
