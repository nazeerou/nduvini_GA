<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApprovalTimeline extends Model
{
    public $timestamps = false;
    protected $fillable = ['approval_request_id', 'action', 'actor_id', 'note', 'created_at'];
    protected $casts = ['created_at' => 'datetime'];

    public function actor()  { return $this->belongsTo(User::class, 'actor_id'); }
    public function request() { return $this->belongsTo(ApprovalRequest::class, 'approval_request_id'); }
}