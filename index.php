<?php

@include_once __DIR__ . '/vendor/autoload.php';

use Kirby\Cms\App;
use tobimori\ImageMagickExtended;

App::plugin('tobimori/magick-extended', []);

Kirby\Image\Darkroom::$types['im-extended'] = ImageMagickExtended::class;
