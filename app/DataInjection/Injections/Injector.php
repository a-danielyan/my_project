<?php

namespace App\DataInjection\Injections;

use Illuminate\Database\Events\MigrationsEnded;
use Illuminate\Database\Events\MigrationsStarted;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Arr;

class Injector extends Migrator
{
    /**
     * Rollback the given migrations.
     *
     * @param array $migrations
     * @param array|string $paths
     * @param array $options
     * @return array
     */
    protected function rollbackMigrations(array $migrations, $paths, array $options): array
    {
        $rolledBack = [];

        $this->requireFiles($files = $this->getMigrationFiles($paths));

        $this->fireMigrationEvent(new MigrationsStarted('Down'));

        // Next we will run through all the migrations and call the "down" method
        // which will reverse each migration in order. This getLast method on the
        // playbackFileRepository already returns these migration's names in reverse order.
        foreach ($migrations as $migration) {
            $migration = (object)$migration;

            if (!$file = Arr::get($files, $migration->injection)) {
                $this->note("<fg=red>Migration not found:</> " . $migration->injector);

                continue;
            }

            $rolledBack[] = $file;

            $this->runDown(
                $file,
                $migration,
                $options['pretend'] ?? false,
            );
        }

        $this->fireMigrationEvent(new MigrationsEnded('Down'));

        return $rolledBack;
    }
}
