<?php

if(!function_exists('fa'))
{
    function fa(string $icon, ?string $pro = null): string
    {
        if(empty($pro)) return $icon;

        return is_fa_pro() ? $pro : $icon;
    }
}

if(!function_exists('is_fa_pro'))
{
    /**
     * Check if FontAwesome is using PRO licence.
     *
     * @return bool
     */
    function is_fa_pro(): bool
    {
        return infinity_config('fontawesome.licence', false);
    }
}
