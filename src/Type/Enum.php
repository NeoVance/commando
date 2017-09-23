<?php
/**
 * Base implementation for different types.
 * 
 * @namespace \Types
 * @package Commando
 * @module Ranger
 * @file src/Types/Type.php
 * @since 3.0
 */
namespace Commando\Type;

/**
 * Interface for basic types
 * 
 * @abstract
 * @class Enum
 */
abstract class Enum {
    
    /**
     * Assign the default value of the type
     * 
     * @const Default
     */
    const Default = null;
    
    /**
     * Instance value
     * 
     * @protected
     * @property value
     */
    protected $value;
    
    /**
     * Instance ReflectionClass
     * 
     * @protected
     * @static
     * @property \ReflectionClass reflection
     */
    protected static $reflection;
    
    /**
     * Create a new type
     * 
     * @constructor
     * @public
     * @function __construct(initial = null, bool strict = false)
     */
    public function __construct($initial = null, bool $strict = false) {
        if (initial === null) {
            $this->value = static::Default;
        }
        
        if (!in_array($initial, static::getConstants())) {
            $error = sprintf("Value not a const in Type %s", static::class);
            throw new \UnexpectedValueException($error);
        }
        
        $this->value = $initial;
    }
    
    /**
     * Get a collection of the constants on this Type
     * @public
     * @function getConstants(bool showDefault = false)
     */
    public static function getConstants(bool $showDefault = false)
    {
        static::prepareReflection();
        
        $constants = static::$reflection->getConstants();
        
        if (!$showDefault) {
            unset($constants["Default"]);
        }
        
        return $constants;
    }
    
    /**
     * Prepare ReflectionClass for use and cache
     * 
     * @protected
     * @static
     * @function prepareReflection
     */
    protected static function prepareReflection()
    {
        if (!isset(static::$reflection)) {
            static::$reflection = new \ReflectionClass(static::class);
        }
    }
    
    /**
     * Convert to string
     */
    public function __toString()
    {
        return (string)$this->value;
    }
    
    /**
     * Re-hydrate from serialization
     */
    public static function __set_state(array $state)
    {
        return new static($state['value']);
    }
    
    /**
     * Serialize
     */
    public function __debugInfo()
    {
        return [ 'value' => $this->value ];
    }
    
    /**
     * Use constant names as factory methods for Type
     */
    public static function __callStatic(string $method, array $args = [])
    {
        $constants = static::getConstants();
        
        if (!in_array($method, $constants)) {
            $error = sprintf(
                "Invalid call to function %s in %s.",
                $method,
                static::class
            );
            
            throw new \Exception($error);
        }
        
        return new static($constants[$method]);
    }
    
}