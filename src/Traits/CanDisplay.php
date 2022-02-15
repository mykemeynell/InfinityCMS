<?php

namespace Infinity\Traits;

trait CanDisplay
{
    /**
     * @throws \Exception
     */
    public function getDisplayName(): string
    {
        throw new \Exception(sprintf("%s method has not been implemented on %s", __METHOD__, self::class));
    }
}
