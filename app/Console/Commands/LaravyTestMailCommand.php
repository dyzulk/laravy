<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('laravy:test-mail {email}')]
#[Description('Sends a test email to the specified address')]
class LaravyTestMailCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        $this->info("Sending test email to: {$email}...");

        try {
            Mail::raw('Halo! Ini adalah tes pengiriman email menggunakan custom artisan command laravy:test-mail.', function ($message) use ($email) {
                $message->to($email)->subject('Tes Cloudflare Mail (Artisan Command)');
            });

            $this->info('Email berhasil dikirim!');
        } catch (\Exception $e) {
            $this->error('Gagal mengirim email: ' . $e->getMessage());
        }
    }
}
