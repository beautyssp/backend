<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;
    public $table = 'suppliers';
    protected $fillable = [
        'name',
        'nit',
        'email',
        'telephone',
        'cellphone',
        'address',
        'city',
        'country',
        'legal_representative',
        'type_person',
        'economic_activity',
        'banco',
        'bank_certificate',
        'create_by',
        'last_update_by'
    ];

    public function UserCreator(){
        return $this->belongsTo(User::class, 'create_by');
    }

    public function UserUpdate(){
        return $this->belongsTo(User::class, 'last_update_by');
    }

    public function certificate(){
        return $this->hasOne(FilesModel::class, 'id', 'bank_certificate');
    }

    public function categories(){
        return $this->hasMany(Categories::class);
    }

}
