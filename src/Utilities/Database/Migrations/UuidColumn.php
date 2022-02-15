<?php

namespace Infinity\Utilities\Database\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;

trait UuidColumn
{
    /**
     * Get a configured column object for blueprint.
     *
     * @param Blueprint $blueprint
     * @param string    $name
     * @param bool      $primary
     *
     * @return \Illuminate\Database\Schema\ColumnDefinition
     */
    public function createUuidColumn(Blueprint $blueprint, string $name = 'id', bool $primary = true): ColumnDefinition
    {
        $uuid = $blueprint->uuid($name);

        if($primary) $uuid->primary();

        return $uuid;
    }
}
