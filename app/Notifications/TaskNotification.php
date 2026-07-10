<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $task;
    public $type; // 'assigned' or 'updated'
    public $performer; // User who did the action

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, string $type, ?User $performer = null)
    {
        $this->task = $task;
        $this->type = $type;
        $this->performer = $performer;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $performerName = $this->performer ? $this->performer->name : 'Sistem';
        
        $url = url('/admin/tasks/board'); // Link to task board

        if ($this->type === 'assigned') {
            return (new MailMessage)
                        ->subject('Yeni Görev Atandı: ' . $this->task->title)
                        ->greeting('Merhaba ' . $notifiable->name . ',')
                        ->line($performerName . ' sana yeni bir görev atadı.')
                        ->line('Görev: ' . $this->task->title)
                        ->line('Öncelik: ' . ucfirst($this->task->priority))
                        ->action('Görevi Görüntüle', $url)
                        ->line('İyi çalışmalar!');
        }

        if ($this->type === 'updated') {
            return (new MailMessage)
                        ->subject('Görev Güncellendi: ' . $this->task->title)
                        ->greeting('Merhaba ' . $notifiable->name . ',')
                        ->line($performerName . ', takip ettiğiniz/atandığınız bir görevi güncelledi.')
                        ->line('Görev: ' . $this->task->title)
                        ->action('Görevi Görüntüle', $url)
                        ->line('İyi çalışmalar!');
        }
        
        // Fallback
        return (new MailMessage)
            ->subject('Görev Bildirimi')
            ->line('Görev: ' . $this->task->title)
            ->action('Panoya Git', $url);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'type' => $this->type,
        ];
    }
}
