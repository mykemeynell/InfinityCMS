<?php

namespace Infinity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;
use Infinity\Facades\Infinity;
use Infinity\Models\Users\User;

class BasePolicy
{
    use HandlesAuthorization;

    /**
     * Data types.
     *
     * @var array
     */
    protected static array $dataTypes = [];

    /**
     * Handle permission checks that aren't explicitly specified.
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return bool
     */
    public function __call(string $name, array $arguments)
    {
        if (count($arguments) < 2) {
            throw new \InvalidArgumentException('Too few arguments');
        }

        /** @var \Infinity\Models\Users\User $user */
        $user = $arguments[0];

        /** @var $model */
        $model = $arguments[1];

        return $this->checkPermission($user, $model, $name);
    }

    /**
     * Check the permissions of a given user and model.
     *
     * @param \Infinity\Models\Users\User $user
     * @param                             $model
     * @param                             $action
     *
     * @return bool
     */
    protected function checkPermission(User $user, $model, $action): bool
    {
        if (!isset(self::$dataTypes[get_class($model)])) {
            $dataType = Infinity::model('DataType');
            self::$dataTypes[get_class($model)] = $dataType->where('model_name', get_class($model))->first();
        }

        $dataType = self::$dataTypes[get_class($model)];
        $actionName = sprintf("%s.%s", $dataType->name, $action);

        if(env('INFINITY_DEV', false)) {
            Log::info(sprintf("Checking that user ID [%s] can perform action [%s]", $user->getKey(), $actionName));
        }

        return $user->hasPermission($actionName);
    }
}
