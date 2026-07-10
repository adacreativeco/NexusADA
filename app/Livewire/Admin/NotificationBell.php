<?php

namespace App\Livewire\Admin;

use App\Models\AppNotification;
use Livewire\Component;

class NotificationBell extends Component
{
    public bool $dropdownOpen = false;

    public function toggleDropdown()
    {
        $this->dropdownOpen = !$this->dropdownOpen;
    }

    public function markAllRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        $this->dropdownOpen = false;
    }

    public function markRead(int $id)
    {
        $notif = AppNotification::find($id);
        if ($notif && $notif->user_id === auth()->id()) {
            $notif->update(['read_at' => now()]);
        }
    }

    public function render()
    {
        $unreadCount = AppNotification::where('user_id', auth()->id())->unread()->count();
        $notifications = AppNotification::where('user_id', auth()->id())
            ->latest()
            ->limit(10)
            ->get();

        return view('livewire.admin.notification-bell', [
            'unreadCount' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }
}
