<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Paparan Pusat Notifikasi & Log Aktiviti Sistem
     */
    public function index(Request $request)
    {
        $query = SystemNotification::with('application')->latest();

        $user = Auth::user();

        // Jika pegawai jajahan, utamakan jajahan beliau
        if ($user && $user->isPegawaiJajahan() && $user->jajahan) {
            $query->where(function ($q) use ($user) {
                $q->where('jajahan', $user->jajahan)
                  ->orWhereNull('jajahan');
            });
        }

        // Tapis mengikut jenis aktiviti
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Tapis mengikut status baca
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        // Carian No Rujukan atau Tajuk
        if ($request->filled('carian')) {
            $search = $request->carian;
            $query->where(function ($q) use ($search) {
                $q->where('no_rujukan', 'LIKE', "%{$search}%")
                  ->orWhere('title', 'LIKE', "%{$search}%")
                  ->orWhere('message', 'LIKE', "%{$search}%");
            });
        }

        $notifications = $query->paginate(15)->withQueryString();

        $unreadCount = SystemNotification::where('is_read', false)->count();

        return view('admin.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Tandakan satu notifikasi sebagai dibaca
     */
    public function markAsRead($id)
    {
        $notification = SystemNotification::findOrFail($id);
        $notification->update(['is_read' => true]);

        if ($notification->action_url) {
            return redirect($notification->action_url);
        }

        return redirect()->back()->with('success', 'Notifikasi ditandakan sebagai dibaca.');
    }

    /**
     * Tandakan semua notifikasi sebagai dibaca
     */
    public function markAllAsRead()
    {
        SystemNotification::where('is_read', false)->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandakan sebagai dibaca.');
    }

    /**
     * Dapatkan data notifikasi terkini untuk Dropdown Topbar (JSON)
     */
    public function getLatest()
    {
        $user = Auth::user();
        $query = SystemNotification::latest();

        if ($user && $user->isPegawaiJajahan() && $user->jajahan) {
            $query->where(function ($q) use ($user) {
                $q->where('jajahan', $user->jajahan)
                  ->orWhereNull('jajahan');
            });
        }

        $unreadCount = (clone $query)->where('is_read', false)->count();
        $latest = $query->limit(5)->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'message' => $item->message,
                'type' => $item->type,
                'badge_color' => $item->badge_color,
                'icon' => $item->icon,
                'is_read' => $item->is_read,
                'action_url' => $item->action_url ?: route('admin.notifications.show', $item->id),
                'time_ago' => $item->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $latest,
        ]);
    }
}
