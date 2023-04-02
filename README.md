![Kirby ImageMagick Extended Banner](./.github/banner.png)

# Kirby ImageMagick Extended

This plugin extends Kirby's built-in ImageMagick driver with features for working with animated or multi-layer images.

## The issue

When converting an animated image to another format, ImageMagick converts each frame individually and outputs them as separate files with suffixes. This is not ideal for Kirby, as it expects the output to be a single image file. This plugin solves the issue by checking each image upfront converting for its frame count, and will then specify to convert the first frame only if the target format does not support multiple frames. It also adds a new option `frame` to manually specify the frame index. Additionally, this plugin adds support for `APNG`s images, which are unsupported because ImageMagick by default needs to be told to treat them as animated images.

The detection of available image frames requires the `identify` command to be available, which can be seen as breaking change, hence the creation of this plugin as it's unlikely to be merged in Kirby Core for fixing a variety of very niche edge case scenarios.

## Requirements

- Kirby 3.8+
- PHP 8.0+
- ImageMagick 7.0+ with `convert` and `identify` commands

## Installation

### Download

Download and copy this repository to `/site/plugins/kirby-magick-extended`.

### Composer

```
composer require tobimori/kirby-magick-extended
```

## Usage

### Set driver in config

```php
// site/config/config.php

return [
  'thumbs.driver' => 'im-extended',
];
```

When applied, the plugin will already automatically detect animated images and convert only the first frame, if the target format is unsupported.

### Use `frame` option

```php
// In your template file

<?php if($image = $page->animated()->toFile()) :
  $thumbOptions = [
    'width' => 100,
    'height' => 100,
    'frame' => 0, // specify frame index
    'format' => 'png',
  ]; ?>
  <img src="<?= $image->thumb($thumbOptions)->url() ?>" />
<?php endif ?>
```

### Disable APNG detection

```php
// In your template file

<?php if($image = $page->animated()->toFile()) :
  $thumbOptions = [
    'width' => 100,
    'height' => 100,
    'apng' => false, // disable APNG detection
    'format' => 'png',
  ]; ?>
  <img src="<?= $image->thumb($thumbOptions)->url() ?>" />
<?php endif ?>
```

### Custom `identify` binary path

```php
// site/config/config.php
return [
  'thumbs' => [
    'identifyBin' => 'identify',
  ],
];
```

## Future?

- [ ] Add unit tests from PR
- [x] Add APNG support with auto-prefixing of `APNG:`
- [ ] Get approved to be merged into Kirby Core - tracked in [PR #4275](https://github.com/getkirby/kirby/pull/4275)

## Credits

This plugin extends [Kirby's ImageMagick driver](https://github.com/getkirby/kirby/blob/develop/src/Image/Darkroom/ImageMagick.php), originally written by Bastian Allgeier, licensed under [MIT License](https://opensource.org/licenses/MIT).

## License

[MIT License](./LICENSE)
Copyright © 2023 Tobias Möritz
