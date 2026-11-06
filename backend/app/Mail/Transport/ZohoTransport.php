<?php

namespace App\Mail\Transport;

use App\Services\Mail\ZohoMailService;
use RuntimeException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;

class ZohoTransport extends AbstractTransport
{
    public function __construct(
        private readonly ZohoMailService $zohoMail
    )
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (!$email instanceof Email) {
            throw new RuntimeException(
                'Zoho transport podržava samo Symfony Email poruke.'
            );
        }

        $this->zohoMail->send($email);
    }

    public function __toString(): string
    {
        return 'zoho-api';
    }
}
