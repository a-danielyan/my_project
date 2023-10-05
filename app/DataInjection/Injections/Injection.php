<?php

namespace App\DataInjection\Injections;

use Illuminate\Database\Migrations\Migration;

abstract class Injection extends Migration
{
    public const ACTION_UPDATE = 'update';
    public const ACTION_ROLLBACK = 'rollback';
}
