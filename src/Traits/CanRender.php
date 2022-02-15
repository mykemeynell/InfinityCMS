<?php

namespace Infinity\Traits;

use Illuminate\View\View;

trait CanRender
{
    /**
     * @throws \Throwable
     */
    public function render($content)
    {
        if ($content instanceof View) {
            return $content->render();
        }

        return $content;
    }
}
