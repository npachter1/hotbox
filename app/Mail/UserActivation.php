<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Auth\User;


class UserActivation extends Mailable
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
            ->subject('Hi '.$this->user->name.' We have your activation link for '.config('app.name'))
            ->onQueue('email')
            ->with([
                'user'      => $this->user,
                'mes'       => 'Your Activation link to access '.config('app.name').' is below. -- Please click on the link in order to activate your account with us!',
                'cta_title' => 'ACTIVATE ACCOUNT',
                'cta_url'   => url('/auth/'.$this->user->activation_token.'/activate')
            ]);
        
    }


    /* the queing tag for */
    public function tags(){
        return ['UserActivationEmail - '.$this->user->email];
    }
    
}
