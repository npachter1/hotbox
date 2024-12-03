<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Auth\User;
use App\Models\Auth\Location;


class UserMigrated extends Mailable
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

        $loc = Location::find($this->user->location_id);

        return $this->view('email.touser')
            ->subject('Hi '.$this->user->name.' Your '.config('app.name').' Location '.data_get($loc,'name','Misc').' Has been Migrated!')
            ->onQueue('email')
            ->with([
                'user'      => $this->user,
                'mes'       => 'Your '.data_get($loc,'name','Misc').' Location has just finished Migration! -- Please click on the link below to enter..',
                'cta_title' => 'LOGIN TO ACCOUNT',
                'cta_url'   => url('/admin/dashboard')
            ]);
        
    }


    /* the queing tag for */
    public function tags(){
        return ['UserMigratedEmail - '.$this->user->email];
    }
    
}
