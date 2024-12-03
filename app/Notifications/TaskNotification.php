<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class TaskNotification extends Notification
{
    use Queueable;

    private $details;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'broadcast', WebPushChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    // public function toMail($notifiable)
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {

        $body = 'You have a new assigned task for '.$this->details->location->name.'!';

        if($this->details->status === 'completed')
        {
            $body = 'A task has been completed for '.$this->details->location->name.'!';
        }

        return [
            'title' => 'Hello from '.env('APP_NAME').'!',
            'body' => $body,
            'action_url' =>  env('APP_URL').'/admin/auth/task/'.$this->details->id.'/edit',
            'created' => Carbon::now()->toIso8601String()
        ];
    }

    /**
     * Get the web push representation of the notification.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @return \Illuminate\Notifications\Messages\DatabaseMessage
     */
    public function toWebPush($notifiable, $notification)
    {
        $body = 'You have a new assigned task for '.$this->details->location->name.'!';

        if($this->details->status === 'completed')
        {
            $body = $body = 'A task has been completed for '.$this->details->location->name.'!';
        }

        return (new WebPushMessage)
            ->title('Hello from '.env('APP_NAME').'!')
            ->icon('/images/logo.png')
            ->body($body)
            ->action('View task', 'admin/auth/task/'.$this->details->id.'/edit')
            ->data(['id' => $notification->id]);
    }
}
