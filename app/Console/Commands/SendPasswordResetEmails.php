<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Password;

class SendPasswordResetEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:send-reset-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send password reset emails to all eligible users';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::where('status', 1)->where('email','werose4266@gronasu.com')->get();

        foreach ($users as $user) {
            $token = Password::createToken($user);
            $user->sendPasswordResetNotification($token);
        }

        $this->info('Password reset emails sent to all eligible users.');
    }
}
