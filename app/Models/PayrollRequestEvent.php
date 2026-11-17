<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollRequestEvent extends Model
{
    protected $guarded = [];
    public function request() { return $this->belongsTo(PayrollRequest::class); }
    public function actor()   { return $this->belongsTo(User::class, 'actor_id'); }
}