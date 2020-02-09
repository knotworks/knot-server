<?php

namespace Knot\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Question\Question;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'knot:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bootstraps your Knot installation';

    public function handle()
    {
        $this->welcome();

        $this->createEnvFile();

        if (strlen(config('app.key')) === 0) {
            $this->call('key:generate');

            $this->info('~ App key generated.');
        }

        if (! file_exists('/storage/oauth-public.key')) {
            $this->call('passport:keys');

            $this->info('~ OAuth keys generated.');
        }

        $credentials = $this->requestDatabaseCredentials();
        $this->updateEnvironmentFile($credentials);

        if ($this->confirm('Do you want to migrate the database?', false)) {
            $this->migrateDatabaseWithFreshCredentials($credentials);

            $this->info('~ Database successfully migrated.');
            $this->line('Creating Laravel Passport OAuth client....');
            $this->call('passport:client --password');
            $this->info('~ OAuth client created.');
        } else {
            $this->comment('~ NOTE: You will need to manually run `php artisan migrate && php artisan passport:client --password` in your console.');
        }

        $this->goodbye();
    }

    /**
     * Update the .env file from an array of $key => $value pairs.
     *
     * @param array $updatedValues
     *
     * @return void
     */
    protected function updateEnvironmentFile($updatedValues)
    {
        $envFile = $this->laravel->environmentFilePath();

        foreach ($updatedValues as $key => $value) {
            file_put_contents($envFile, preg_replace(
        "/{$key}=(.*)/",
        "{$key}={$value}",
        file_get_contents($envFile)
      ));
        }
    }

    /**
     * Display the welcome message.
     */
    protected function welcome()
    {
        $this->info('>> Welcome to the Knot installation process! <<');
    }

    /**
     * Display the completion message.
     */
    protected function goodbye()
    {
        $this->info('>> The installation process is complete. Have fun! <<');
    }

    /**
     * Request the local database details from the user.
     *
     * @return array
     */
    protected function requestDatabaseCredentials()
    {
        return [
      'DB_DATABASE' => $this->ask('Database name', 'knot'),
      'DB_PORT' => $this->ask('Database port', 3306),
      'DB_USERNAME' => $this->ask('Database user', 'homestead'),
      'DB_PASSWORD' => $this->askHiddenWithDefault('Database password (leave blank for no password)'),
    ];
    }

    /**
     * Create the initial .env file.
     */
    protected function createEnvFile()
    {
        if (! file_exists('.env')) {
            copy('.env.example', '.env');

            $this->info('.env file successfully created');
        }
    }

    /**
     * Migrate the db with the new credentials.
     *
     * @param array $credentials
     *
     * @return void
     */
    protected function migrateDatabaseWithFreshCredentials($credentials)
    {
        foreach ($credentials as $key => $value) {
            $configKey = strtolower(str_replace('DB_', '', $key));

            if ($configKey === 'password' && $value == 'null') {
                config(["database.connections.mysql.{$configKey}" => '']);

                continue;
            }

            config(["database.connections.mysql.{$configKey}" => $value]);
        }

        $this->call('migrate');
    }

    /**
     * Prompt the user for optional input but hide the answer from the console.
     *
     * @param string $question
     * @param bool   $fallback
     *
     * @return string
     */
    public function askHiddenWithDefault($question, $fallback = true)
    {
        $question = new Question($question, 'NULL');

        $question->setHidden(true)->setHiddenFallback($fallback);

        $password = $this->output->askQuestion($question);
    }
}