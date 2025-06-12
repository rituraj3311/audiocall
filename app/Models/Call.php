<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Call extends Model
{
    protected $fillable = ['caller_id','receiver_id','started_at','ended_at'];

    public function CallerUser(){
        return $this->belongsTo(User::class,'caller_id');
    }
    public function ReceiverUser(){
        return $this->belongsTo(User::class,'receiver_id');
    }
}
