<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class PriceIncreasedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $data;
    public $user;

    public function __construct($data) {
        $this->data = $data;
        $this->user = 'Test user';
    }

    public function build()
    {
        return $this->subject('Item Price Increase')
                    ->view('emails.price_increased')
                    ->with([
                        'user' => $this->user,
                        'data' => $this->data,
                    ]);
    }
}
