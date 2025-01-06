<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User; // Assuming you want to send to the user model
use Illuminate\Contracts\Queue\ShouldQueue;

class GoodsReceivedNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $requisition;

    public function __construct(User $user, $requisition)
    {
        $this->user = $user;
        $this->requisition = $requisition;
    }

    public function build()
    {
        return $this->subject('Item Received For Urgent Requisition')
                    ->view('emails.goods_received') // The view to send
                    ->with([
                        'userName' => $this->user->name,
                        'requisitionCode' => $this->requisition->code,
                        'requisitionId' => $this->requisition->id,
                    ]);
    }
}
