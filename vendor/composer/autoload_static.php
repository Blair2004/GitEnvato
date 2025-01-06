<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitf50fdcfe433d1c28812771e6095b2527
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'League\\MimeTypeDetection\\' => 25,
            'League\\Flysystem\\Local\\' => 23,
            'League\\Flysystem\\Ftp\\' => 21,
            'League\\Flysystem\\' => 17,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'League\\MimeTypeDetection\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/mime-type-detection/src',
        ),
        'League\\Flysystem\\Local\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/flysystem-local',
        ),
        'League\\Flysystem\\Ftp\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/flysystem-ftp',
        ),
        'League\\Flysystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/league/flysystem/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitf50fdcfe433d1c28812771e6095b2527::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitf50fdcfe433d1c28812771e6095b2527::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitf50fdcfe433d1c28812771e6095b2527::$classMap;

        }, null, ClassLoader::class);
    }
}