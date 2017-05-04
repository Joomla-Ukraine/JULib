<?php
/**
 * JULib for Joomla!
 *
 * @package    JULib
 *
 * @copyright  Copyright (C) 2012-2017 Denys Nosov. All rights reserved.
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 */

/**
 * Class to perform additional changes during render/create
 *
 * @package  JUNewsUltra Pro
 * @since    2.0
 */
class JUImg
{
    /**
     * @param      $url
     * @param null $attr
     *
     * @return string
     */
    public function Render($url, $attr = null)
    {
        $path = JPATH_BASE . '/';

        if($url != 'cover')
        {
            $url = trim($url, '/');
            $url = trim($url);
            $url = rawurldecode($url);

            $_error = 0;
            if(preg_match('#^(http|https|ftp)://#i', $url))
            {
                $headers = @get_headers($url);
                if(strpos($headers[0], '200') === false)
                {
                    $_error = 1;
                }
            }
            else
            {
                $url = $path . $url;
                if(!file_exists($url))
                {
                    $_error = 1;
                }
            }

            $imgfile = pathinfo($url);
            $imgurl  = $this->_URLconverter($imgfile['filename']);
        }
        else
        {
            $_error   = 0;
            $img_name = implode($attr);
            $imgurl   = 'cover';
        }

        $fext        = array();
        $wh          = array();
        $img_cache   = array();
        $error_image = array();
        foreach ($attr as $whk => $whv)
        {
            if($whk == 'f')
            {
                $fext[] = $whv;
            }

            if($whk == 'w' || $whk == 'h')
            {
                $wh[] = $whv;
            }

            if($whk == 'cache')
            {
                $img_cache[] = $whv;
            }

            if($whk == 'error_image')
            {
                $error_image[] = $whv;
            }
        }

        $fext      = implode($fext);
        $fext      = '.' . ($fext == '' ? 'jpg' : $fext);
        $img_cache = implode($img_cache);
        $img_cache = ($img_cache == '' ? 'cache' : $img_cache);

        if($_error == 1)
        {
            $error_image = implode($error_image);
            $blank_image = $path . 'libraries/julib/noimage.png';
            $url         = ($error_image == '' ? $blank_image : $error_image);
        }

        $wh        = implode('x', $wh);
        $wh        = ($wh == '' ? '0' : $wh);
        $subfolder = $img_cache . '/' . $wh . '/' . substr(strtolower(MD5($img_name)), -1);

        $md5 = array();
        foreach ($attr as $k => $v)
        {
            $f     = explode("_", $k);
            $k     = $f[0];
            $md5[] = $k . $v;
        }

        $target = $subfolder . '/' . substr(strtolower($imgurl), 0, 150) . '-' . MD5($url . implode('.', $md5)) . $fext;

        $this->_MakeDirectory($dir = $path . $subfolder, $mode = 0777);

        if(file_exists($path . $target))
        {
            $outpute = $target;
        }
        else
        {
            $outpute = $this->Create($url, $img_cache, $target, $attr);
        }

        return $outpute;
    }

    /**
     * @param      $url
     * @param      $img_cache
     * @param      $target
     * @param null $attr
     *
     * @return string
     */
    public function Create($url, $img_cache, $target, $attr = null)
    {
        include_once(__DIR__ . '/phpthumb/phpthumb.class.php');
        $phpThumb = new JUThumbs();

        $path       = JPATH_BASE . '/';
        $cache_path = $path . $img_cache . '/';

        $phpThumb->resetObject();

        $phpThumb->setParameter('config_max_source_pixels', round(max(intval(ini_get('memory_limit')), intval(get_cfg_var('memory_limit'))) * 1048576 / 6));

        $phpThumb->setParameter('config_temp_directory', $cache_path);
        $phpThumb->setParameter('config_cache_directory', $cache_path);
        $phpThumb->setCacheDirectory();

        $phpThumb->setParameter('config_cache_maxfiles', '0');
        $phpThumb->setParameter('config_cache_maxsize', '0');
        $phpThumb->setParameter('config_cache_maxage', '0');

        $phpThumb->setParameter('config_error_bgcolor', 'FAFAFA');
        $phpThumb->setParameter('config_error_textcolor', '770000');

        $phpThumb->setParameter('config_nohotlink_enabled', false);

        if($url == 'cover')
        {
            $cover = array();
            foreach ($attr as $whk => $whv)
            {
                if($whk == 'cover')
                {
                    $cover[] = $whv;
                }
            }

            $phpThumb->setSourceFilename($path . 'libraries/julib/blank.png');
            $phpThumb->setParameter('fltr', 'clr|' . implode($cover));
        }
        else
        {
            $phpThumb->setSourceFilename($url);
        }

        $phpThumb->setParameter('q', '80');
        $phpThumb->setParameter('aoe', '1');
        $phpThumb->setParameter('f', 'jpg');

        if(is_array($attr))
        {
            foreach ($attr as $k => $v)
            {
                $f = explode("_", $k);
                $k = $f[0];
                $phpThumb->setParameter($k, $v);
            }
        }

        $imagemagick = '';
        if(strtoupper(substr(PHP_OS, 0, 3)) == 'WIN')
        {
            $imagemagick = 'C:/ImageMagick/convert.exe';
        }

        $phpThumb->setParameter('config_imagemagick_path', $imagemagick);
        $phpThumb->setParameter('config_prefer_imagemagick', true);
        $phpThumb->setParameter('config_imagemagick_use_thumbnail', true);

        $outpute = '';
        if($phpThumb->GenerateThumbnail())
        {
            if($phpThumb->RenderToFile($path . $target))
            {
                $outpute = $target;
            }

            $phpThumb->purgeTempFiles();
        }

        return $outpute;
    }

    /**
     * @param $dir
     * @param $mode
     *
     * @return bool
     */
    public function _MakeDirectory($dir, $mode)
    {
        if(is_dir($dir) || @mkdir($dir, $mode))
        {
            $indexfile    = $dir . '/index.html';
            $indexcontent = '<!DOCTYPE html><title></title>';

            if(!file_exists($indexfile))
            {
                $file = fopen($indexfile, 'w');
                fputs($file, $indexcontent);
                fclose($file);
            }

            return true;
        }

        if(!$this->_MakeDirectory(dirname($dir), $mode))
        {
            return false;
        }

        return @mkdir($dir, $mode);
    }

    /**
     * @param $url
     *
     * @return mixed
     */
    public function _URLconverter($url)
    {
        $url = strtolower($url);
        $url = preg_replace("#[[:punct:]]#", "", $url);
        $url = preg_replace("#[а-я]#isu", "", $url);
        $url = str_replace(" +", "_", $url);
        $url = str_replace(" ", "", $url);

        return $url;
    }
}