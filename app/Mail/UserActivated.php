<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Auth\User;


class UserActivated extends Mailable
{
    
    use Queueable, SerializesModels;

    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user){
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){

        return $this->view('email.touser')
            ->subject('Hi '.$this->user->name.' Your '.config('app.name').' Account has been activated!')
            ->onQueue('email')
            ->with([
                'user'      => $this->user,
                'mes'       => 'Your User account at '.config('app.name').' has been activated! -- Please click on the link below to enter..',
                'cta_title' => 'LOGIN TO ACCOUNT',
                'cta_url'   => url('/admin/dashboard')
            ]);
        
    }


    /* the queing tag for */
    public function tags(){
        return ['UserActivatedEmail - '.$this->user->email];
    }
    
}
