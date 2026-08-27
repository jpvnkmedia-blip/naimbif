<?php

namespace App\Models;

use App\Mail\NaimbifNotificationMail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SystemNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_id',
        'no_rujukan',
        'jajahan',
        'type',
        'title',
        'message',
        'icon',
        'badge_color',
        'action_url',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper untuk mencipta notifikasi sistem dan menghantar notifikasi emel secara automatik.
     */
    public static function logAndNotify(
        string $type,
        string $title,
        string $message,
        ?Application $application = null,
        ?string $actionUrl = null,
        string $badgeColor = 'emerald',
        string $icon = 'fas fa-bell',
        ?int $userId = null
    ): self {
        $notification = self::create([
            'user_id' => $userId,
            'application_id' => $application?->id,
            'no_rujukan' => $application?->no_rujukan,
            'jajahan' => $application?->jajahan,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'badge_color' => $badgeColor,
            'action_url' => $actionUrl,
            'is_read' => false,
        ]);

        // Hantar notifikasi emel di latar belakang
        try {
            self::sendEmailNotification($type, $title, $message, $application, $actionUrl);
        } catch (\Throwable $e) {
            Log::warning('Gagal menghantar notifikasi emel NAIMbif: ' . $e->getMessage());
        }

        return $notification;
    }

    /**
     * Hantar notifikasi emel kepada pegawai berkaitan
     */
    protected static function sendEmailNotification(
        string $type,
        string $title,
        string $message,
        ?Application $application,
        ?string $actionUrl
    ): void {
        // Senarai penerima emel pegawai
        $recipients = ['admin@jpvnk.gov.my', 'negeri@jpvnk.gov.my'];

        if ($application && $application->jajahan) {
            // Tambah emel pegawai jajahan jika ada rekod
            $jajahanOfficer = User::where('jajahan', 'LIKE', '%' . $application->jajahan . '%')
                ->where('role', 'pegawai_jajahan')
                ->first();
            if ($jajahanOfficer && !in_array($jajahanOfficer->email, $recipients)) {
                $recipients[] = $jajahanOfficer->email;
            }
        }

        $mailable = new NaimbifNotificationMail(
            type: $type,
            title: $title,
            activityMessage: $message,
            application: $application,
            actionUrl: $actionUrl
        );

        foreach ($recipients as $recipient) {
            Mail::to($recipient)->queue($mailable);
        }
    }
}
