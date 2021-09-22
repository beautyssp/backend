<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FilesModel extends Model
{
    public $table = 'files';
    protected $fillable = ['name','type','observations'];
}
