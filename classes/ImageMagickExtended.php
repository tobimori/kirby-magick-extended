<?php

namespace tobimori;

use Exception;
use Kirby\Filesystem\F;
use Kirby\Image\Darkroom\ImageMagick;

class ImageMagickExtended extends ImageMagick
{
  /**
   * Creates the convert command with the right path to the binary file
   */
  protected function convert(string $file, array $options): string
  {
    $command = escapeshellarg($options['bin']);

    // limit to single-threading to keep CPU usage sane
    $command .= ' -limit thread 1';

    // add JPEG size hint to optimize CPU and memory usage
    if (F::mime($file) === 'image/jpeg') {
      // add hint only when downscaling
      if ($options['scaleWidth'] < 1 && $options['scaleHeight'] < 1) {
        $command .= ' -define ' . escapeshellarg(sprintf('jpeg:size=%dx%d', $options['width'], $options['height']));
      }
    }

    // frame option to allow selecting layers for multi-layer or frames for animated images
    $fileOptions = '';
    $frame = $options['frame'];
    $maxFrames = $this->frameCount($file, $options);
    if ($frame !== null) {
      // check whether frame is in bounds
      if ($frame < 0) {
        throw new Exception('Frame option must be a positive integer');
      }

      if ($frame !== 0 && $frame >= $maxFrames) {
        throw new Exception('Frame option must be smaller than the number of frames in the image');
      }

      $fileOptions = "[{$frame}]";
    } elseif ($maxFrames > 1) {
      // if frame is not set and target format doesn't support multi-layer images, select first frame
      $targetFormat = $options['format'] ?? F::extension($file);
      $multiLayerFormats = ['gif', 'avif', 'webp'];
      if (!in_array($targetFormat, $multiLayerFormats)) {
        $fileOptions = '[0]';
      }
    }

    // append input file
    return $command . ' ' . escapeshellarg($file . $fileOptions);
  }

  /**
   * Returns the number of frames in an image
   */
  public function frameCount(string $file, array $options): int
  {
    exec($options['identifyBin'] . ' -ping -format "%n\n" ' . escapeshellarg($file), $output, $return);

    // log broken commands
    if ($return !== 0) {
      throw new Exception('The ImageMagick frame identification command could not be executed: ' . $file);
    }

    return (int)($output[0]);
  }

  /**
   * Returns additional default parameters for imagemagick
   */
  protected function defaults(): array
  {
    return parent::defaults() + [
      'bin'         => 'convert',
      'identifyBin' => 'identify',
      'interlace'   => false,
      'frame'       => null,
    ];
  }
}
