<?php

namespace App\DataInjection\Console;

use Illuminate\Database\Console\Migrations\InstallCommand as MigrationsInstallCommand;

/**
 * Class InstallCommand
 * @package App\DataInjection\Console
 */
class InstallCommand extends MigrationsInstallCommand
{
    use InjectorPathTrait;

    protected $name = 'data-inject:install';

    protected $description = 'Create the injection playbackFileRepository';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->repository->setSource($this->input->getOption('database'));

        $this->repository->createRepository();

        $this->info('Injection table created successfully.');
    }
}
