<?php

namespace App\DataInjection;

use Illuminate\Database\Connection;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Query\Builder;

class DatabaseDataInjectionRepository extends DatabaseMigrationRepository
{
    /**
     * Get the completed migrations.
     *
     * @return array
     */
    public function getRan(): array
    {
        return $this->table()
            ->orderBy('batch')
            ->orderBy('injection')
            ->pluck('injection')->all();
    }

    /**
     * Get list of migrations.
     *
     * @param int $steps
     * @return array
     */
    public function getMigrations($steps): array
    {
        $query = $this->table()->where('batch', '>=', '1');

        return $query->orderBy('batch', 'desc')
            ->orderBy('injection', 'desc')
            ->take($steps)->get()->all();
    }

    /**
     * Get the last migration batch.
     *
     * @return array
     */
    public function getLast(): array
    {
        $query = $this->table()->where('batch', $this->getLastBatchNumber());

        return $query->orderBy('injection', 'desc')->get()->all();
    }

    /**
     * Get the completed migrations with their batch numbers.
     *
     * @return array
     */
    public function getMigrationBatches(): array
    {
        return $this->table()
            ->orderBy('batch')
            ->orderBy('injection')
            ->pluck('batch', 'injection')->all();
    }

    /**
     * Log that a migration was run.
     *
     * @param string $file
     * @param int $batch
     * @return void
     */
    public function log($file, $batch): void
    {
        $record = ['injection' => $file, 'batch' => $batch];

        $this->table()->insert($record);
    }

    /**
     * Remove a migration from the log.
     *
     * @param object $migration
     * @return void
     */
    public function delete($migration): void
    {
        $this->table()->where('injection', $migration->injection)->delete();
    }

    /**
     * Get the next migration batch number.
     *
     * @return int
     */
    public function getNextBatchNumber(): int
    {
        return $this->getLastBatchNumber() + 1;
    }

    /**
     * Get the last migration batch number.
     *
     * @return int
     */
    public function getLastBatchNumber(): int
    {
        return $this->table()->max('batch') ?? 0;
    }

    /**
     * Create the migration playbackFileRepository data store.
     *
     * @return void
     */
    public function createRepository(): void
    {
        $schema = $this->getConnection()->getSchemaBuilder();

        $schema->create($this->table, function ($table) {
            // The migrations table is responsible for keeping track of which of the
            // migrations have actually run for the application. We'll create the
            // table to hold the migration file's path as well as the batch ID.
            $table->increments('id');
            $table->string('injection');
            $table->integer('batch');
        });
    }

    /**
     * Determine if the migration playbackFileRepository exists.
     *
     * @return bool
     */
    public function repositoryExists(): bool
    {
        $schema = $this->getConnection()->getSchemaBuilder();

        return $schema->hasTable($this->table);
    }

    /**
     * Get a query builder for the migration table.
     *
     * @return Builder
     */
    protected function table(): Builder
    {
        return $this->getConnection()->table($this->table)->useWritePdo();
    }

    /**
     * Get the connection resolver instance.
     *
     * @return ConnectionResolverInterface
     */
    public function getConnectionResolver(): ConnectionResolverInterface
    {
        return $this->resolver;
    }

    /**
     * Resolve the database connection instance.
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->resolver->connection($this->connection);
    }

    /**
     * Set the information source to gather data.
     *
     * @param string $name
     * @return void
     */
    public function setSource($name): void
    {
        $this->connection = $name;
    }
}
