<?php

namespace App\DataInjection\Console;

use Illuminate\Database\Console\Migrations\MigrateCommand;

/**
 * Class DataInjectCommand
 * @package App\DataInjection\Console
 */
class DataInjectCommand extends MigrateCommand
{
    use InjectorPathTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-inject {--database= : The database connection to use}
                {--force : Force the operation to run when in production}
                {--path=* : The path(s) to the migrations files to be executed}
                {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
                {--pretend : Dump the SQL queries that would be run}
                {--step : Force the migrations to be run so they can be rolled back individually}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the injections to database';

    /**
     * Prepare the migration database for running.
     *
     * @return void
     */
    protected function prepareDatabase(): void
    {
        $this->migrator->setConnection($this->option('database'));

        if (!$this->migrator->repositoryExists()) {
            $this->call(
                'data-inject:install',
                array_filter([
                    '--database' => $this->option('database'),
                ]),
            );
        }
    }

    public function handle(): void
    {
        if (!$this->confirmToProceed()) {
            return;
        }

        $this->prepareDatabase();

        // Next, we will check to see if a path option has been defined. If it has
        // we will use the path relative to the root of this installation folder
        // so that migrations may be run for any path within the applications.
        $this->migrator->setOutput($this->output)
            ->run($this->getMigrationPaths(), [
                'pretend' => $this->option('pretend'),
                'step' => $this->option('step'),
            ]);
    }
}
