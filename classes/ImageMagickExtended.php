<?php

namespace tobimori;

use Exception;
use Kirby\Filesystem\F;
use Kirby\Image\Darkroom\ImageMagick;

class ImageMagickExtended extends ImageMagick
{
  /**
   * Keep animated gifs & animated pngs
   */
  protected function coalesce(string $file, array $options): string|null
  {
    if (F::extension($file) === 'gif' || $options['apng'] && F::mime($file) === 'image/png') {
      return '-coalesce';
    }

    return null;
  }

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
    $fileSuffix = '';
    $filePrefix = '';
    $frame = $options['frame'];

    // assume image is apng, if apng option is set and file is png
    if ($options['apng'] && F::mime($file) === 'image/png') {
      $filePrefix = 'apng:';
    }

    $maxFrames = $this->frameCount($filePrefix . $file, $options);

    if ($maxFrames === 1) {
      $filePrefix = ''; // remove apng prefix if image is not animated
    }

    // get frame argument
    if ($frame !== null) {
      // check whether frame is in bounds
      if ($frame < 0) {
        throw new Exception('Frame option must be a positive integer');
      }

      if ($frame !== 0 && $frame >= $maxFrames) {
        throw new Exception('Frame option must be smaller than the number of frames in the image');
      }

      $fileSuffix = "[{$frame}]";
    } elseif ($maxFrames > 1) {
      // if frame is not set and target format doesn't support multi-layer images, select first frame
      $targetFormat = $options['format'] ?? F::extension($file);
      $multiLayerFormats = ['gif', 'avif', 'webp', $options['apng'] ? 'png' : null];
      if (!in_array($targetFormat, $multiLayerFormats)) {
        $fileSuffix = '[0]';
      }
    }

    // append input file
    return $command . ' ' . escapeshellarg($filePrefix . $file . $fileSuffix);
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
      'apng'        => true,
    ];
  }
}
