<?php

namespace App\Livewire\Admin;

use App\Models\AppNotification;
use Livewire\Component;
use Livewire\WithPagination;

class NotificationCenter extends Component
{
    use WithPagination;

    public string $filter = 'all'; // all, unread, task, approval, system
    
    protected $paginationTheme = 'bootstrap'; // Livewire pagination setup

    protected $listeners = ['refreshNotifications' => '$refresh'];

    public function markRead(int $id)
    {
        $notif = AppNotification::find($id);
        if ($notif && $notif->user_id === auth()->id()) {
            $notif->update(['read_at' => now()]);
            $this->dispatch('refreshNotifications');
        }
    }

    public function deleteNotification(int $id)
    {
        $notif = AppNotification::find($id);
        if ($notif && $notif->user_id === auth()->id()) {
            $notif->delete();
            $this->dispatch('refreshNotifications');
            $this->dispatch('notify', type: 'success', message: 'Bildirim silindi.');
        }
    }

    public function markAllRead()
    {
        AppNotification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        $this->dispatch('refreshNotifications');
        $this->dispatch('notify', type: 'success', message: 'Tüm bildirimler okundu olarak işaretlendi.');
    }

    public function clearAll()
    {
        AppNotification::where('user_id', auth()->id())->delete();
        $this->dispatch('refreshNotifications');
        $this->dispatch('notify', type: 'success', message: 'Tüm bildirimler silindi.');
    }

    public function approveItem(int $notificationId, string $modelType, int $modelId)
    {
        if (class_exists($modelType)) {
            $model = $modelType::find($modelId);
            if ($model) {
                $model->approve(auth()->user(), __('Bildirim merkezinden onaylandı.'));
                
                // Mark this notification as read
                $this->markRead($notificationId);
                
                $this->dispatch('notify', type: 'success', message: 'Kayıt başarıyla onaylandı.');
            }
        }
    }

    public function rejectItem(int $notificationId, string $modelType, int $modelId)
    {
        if (class_exists($modelType)) {
            $model = $modelType::find($modelId);
            if ($model) {
                $model->reject(auth()->user(), __('Bildirim merkezinden reddedildi.'));
                
                // Mark this notification as read
                $this->markRead($notificationId);
                
                $this->dispatch('notify', type: 'success', message: 'Kayıt reddedildi.');
            }
        }
    }

    public function render()
    {
        $query = AppNotification::where('user_id', auth()->id());

        if ($this->filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->filter === 'task') {
            $query->whereIn('type', ['task_assigned', 'task_completed']);
        } elseif ($this->filter === 'approval') {
            $query->where('type', 'approval_required');
        } elseif ($this->filter === 'system') {
            $query->whereIn('type', ['system', 'contract_expiring', 'document_uploaded']);
        }

        $notifications = $query->latest()->paginate(15);

        return view('livewire.admin.notification-center', [
            'notifications' => $notifications,
            'unreadCount' => AppNotification::where('user_id', auth()->id())->unread()->count(),
        ])->layout('layouts.admin', [
            'title' => 'Bildirim Merkezi',
            'breadcrumb' => 'Bildirimler',
        ]);
    }
}
