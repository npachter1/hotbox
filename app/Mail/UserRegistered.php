<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Auth\User;
use App\Models\Auth\Location;

use Carbon\Carbon;


class UserRegistered extends Mailable
{
    
    use Queueable, SerializesModels;

    protected $user;
    protected $location;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, Location $location){
        $this->user = $user;
        $this->location = $location;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){

        $isActive = ($this->user->status=='activated' ? true : false);
        $abbvDate = Carbon::now()->timezone(data_get($this->location,'settings.communication_timezone','UTC'))->format('m/d/y g:ia');

        return $this->view('email.touser')
            ->subject('Hi '.$this->user->name.' Your '.config('app.name').' Location has been Successfully registered!')
            ->onQueue('email')
            ->with([
                'user'      => $this->user,
                'mes'       => 'Your App Location at '.config('app.name').' has been successfully Registered on '.$abbvDate.' -- Please click on the link below to '.($isActive==true ? 'Enter' : 'Activate').' your Account..',
                'cta_title' => ($isActive==true ? 'GO TO DASHBOARD' : 'ACTIVATE YOUR ACCOUNT'),
                'cta_url'   => ($isActive==true ? url('/admin/dashboard') : url('/auth/'.$this->user->activation_token.'/activate'))
            ]);
        
    }


    /* the queing tag for */
    public function tags(){
        return ['UserRegistered - '.$this->user->email];
    }
    
}
