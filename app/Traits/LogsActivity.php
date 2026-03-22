<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    protected function logChanges(
        string $modelType,
        int $modelId,
        array $oldData,
        array $newData,
        array $watchedFields
    ): void {
        $now = now();

        foreach ($watchedFields as $field) {
            $oldVal = $oldData[$field] ?? null;
            $newVal = $newData[$field] ?? null;

            // Skip kalau tidak ada perubahan
            if ($oldVal === $newVal) continue;

            ActivityLog::create([
                'user_id'    => auth()->id(),
                'model_type' => $modelType,
                'model_id'   => $modelId,
                'field_name' => $field,
                'old_value'  => $oldVal,
                'new_value'  => $newVal,
                'created_at' => $now, // sama supaya dikelompokkan
            ]);
        }
    }

    protected function logItemsChange(int $attendanceId, array $oldItems, array $newItems): void
    {
        // Bandingkan sebagai JSON, skip kalau sama
        $oldJson = json_encode($oldItems);
        $newJson = json_encode($newItems);

        if ($oldJson === $newJson) return;

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'model_type' => 'attendance_items',
            'model_id'   => $attendanceId,
            'field_name' => 'items',
            'old_value'  => $oldJson,
            'new_value'  => $newJson,
            'created_at' => now(),
        ]);
    }
}