<?php

namespace Infinity\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\Pure;

/**
 * @property string $key
 * @property string $name
 * @property string|null $value
 * @property string $type
 * @property string $default
 */
class Setting extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'key', 'name', 'value', 'type', 'default'
    ];

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the default value attribute.
     *
     * @return string|null
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * Get the setting type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Test if the setting type is JSON.
     *
     * @return bool
     */
    #[Pure] public function isJson(): bool
    {
        return $this->getType() === 'json';
    }

    /**
     * Get the setting value.
     *
     * @return bool|string|object|null
     */
    public function getValue(): bool|string|object|null
    {
        $value = !empty($this->value)
            ? $this->value
            : $this->getDefault();

        if(Str::startsWith($value, 'f::')) {
            $re = '/f\:\:([\w\_\d]+)\(([\w\W]*)\)/mi';
            preg_match_all($re, $value, $matches, PREG_SET_ORDER, 0);

            if(!empty($matches) && count($matches[0]) >= 3) {
                $method = $matches[0][1];
                $arguments = explode(',', $matches[0][2]);
                foreach ($arguments as $key => $argument) {
                    $arguments[$key] = trim($argument, ' \t\n\r\0\x0B\'');
                }

                $value = call_user_func_array($method, $arguments);
            }
        }

        $value = val($value);

        if($this->isJson()) {
            return json_decode($value);
        }

        return $value;
    }

    /**
     * Get the setting key.
     *
     * @return string
     */
    public function getSettingKey(): string
    {
        return $this->key;
    }
}
