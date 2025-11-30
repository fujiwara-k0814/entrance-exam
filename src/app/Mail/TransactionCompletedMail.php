<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $itemName;
    protected string $sellerEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $itemName, string $sellerEmail)
    {
        $this->itemName = $itemName;
        $this->sellerEmail = $sellerEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('取引完了のお知らせ')
            ->html("出品商品「{$this->itemName}」の取引が完了しました。");
    }
}
