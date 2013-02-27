<?php
/**
* Initialize default instance of RSGallery2
* @version $Id$
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
*/
defined( '_JEXEC' ) or die( 'Access Denied.' );

// initialize RSG2 core functionality
require_once( JPATH_SITE. DS . "administrator" . DS . "components" . DS . "com_rsgallery2" . DS . "init.rsgallery2.php" );

// createa new instance of RSGallery2
rsgInstance::instance();