<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Activation extends Notification
{
    
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $user;

    public function __construct($user){
        $this->user = $user;
    }


    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable){
        return ['mail'];
    }
    

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable){
        
        $url = url('/auth/'.$this->user->activation_token.'/activate');

        return (new MailMessage())
                    ->from('support@hotboxerp.com','HotBox Admin')
                    ->subject('Hi '.$this->user->name.' We have your activation link for '.config('app.name'))
                    ->greeting('Hello!')
                    ->line('Thank you for registering an account with us.')
                    ->line('Click on the below link to verify your email!')
                    ->action('Verify now!', $url)
                    ->line('Thank you for using our application!');
    }


    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable){
        
        return [
            //
        ];
    }
    
}
