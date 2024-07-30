<?php

// app/Mail/LoginNotificationMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoginNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $ipAddress;
    public $loginTime;
    public $secureAccountUrl;

    public function __construct($user, $ipAddress, $loginTime, $secureAccountUrl)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
        $this->loginTime = $loginTime;
        $this->secureAccountUrl = $secureAccountUrl;
    }

    public function build()
    {
        return $this->view('emails.login-notification')
                    ->subject('New Login Notification')
                    ->with([
                        'user' => $this->user,
                        'ipAddress' => $this->ipAddress,
                        'loginTime' => $this->loginTime,
                        'secureAccountUrl' => $this->secureAccountUrl,
                    ]);
    }
}

