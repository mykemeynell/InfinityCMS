<?php

namespace Infinity\Resources\Fields\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Infinity\Resources\Handlers\HandlerInterface;

trait UsesConditionalAttributes
{
    private static string $conditionSeparator = ':';

    /**
     * Parse any conditional logic that has been passed and assign to HTML attributes.
     *
     * @param string                              $fieldName
     * @param array                               $conditionals
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array                               $viewData
     * @param null                                $handler
     *
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    protected function parseConditionalAttributes(string $fieldName, array $conditionals, Model $model, array $viewData = [], $handler = null): Collection
    {
        $attributes = array_key_exists('attributes', $viewData)
            ? $viewData['attributes'] : [];

        foreach($conditionals as $htmlAttribute => $conditionalLogic) {
            foreach($conditionalLogic as $condition => $appliedValue) {
                if($this->isConditionMetOnModel($condition, $model)) {
                    $attributes = $this->mergeWithDefinedAttribute($attributes, $htmlAttribute, $appliedValue);
                }

                if($this->isConditionMetViaFieldNameAndHandler($fieldName, $condition, $model, $handler)) {
                    $attributes = $this->mergeWithDefinedAttribute($attributes, $htmlAttribute, $appliedValue);
                }
            }
        }


        return collect($attributes);
    }

    /**
     * Merge with defined attributes.
     *
     * @param array  $mergeInto
     * @param string $attribute
     * @param        $whatToMerge
     *
     * @return array
     */
    private function mergeWithDefinedAttribute(array $mergeInto, string $attribute, $whatToMerge): array
    {
        if(is_array($whatToMerge)) {
            $whatToMerge = implode(" ", $whatToMerge);
        }

        if(!array_key_exists($attribute, $mergeInto)) {
            $mergeInto[$attribute] = $whatToMerge;
            return $mergeInto;
        }

        $mergeInto[$attribute] .= " {$whatToMerge}";
        return  $mergeInto;
    }

    /**
     * Test if a condition is met.
     *
     * @param $condition
     * @param $model
     *
     * @return bool
     * @throws \Exception
     */
    private function isConditionMetOnModel($condition, $model): bool
    {
        $property = Str::beforeLast($condition, self::$conditionSeparator);
        $value =  val(Str::afterLast($condition, self::$conditionSeparator));

        if(!method_exists($model, 'hasAttribute') || !$model->hasAttribute($property)) {
            return false;
        }

        return (Str::contains($condition, ':'))
            ? $model->getAttribute($property) == $value
            : val($condition);
    }

    /**
     * Specifically test to see if the field name that is passed matches the name given in the condition and then compare the condition to the returned value of the handler.
     *
     * @param string                              $fieldName
     * @param string                              $condition
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param                                     $handler
     *
     * @return bool
     * @throws \Exception
     */
    private function isConditionMetViaFieldNameAndHandler(string $fieldName, string $condition, Model $model, $handler): bool
    {
        if(!Str::contains($condition, self::$conditionSeparator)) {
            return $condition;
        }

        if($handler instanceof HandlerInterface) {
            throw new \Exception(sprintf("Handlers of type [%s] cannot be used when evaluating conditional statements against user-defined fields, such as [%s] on [%s].", HandlerInterface::class, $fieldName, self::class));
        }

        if(is_array($handler) && count($handler) == 2) {
            $value = call_user_func_array([$handler[0], $handler[1]], [$model]);
        } elseif(is_callable($handler)) {
            $value = call_user_func_array($handler, [$model]);
        } elseif(!empty($handler)) {
            throw new \Exception(sprintf("The handler of type [%s] passed to test conditional attributes is not supported.", gettype($handler)));
        } else {
            return false;
        }

        $conditionFieldName = Str::beforeLast($condition, self::$conditionSeparator);
        $conditionValue = val(Str::afterLast($condition, self::$conditionSeparator));

        return $conditionFieldName == $fieldName && $conditionValue == $value;
    }
}
