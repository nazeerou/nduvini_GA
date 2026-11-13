<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'branch_id',
        'subject',       // short human title, e.g. "Proforma number changed"
        'description',   // longer explanation
        'action',        // create | update | delete | login | other
        'model_type',    // e.g. 'estimations', 'labour_estimations'
        'model_id',      // affected record id
        'reference',     // estimation reference for easy filtering
        'old_values',    // JSON snapshot before
        'new_values',    // JSON snapshot after
        'changes',       // JSON of only what changed (key => {old, new})
        'url',
        'method',
        'ip',
        'agent',
        'is_critical',   // bool — true for critical actions
        'severity',      // info | warning | critical
    ];

    protected $casts = [
        'old_values'  => 'array',
        'new_values'  => 'array',
        'changes'     => 'array',
        'is_critical' => 'boolean',
    ];

    /* ----------------------- Relationships ----------------------- */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* ----------------------- Scopes ------------------------------ */

    public function scopeCritical($query)
    {
        return $query->where('is_critical', true);
    }

    public function scopeForReference($query, $reference)
    {
        return $query->where('reference', $reference);
    }

    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }
}