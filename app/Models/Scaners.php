<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scaners extends Model
{
    use HasFactory;
    public $table = 'scaners';
    protected $fillable = ['name','socket','user_id'];

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

}
