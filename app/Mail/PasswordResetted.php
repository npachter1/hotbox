<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Auth\User;
use App\Models\AppSchema;


class PasswordResetted extends Mailable
{
    
    use Queueable, SerializesModels;

    protected $user;
    protected $token;
    protected $schema;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user){
        $this->user = $user;
        $this->schema = AppSchema::getSchema('user_schema');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){

        return $this->view('email.touser')
            ->subject('Hi '.$this->user->name.' Your '.config('app.name').' Password has been Reset')
            ->onQueue('email')
            ->with([
                'user'      => $this->user,
                'mes'       => 'Your password for '.config('app.name').' has been reset',
                'cta_title' => '(re)LOGIN',
                'cta_url'   => url('/admin/dashboard')
            ]);
            
    }


    /* the queing tag for */
    public function tags(){
        return ['UserPasswordResetted - '.$this->user->email];
    }
    
}
