<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Kirim notifikasi ke satu user
     */
    public static function sendToUser($userId, $title, $message, $type = 'info', $data = null)
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal mengirim notifikasi ke user ' . $userId . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi ke banyak user
     */
    public static function sendToMany(array $userIds, $title, $message, $type = 'info', $data = null)
    {
        $results = [];

        foreach ($userIds as $userId) {
            $results[$userId] = self::sendToUser($userId, $title, $message, $type, $data);
        }

        return $results;
    }

    /**
     * Kirim notifikasi ke semua user dengan role tertentu (misal: 'admin')
     */
    public static function sendToRole($role, $title, $message, $type = 'info', $data = null)
    {
        $users = User::where('role', $role)->pluck('id');
        return self::sendToMany($users->toArray(), $title, $message, $type, $data);
    }

    /**
     * Kirim notifikasi ke semua user (hati-hati untuk sistem besar)
     */
    public static function sendToAllUsers($title, $message, $type = 'info', $data = null)
    {
        $userIds = User::pluck('id')->toArray();
        return self::sendToMany($userIds, $title, $message, $type, $data);
    }
}
