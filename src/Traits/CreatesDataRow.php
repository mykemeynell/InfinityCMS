<?php

namespace Infinity\Traits;

use Infinity\Models\DataRow;
use Infinity\Models\DataType;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

trait CreatesDataRow
{
    /**
     * Create a data row column definition.
     *
     * @param string $type
     * @param string $displayName
     * @param bool   $required
     * @param int    $order
     * @param bool   $browse
     * @param bool   $read
     * @param bool   $edit
     * @param bool   $add
     * @param bool   $delete
     * @param array  $details
     *
     * @return array
     */
    #[ArrayShape([
        'type' => "string",
        'display_name' => "string",
        'required' => "int",
        'order' => "int",
        'browse' => "int",
        'read' => "int",
        'edit' => "int",
        'add' => "int",
        'delete' => "int",
        'details' => "array"
    ])] private function define(string $type, string $displayName, bool $required, int $order, bool $browse, bool $read, bool $edit, bool $add, bool $delete, array $details = []): array
    {
        return [
            'type' => $type,
            'display_name' => $displayName,
            'required' => (int)$required,
            'order' => $order,
            'browse' => (int)$browse,
            'read' => (int)$read,
            'edit' => (int)$edit,
            'add' => (int)$add,
            'delete' => (int)$delete,
            'details' => $details
        ];
    }

    /**
     * Loop through column definitions for a given data type.
     *
     * @param \Infinity\Models\DataType $dataType
     * @param array                     $columns
     *
     * @return void
     */
    private function loop(\Infinity\Models\DataType $dataType, array $columns): void
    {
        foreach($columns as $column => $def) {
            $dataRow = $this->dataRow($dataType, $column);
            if(!$dataRow->exists) {
                $dataRow->fill($def)->save();
            }
        }
    }

    /**
     * @param \Infinity\Models\DataType $type
     * @param string                    $field
     *
     * @return mixed
     */
    protected function dataRow(DataType $type, string $field): mixed
    {
        return DataRow::firstOrNew([
            'data_type_id' => $type->getKey(),
            'field'        => $field,
        ]);
    }
}
