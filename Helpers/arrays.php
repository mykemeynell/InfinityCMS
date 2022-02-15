<?php

if(!function_exists('array_merge_unique'))
{
    /**
     * Uniquely merge two or more arrays.
     *
     * @param array $array
     * @param array ...$arrays
     *
     * @return array
     */
    function array_merge_unique(array $array, array ...$arrays)
    {
        return array_unique(
            array_merge($array, ...$arrays)
        );
    }
}
