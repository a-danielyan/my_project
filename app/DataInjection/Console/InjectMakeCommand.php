<?php

namespace App\DataInjection\Console;

use App\DataInjection\Injections\InjectionCreator;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Support\Composer;
use Illuminate\Support\Str;

/**
 * Class InjectMakeCommand
 * @package App\DataInjection\Console
 */
class InjectMakeCommand extends MigrateMakeCommand
{
    use InjectorPathTrait;

    protected $signature = 'make:data-injector {name : The name of the injector}
        {--path= : The location where the migration file should be created}
        {--realpath : Indicate any provided migration file paths are pre-resolved absolute paths}
        {--fullpath : Output the full path of the migration}';

    protected $description = 'Create a new injection file';

    public function __construct(InjectionCreator $creator, Composer $composer)
    {
        parent::__construct($creator, $composer);
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created, so we can create the appropriate migrations.
        $name = Str::snake(trim($this->input->getArgument('name')));

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($name, null, false);

        $this->composer->dumpAutoloads();
    }
}
