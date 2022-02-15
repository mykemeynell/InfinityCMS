<?php

namespace Infinity\Database\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UsernameObserver
{
    /**
     * Set the model username if one has not been passed.
     *
     * @param Model $model
     *
     * @throws \Exception
     */
    public function creating(Model $model): void
    {
        if(empty($model->getAttribute('username'))) {
            $generatedUsername = sprintf("%s_%s",
                Str::lower(Str::snake($model->getAttribute('name'))),
                Str::lower(Str::random(4))
            );
            $model->setAttribute('username', $generatedUsername);
        }
    }
}
