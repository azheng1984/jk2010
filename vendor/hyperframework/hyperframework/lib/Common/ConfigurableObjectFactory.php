<?php

class ConfigurableObjectFactory {
    public function create($configName, $defaultClass, array $params = null) {
        $class = new ReflectionClass('ReflectionFunction');
        return $class->newInstanceArgs(array('substr'));
    }
}
