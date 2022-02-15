<?php

namespace Infinity\Models;

use Illuminate\Database\Eloquent\Model as IlluminateModel;
use Infinity\Traits\CanDisplay;
use Infinity\Traits\TestAttributes;

class Model extends IlluminateModel
{
    use CanDisplay, TestAttributes;
}
