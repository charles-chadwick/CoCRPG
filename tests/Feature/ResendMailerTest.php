<?php

use Illuminate\Support\Facades\Mail;
use Resend\Laravel\Transport\ResendTransportFactory;

test('the resend mailer builds a Resend transport from the configured API key', function () {
    config()->set('services.resend.key', 'test_api_key');

    $transport = Mail::mailer('resend')->getSymfonyTransport();

    expect($transport)->toBeInstanceOf(ResendTransportFactory::class);
});

test('the resend mailer is registered in the mail configuration', function () {
    expect(config('mail.mailers.resend.transport'))->toBe('resend');
});

test('mail is not sent through Resend during testing', function () {
    expect(config('mail.default'))->toBe('array');
});
