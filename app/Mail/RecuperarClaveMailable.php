<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecuperarClaveMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contact_data)
    {
        //
        $this->contact = json_decode($contact_data);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->contact;
        // dd($data->codigo);exit;
        return $this->subject('Ok Computer')->from('programador01@okcomputer.com.pe', 'Recuperar clave')->view('mail.recuperar_clave', compact('data'));
    }
}
