<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Log an event to the database and standard application log.
     *
     * @param string $eventType
     * @param string $description
     * @param int|null $userId
     */
    public static function log(string $eventType, string $description, ?int $userId = null): void
    {
        $resolvedUserId = $userId ?: (Auth::check() ? Auth::id() : null);
        $sessionId = session()->getId();
        $ipAddress = Request::ip();
        $userAgent = Request::header('User-Agent');
        $url = Request::fullUrl();
        $method = Request::method();

        // Save to Database (Requirement 8)
        try {
            // Use native query builder to avoid any Eloquent boot circular issues
            \Illuminate\Support\Facades\DB::table('activity_logs')->insert([
                'user_id' => $resolvedUserId,
                'session_id' => $sessionId,
                'event_type' => $eventType,
                'description' => $description,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'url' => $url,
                'method' => $method,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Fail silently or log error
            Log::error('Failed to save activity log to database: ' . $e->getMessage());
        }

        // Save to standard log in common JSON format (Requirements 12 & 13)
        $logPayload = [
            'timestamp' => now()->toIso8601String(),
            'user_id' => $resolvedUserId,
            'session_id' => $sessionId,
            'event_type' => $eventType,
            'description' => $description,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'url' => $url,
            'method' => $method,
        ];

        Log::channel('single')->info('ACTIVITY_LOG: ' . json_encode($logPayload));
    }
}
