<?php

namespace App\DataInjection\Console;

use Illuminate\Database\Console\Migrations\RollbackCommand as MigrationRollbackCommand;

/**
 * Class RollbackCommand
 * @package App\DataInjection\Console
 */
class RollbackCommand extends MigrationRollbackCommand
{
    use InjectorPathTrait;

    protected $name = 'data-inject:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the injected database';
}
