<?php

if (!function_exists('get_reflection_property')) {
    /**
     * Get a reflected property.
     *
     * @param $object
     * @param $property
     *
     * @return \ReflectionProperty
     * @throws \ReflectionException
     */
    function get_reflection_property($object, $property): ReflectionProperty
    {
        $reflectionProperty = new \ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty;
    }
}

if (!function_exists('get_protected_property')) {
    /**
     * Get a protected property from within a passed object.
     *
     * @param $object
     * @param $property
     *
     * @return mixed
     * @throws \ReflectionException
     */
    function get_protected_property($object, $property): mixed
    {
        return get_reflection_property($object, $property)->getValue($object);
    }
}

if (!function_exists('class_uses_trait')) {
    /**
     * Test if class uses trait.
     *
     * @param $object
     * @param $traits
     *
     * @return bool
     * @throws \ReflectionException
     */
    function class_uses_trait($object, $trait): bool
    {
        $assignedTraits = get_reflection_class_traits(
            new ReflectionClass($object)
        );

        return array_key_exists($trait, (array)$assignedTraits);
    }
}

if (!function_exists('get_reflection_class_traits')) {
    /**
     * Get a classes traits using reflection.
     *
     * @param \ReflectionClass $reflectionClass
     * @param array            $traits
     *
     * @return array|bool
     */
    function get_reflection_class_traits(
        ReflectionClass $reflectionClass,
        array $traits = []
    ): bool|array {
        if ($reflectionClass->getParentClass()) {
            $traits = get_reflection_class_traits($reflectionClass->getParentClass(),
                $traits);
        }

        if (!empty($reflectionClass->getTraits())) {
            foreach ($reflectionClass->getTraits() as $trait_key => $trait) {
                $traits[$trait_key] = $trait;
                $traits = get_reflection_class_traits($trait, $traits);
            }
        }

        return $traits;
    }
}

if (!function_exists('get_file_namespace')) {
    /**
     * Get a classes' namespace from its path.
     *
     * @param string $file
     *
     * @return string|null
     */
    function get_file_namespace(string $file): ?string
    {
        $ns = null;
        $handle = fopen($file, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (str_starts_with($line, 'namespace')) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            fclose($handle);
        }
        return $ns;
    }
}
