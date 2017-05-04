<?php
/**
 * JULib cli for Joomla!
 *
 * @package    JULib
 *
 * @copyright  Copyright (C) 2016-2017 Denys Nosov. All rights reserved.
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 *
 */

const _JEXEC = 1;

if(file_exists(dirname(__DIR__) . '/defines.php'))
{
    require_once dirname(__DIR__) . '/defines.php';
}

if(!defined('_JDEFINES'))
{
    define('JPATH_BASE', dirname(__DIR__));
    require_once (JPATH_BASE . '/includes/defines.php');
}

require_once (JPATH_LIBRARIES . '/import.legacy.php');
require_once (JPATH_LIBRARIES . '/cms.php');

error_reporting(0);
ini_set('display_errors', 0);

/**
 * A command line cron job to attempt to remove files that should have been deleted at update.
 *
 * @since  2.0
 */
class JUImgCli extends JApplicationCli
{

    public function doExecute()
    {
        $clear = $this->unlinkRecursive(JPATH_BASE . '/img');
        $clear .= $this->unlinkRecursive(JPATH_BASE . '/cache/com_content');
        $clear .= $this->unlinkRecursive(JPATH_BASE . '/cache/mod_junewsultra');
        $clear .= $this->unlinkRecursive(JPATH_BASE . '/cache/com_jursspublisher');
        $clear .= $this->unlinkRecursive(JPATH_BASE . '/cache/com_jursspublisher_yatableau');

        $this->out($clear);

        return;
    }

    /**
     * @param $dir
     */
    public function unlinkRecursive($dir)
    {
        if(!$dh = opendir($dir))
        {
            return;
        }

        while (false !== ($obj = readdir($dh)))
        {
            if($obj == '.' || $obj == '..')
            {
                continue;
            }

            print $dir . "/" . $obj . "\n";

            if(!unlink($dir . "/" . $obj))
            {
                $this->unlinkRecursive($dir . "/" . $obj);
            }
        }
        closedir($dh);

        @rmdir($dir);

        return;
    }
}

JApplicationCli::getInstance('JUImgCli')->execute();