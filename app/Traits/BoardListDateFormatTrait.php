<?php

namespace App\Traits;

use Carbon\Carbon;

trait BoardListDateFormatTrait
{
    private function formatTimestamp($timestamp): ?string
    {
        if (!$timestamp) {
            return null;
        }
        $activityTimezone = 'Asia/Karachi';
        $timestamp = Carbon::parse($timestamp)->timezone($activityTimezone)->subHours(15);
        $now = Carbon::now($activityTimezone);
        $diffInSeconds = $now->diffInSeconds($timestamp);
        if ($diffInSeconds === 0) {
            return 'just now';
        }
        if ($diffInSeconds > 0 && $diffInSeconds < 10) {
            return 'few seconds ago';
        }
        if ($diffInSeconds > 9 && $diffInSeconds < 60) {
            return $diffInSeconds . ' seconds ago';
        }
        $diffInMinutes = $now->diffInMinutes($timestamp);
        if ($diffInMinutes === 1) {
            return '1 minute ago';
        }
        if ($diffInMinutes < 60) {
            return $diffInMinutes . ' minutes ago';
        }
        $diffInHours = $now->diffInHours($timestamp);
        if ($diffInHours === 1) {
            return '1 hour ago';
        }
        if ($diffInHours < 24) {
            return $diffInHours . ' hours ago';
        }
        $diffInDays = $now->diffInDays($timestamp);
        if ($diffInDays === 1) {
            return 'yesterday at ' . $timestamp->format('h:i A');
        }
//        if ($diffInDays < 30) {
//            return $diffInDays . ' days ago';
//        }
        return $timestamp->format('F jS \a\t h:i A');
    }
}
