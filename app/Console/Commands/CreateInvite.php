<?php

namespace Knot\Console\Commands;

use Doorman;
use Illuminate\Console\Command;

class CreateInvite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'doorman:invite';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates an invite code for the specified email.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = $this->ask('What email address would you like to associate with this invite?');
        Doorman::generate()->for($email)->make();
        $this->info("Invite successfully generated for {$email}!");
    }
}
