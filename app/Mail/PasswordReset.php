<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Auth\User;
use App\Models\AppSchema;


class PasswordReset extends Mailable
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
    public function __construct(User $user, $token){
        $this->user = $user;
        $this->token = $token;
        $this->schema = AppSchema::getSchema('user_schema');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(){

        return $this->view('email.touser')
            ->subject('Hi '.$this->user->name.' Your '.config('app.name').' Password Reset Request..')
            ->onQueue('email')
            ->with([
                'user'      => $this->user,
                'mes'       => data_get($this->schema,'lang.passwordResetPrompt','password reset request..'),
                'cta_title' => 'PROCEED TO RESET PASSWORD',
                'cta_url'   => url('/password/reset/'.$this->token)
            ]);
            
    }


    /* the queing tag for */
    public function tags(){
        return ['UserPasswordResetRequest - '.$this->user->email];
    }
    
}
