<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Passwordcreation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
     public function __construct($data)
     {
         $this->data = $data;
     }


    /**
     * Build the message.
     *
     * @return $this
     */
     public function build()
     {
        return $this->from('trendysetup@gmail.com',  'Bartum Energy')->subject("Password Creation Link")->view('emails.passwordcreation')->with('data', $this->data);
     }
}
