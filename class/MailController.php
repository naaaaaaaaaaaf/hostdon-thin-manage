<?php


namespace Hostdon;


use Genkgo\Mail\Header\From;
use Genkgo\Mail\Header\Subject;
use Genkgo\Mail\Header\To;
use Genkgo\Mail\MessageBodyCollection;
use Genkgo\Mail\Protocol\Smtp\ClientFactory;
use Genkgo\Mail\Transport\EnvelopeFactory;
use Genkgo\Mail\Transport\SmtpTransport;

class MailController
{
    public function send_mail(string $title, string $data, string $address){

        $message = (new MessageBodyCollection($data))
            ->createMessage()
            ->withHeader(new Subject($title))
            ->withHeader(From::fromEmailAddress('noreply@hostdon.jp'))
            ->withHeader(To::fromSingleRecipient($address));
        $transport = new SmtpTransport(
            ClientFactory::fromString($_ENV['MAIL_PARAM'])->newClient(),
            EnvelopeFactory::useExtractedHeader()
        );
        $transport->send($message);
    }
}