<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitce587b36b276b0c1b1ef96e409a1531c
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

        spl_autoload_register(array('ComposerAutoloaderInitce587b36b276b0c1b1ef96e409a1531c', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitce587b36b276b0c1b1ef96e409a1531c', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitce587b36b276b0c1b1ef96e409a1531c::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
