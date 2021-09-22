<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Warehouses extends Model
{
    use SoftDeletes;
    public $table = 'warehouses';
    protected $fillable = ['description','percent_to_change','create_by','last_update_by','delete_by'];

    public function UserCreator(){
        return $this->belongsTo(User::class, 'create_by');
    }

    public function UserUpdate(){
        return $this->belongsTo(User::class, 'last_update_by');
    }

    public function QuantityProducts(){
        return $this->hasMany(QuantityProducts::class, 'warehouse_id')->where('quantity','!=','0');
    }

    public function OutHistory(){
        return $this->hasMany(HistoryChangeProducts::class, 'warehouse_from');
    }

    public function InHistory(){
        return $this->hasMany(HistoryChangeProducts::class, 'warehouse_to');
    }

}
