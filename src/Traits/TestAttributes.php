<?php

namespace Infinity\Traits;

trait TestAttributes
{
    /**
     * Test if an attribute exists on model.
     *
     * @param string $attr
     *
     * @return bool
     */
    public function hasAttribute(string $attr)
    {
        return array_key_exists($attr, $this->attributes);
    }
}
