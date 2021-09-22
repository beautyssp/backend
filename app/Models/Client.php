<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use SoftDeletes;
    public $table = 'clients';
    protected $fillable = ['name','lastname','email','cellphone','type_person','number_document','create_by','last_update_by'];
}
