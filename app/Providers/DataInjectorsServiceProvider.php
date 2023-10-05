<?php

namespace App\Providers;

use App\DataInjection\Console\DataInjectCommand;
use App\DataInjection\Console\InjectMakeCommand;
use App\DataInjection\Console\InstallCommand;
use App\DataInjection\Console\RollbackCommand;
use App\DataInjection\DatabaseDataInjectionRepository;
use App\DataInjection\Injections\InjectionCreator;
use App\DataInjection\Injections\Injector;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;

class DataInjectorsServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected array $commands = [
        'DataInject' => 'command.data-inject',
        'DataInjectRollback' => 'command.data-inject.rollback',
        'DataInjectInstall' => 'command.data-inject.install',
        'DataInjectMake' => 'command.data-inject.make',
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerRepository();

        $this->registerInjector();

        $this->registerCreator();

        $this->registerCommands($this->commands);
    }

    /**
     * Register the migration playbackFileRepository service.
     *
     * @return void
     */
    protected function registerRepository(): void
    {
        $this->app->singleton('data_injector.playbackFileRepository', function ($app) {
            $table = $app['config']['database.data_injections'];

            return new DatabaseDataInjectionRepository($app['db'], $table);
        });
    }

    /**
     * Register the migrator service.
     *
     * @return void
     */
    protected function registerInjector(): void
    {
        // The migrator is responsible for actually running and rollback the migration
        // files in the application. We'll pass in our database connection resolver
        // so the migrator can resolve any of these connections when it needs to.
        $this->app->singleton('data_injector', function ($app) {
            $repository = $app['data_injector.playbackFileRepository'];

            return new Injector($repository, $app['db'], $app['files'], $app['events']);
        });
    }

    /**
     * Register the migration creator.
     *
     * @return void
     */
    protected function registerCreator(): void
    {
        $this->app->singleton('data_injector.creator', function ($app) {
            return new InjectionCreator(
                $app['files'],
                $app->basePath('stubs')
            );
        });
    }

    /**
     * Register the given commands.
     *
     * @param array $commands
     * @return void
     */
    protected function registerCommands(array $commands): void
    {
        foreach (array_keys($commands) as $command) {
            call_user_func_array([$this, "register{$command}Command"], []);
        }

        $this->commands(array_values($commands));
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerDataInjectCommand(): void
    {
        $this->app->singleton('command.data-inject', function ($app) {
            return new DataInjectCommand($app['data_injector'], $app[Dispatcher::class]);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerDataInjectInstallCommand(): void
    {
        $this->app->singleton('command.data-inject.install', function ($app) {
            return new InstallCommand($app['data_injector.playbackFileRepository']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerDataInjectMakeCommand(): void
    {
        $this->app->singleton('command.data-inject.make', function ($app) {
            // Once we have the migration creator registered, we will create the command
            // and inject the creator. The creator is responsible for the actual file
            // creation of the migrations, and may be extended by these developers.
            $creator = $app['data_injector.creator'];

            $composer = $app['composer'];

            return new InjectMakeCommand($creator, $composer);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerDataInjectRollbackCommand(): void
    {
        $this->app->singleton('command.data-inject.rollback', function ($app) {
            return new RollbackCommand($app['data_injector']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return array_merge([
            'data_injector',
            'data_injector.playbackFileRepository',
            'data_injector.creator',
        ], array_values($this->commands));
    }
}
