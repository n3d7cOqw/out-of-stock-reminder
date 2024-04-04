<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc941c7eba97677dd6db488087c023051
{
    public static $prefixLengthsPsr4 = array (
        'O' => 
        array (
            'OutOfStockReminder\\' => 19,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'OutOfStockReminder\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc941c7eba97677dd6db488087c023051::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc941c7eba97677dd6db488087c023051::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc941c7eba97677dd6db488087c023051::$classMap;

        }, null, ClassLoader::class);
    }
}
