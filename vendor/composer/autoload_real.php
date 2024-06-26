<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInita9bb43d36dfef18d052af8c982e13165
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInita9bb43d36dfef18d052af8c982e13165', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInita9bb43d36dfef18d052af8c982e13165', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInita9bb43d36dfef18d052af8c982e13165::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
