<?php
/* filepath: app/Http/Controllers/API/NotificationController.php */

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Get all notifications for authenticated user
     */
    public function index(Request $request)
    {
        try {
            $query = Notifikasi::where('id_user', Auth::id())
                ->orderBy('created_at', 'desc');

            if ($request->has('unread_only') && $request->unread_only) {
                $query->where('is_read', false);
            }

            $notifications = $query->get();

            // Add time_ago untuk setiap notifikasi
            $notifications->each(function ($notification) {
                $notification->time_ago = $this->getTimeAgo($notification->created_at);
            });

            return response()->json([
                'success' => true,
                'data' => $notifications
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat notifikasi'
            ], 500);
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        try {
            $count = Notifikasi::where('id_user', Auth::id())
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'count' => $count
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'count' => 0
            ], 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notifikasi::where('id_notifikasi', $id)
                ->where('id_user', Auth::id())
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            $notification->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi ditandai sebagai dibaca'
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai notifikasi sebagai dibaca'
            ], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            $updatedCount = Notifikasi::where('id_user', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi ditandai sebagai dibaca',
                'updated_count' => $updatedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menandai semua notifikasi sebagai dibaca'
            ], 500);
        }
    }

    /**
     * Delete specific notification
     */
    public function destroy($id)
    {
        try {
            $notification = Notifikasi::where('id_notifikasi', $id)
                ->where('id_user', Auth::id())
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notifikasi tidak ditemukan'
                ], 404);
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi'
            ], 500);
        }
    }

    /**
     * Clear all notifications
     */
    public function clearAll()
    {
        try {
            $deletedCount = Notifikasi::where('id_user', Auth::id())->delete();

            return response()->json([
                'success' => true,
                'message' => 'Semua notifikasi berhasil dihapus',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing all notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus semua notifikasi'
            ], 500);
        }
    }

    /**
     * Clear read notifications
     */
    public function clearRead()
    {
        try {
            $deletedCount = Notifikasi::where('id_user', Auth::id())
                ->where('is_read', true)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi yang dibaca berhasil dihapus',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing read notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi yang dibaca'
            ], 500);
        }
    }

    /**
     * Clear expired notifications
     */
    public function clearExpired()
    {
        try {
            $deletedCount = Notifikasi::where('id_user', Auth::id())
                ->where('created_at', '<', now()->subDays(30)) // 30 hari yang lalu
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Notifikasi kedaluwarsa berhasil dihapus',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing expired notifications: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus notifikasi kedaluwarsa'
            ], 500);
        }
    }

    /**
     * Helper function to get time ago
     */
    private function getTimeAgo($date)
    {
        $carbon = Carbon::parse($date);
        
        if ($carbon->isToday()) {
            return $carbon->diffForHumans();
        } elseif ($carbon->isYesterday()) {
            return 'Kemarin ' . $carbon->format('H:i');
        } elseif ($carbon->diffInDays() <= 7) {
            return $carbon->format('l H:i');
        } else {
            return $carbon->format('d M Y H:i');
        }
    }
}
