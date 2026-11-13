<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Record an audit entry.
     *
     * @param  string       $subject      Short title, e.g. "Proforma number changed"
     * @param  string       $action       create | update | delete | login | other
     * @param  string|null  $modelType    e.g. 'estimations'
     * @param  mixed        $modelId      affected row id
     * @param  array        $oldValues    Snapshot before change
     * @param  array        $newValues    Snapshot after change
     * @param  string|null  $description  Longer text
     * @param  bool         $isCritical   true = critical
     * @param  string|null  $reference    estimation reference
     */
    public static function log(
        string $subject,
        string $action = 'update',
        ?string $modelType = null,
        $modelId = null,
        array $oldValues = [],
        array $newValues = [],
        ?string $description = null,
        bool $isCritical = false,
        ?string $reference = null
    ): AuditLog {
        $changes = self::diff($oldValues, $newValues);

        $severity = 'info';
        if ($isCritical) {
            $severity = 'critical';
        } elseif (in_array($action, ['delete', 'update']) && !empty($changes)) {
            $severity = 'warning';
        }

        return AuditLog::create([
            'user_id'     => Auth::id(),
            'branch_id'   => Auth::user()->branch_id ?? null,
            'subject'     => $subject,
            'description' => $description,
            'action'      => $action,
            'model_type'  => $modelType,
            'model_id'    => $modelId,
            'reference'   => $reference,
            'old_values'  => $oldValues ?: null,
            'new_values'  => $newValues ?: null,
            'changes'     => $changes ?: null,
            'url'         => Request::fullUrl(),
            'method'      => Request::method(),
            'ip'          => Request::ip(),
            'agent'       => substr((string) Request::header('User-Agent'), 0, 490),
            'is_critical' => $isCritical,
            'severity'    => $severity,
        ]);
    }

    /**
     * Compare two arrays and return key => ['old' => .., 'new' => ..].
     */
    protected static function diff(array $old, array $new): array
    {
        if (empty($old) && empty($new)) {
            return [];
        }

        $changes = [];
        $keys = array_unique(array_merge(array_keys($old), array_keys($new)));

        foreach ($keys as $key) {
            $o = $old[$key] ?? null;
            $n = $new[$key] ?? null;

            if ($o != $n) {
                $changes[$key] = ['old' => $o, 'new' => $n];
            }
        }

        return $changes;
    }
}