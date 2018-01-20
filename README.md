# JULib

Create thumbs for Joomla! extension or stand-alone use.
JULib is PHP wrapper for [phpThumb()](https://github.com/JamesHeinrich/phpThumb) Class by James Heinrich.
 
## Demo (All thumbs)

* [Bad Android](https://bad-android.com)
* [Високий замок](https://wz.lviv.ua)
* [Львівська міська рада](http://city-adm.lviv.ua)

## Use in Joomla! Extension

* [JUNewsUltra](https://github.com/Joomla-Ukraine/JUNewsUltra)
* [JUMultiThumb](https://github.com/Joomla-Ukraine/JUMultiThumb)
* JURSSPublisher

## Code Example
### Joomla! Integration

Install library from Joomla! Extension Manager. Code for use in your extension.

```

require_once(JPATH_SITE . '/libraries/julib/image.php');
$JUImg = new JUImg();

$image = 'images/sampledata/fruitshop/apple.jpg'
  
$options = array(
  	'w'     => '300',
  	'h'     => '100',
  	'q'     => '77',
  	'cache' => 'img'
);
  
$thumb = $JUImg->Render($image, $options);

echo '<img src=". $thumb ."' alt="Apple" width="300" height="100">';

```
	 
### Stand-alone usage

```

define('JPATH_BASE', $_SERVER['DOCUMENT_ROOT']);

require_once(JPATH_SITE . '/libraries/julib/image.php');
$JUImg = new JUImg();

$image = 'images/sampledata/fruitshop/apple.jpg'
  
$options = array(
  	'w'     => '300',
  	'h'     => '100',
  	'q'     => '77',
  	'cache' => 'img'
);
  
$thumb = $JUImg->Render($image, $options);

echo '<img src=". $thumb ."' alt="Apple" width="300" height="100">';

```

## Options

Add option to this array:

```
$options = array(
  	'w'     => '300',
  	'h'     => '100',
  	'q'     => '77',
  	'cache' => 'img'
);
```

| Command | Description |
| --- | --- |
| cache | folder for thumbnails|
|   w | max width of output thumbnail in pixels|
|   h | max height of output thumbnail in pixels|
|  wp | max width for portrait images|
|  hp | max height for portrait images|
|  wl | max width for landscape images|
|  hl | max height for landscape images|
|  ws | max width for square images|
|  hs | max height for square images|
|   f | output image format ("jpeg", "png", or "gif")|
|   q | JPEG compression (1=worst, 95=best, 75=default)|
|  sx | left side of source rectangle (default \| 0) (values 0 < sx < 1 represent percentage)|
|  sy | top side of source rectangle (default \| 0) (values 0 < sy < 1 represent percentage)|
|  sw | width of source rectangle (default \| fullwidth) (values 0 < sw < 1 represent percentage)|
|  sh | height of source rectangle (default \| fullheight) (values 0 < sh < 1 represent percentage)|
|  zc | zoom-crop. Will auto-crop off the larger dimension so that the image will fill the smaller dimension (requires both "w" and "h", overrides "iar", "far"). Set to "1" or "C" to zoom-crop towards the center, or set to "T", "B", "L", "R", "TL", "TR", "BL", "BR" to gravitate towards top/left/bottom/right directions (requies ImageMagick for values other than "C" or "1")|
|  bg | background hex color (default | FFFFFF)|
|  bc | border hex color (default | 000000)|
| xto | EXIF Thumbnail Only - set to only extract EXIF thumbnail and not do any additional processing|
|  ra | Rotate by Angle: angle of rotation in degrees positive \| counterclockwise, negative \| clockwise|
|  ar | Auto Rotate: set to "x" to use EXIF orientation stored by camera. Can also be set to "l" or "L" for landscape, or "p" or "P" for portrait. "\l" and "P" rotate the image clockwise, "L" and "p" rotate the image counter-clockwise.|
| sfn | Source Frame Number - use this frame/page number for multi-frame/multi-page source images (GIF, TIFF, etc)|
| aoe | Output Allow Enlarging - 1=on, 0=off. "far" and "iar" both override this and allow output larger than input)|
| iar | Ignore Aspect Ratio - disable proportional resizing and stretch image to fit "h" & "w" (which must both be set).  (1=on, 0=off)  (overrides "far")|
| far | Force Aspect Ratio - image will be created at size specified by "w" and "h" (which must both be set). Alignment: L=left,R=right,T=top,B=bottom,C=center. BL,BR,TL,TR use the appropriate direction if the image is landscape or portrait.|
| dpi | Dots Per Inch - input DPI setting when importing from vector image format such as PDF, WMF, etc
| sia | Save Image As - default filename to save generated image as. Specify the base filename, the extension (eg: ".png") will be automatically added|
|maxb | MAXimum Byte size - output quality is auto-set to fit thumbnail into "maxb" bytes  (compression quality is adjusted for JPEG, bit depth is adjusted for PNG and GIF)|

## License

GNU General Public License version 2 or later; see [LICENSE.md](LICENSE.md)