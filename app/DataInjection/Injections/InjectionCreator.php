<?php

namespace App\DataInjection\Injections;

use Illuminate\Database\Migrations\MigrationCreator;

class InjectionCreator extends MigrationCreator
{
    public function stubPath(): string
    {
        return __DIR__ . '/stubs';
    }
}
