<?php

if(!function_exists('infinity_date_format')) {
    /**
     * Get the date format.
     *
     * @param bool $long
     *
     * @return mixed
     */
    function infinity_date_format(bool $long = false): string
    {
        return settings(sprintf('date.format.%s', $long ? 'long' : 'short'));
    }
}

if(!function_exists('infinity_time_format'))
{
    /**
     * Get the time format.
     *
     * @param bool $long
     *
     * @return string
     */
    function infinity_time_format(bool $long = false): string
    {
        return settings(sprintf('time.format.%s', $long ? 'long' : 'short'));
    }
}

if(!function_exists('infinity_datetime_format'))
{
    /**
     * Get the date time format.
     *
     * @param bool $long
     *
     * @return string
     */
    function infinity_datetime_format(bool $long = false): string
    {
        return sprintf('%s %s',
            infinity_date_format($long),
            infinity_time_format($long)
        );
    }
}
