<?php
// app/Services/ActivityLogService.php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

class ActivityLogService
{
    public function log(string $action, Model $model, array $oldData = null, array $newData = null)
    {
        return ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_data' => $oldData,
            'new_data' => $newData,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function getLogsForModel(Model $model)
    {
        return ActivityLog::where('model_type', get_class($model))
                         ->where('model_id', $model->id)
                         ->with('user')
                         ->orderBy('created_at', 'desc')
                         ->get();
    }

    public function getLogsForUser($userId)
    {
        return ActivityLog::where('user_id', $userId)
                         ->with('user')
                         ->orderBy('created_at', 'desc')
                         ->get();
    }

    public function getAllLogs($limit = 100)
    {
        return ActivityLog::with('user')
                         ->orderBy('created_at', 'desc')
                         ->limit($limit)
                         ->get();
    }
}