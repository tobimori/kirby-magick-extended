<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit34a1af71f0fe59e8923fade9d82a650e
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'tobimori\\' => 9,
        ),
        'K' => 
        array (
            'Kirby\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'tobimori\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
        'Kirby\\' => 
        array (
            0 => __DIR__ . '/..' . '/getkirby/composer-installer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Kirby\\ComposerInstaller\\CmsInstaller' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/CmsInstaller.php',
        'Kirby\\ComposerInstaller\\Installer' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/Installer.php',
        'Kirby\\ComposerInstaller\\Plugin' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/Plugin.php',
        'Kirby\\ComposerInstaller\\PluginInstaller' => __DIR__ . '/..' . '/getkirby/composer-installer/src/ComposerInstaller/PluginInstaller.php',
        'tobimori\\ImageMagickExtended' => __DIR__ . '/../..' . '/classes/ImageMagickExtended.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit34a1af71f0fe59e8923fade9d82a650e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit34a1af71f0fe59e8923fade9d82a650e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit34a1af71f0fe59e8923fade9d82a650e::$classMap;

        }, null, ClassLoader::class);
    }
}