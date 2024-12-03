<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Auth\User;
use App\Models\Auth\Location;

use Carbon\Carbon;


class UserReactivation extends Mailable
{

    use Queueable, SerializesModels;

    protected $user;
    protected $location;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user){
        $this->user = $user;
        //$this->location = $location;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){
        return $this->view('email.toadmin')
            ->subject(config('app.name').' User Activation Request')
            ->onQueue('email')
            ->with([
                'suite'      => $this->user,
                'mes'       => 'User '.$this->user->email.' is requesting manual account activation -- Please click on the link below to view and activate their account.',
                'cta_title' => 'GO TO DASHBOARD',
                'cta_url'   => url('/admin/auth/users')
            ]);
    }


    /* the queing tag for */
    public function tags(){
        return ['UserRegistered - '.$this->user->email];
    }

}
