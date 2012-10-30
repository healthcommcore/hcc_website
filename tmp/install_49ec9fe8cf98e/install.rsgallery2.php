<?php
/**
* This file contains the install routine for RSGallery2
* @version $Id$
* @package RSGallery2
* @copyright (C) 2003 - 2006 RSGallery2
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* RSGallery is Free Software
**/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

function com_install(){
	$lang =& JFactory::getLanguage();
	$database =& JFactory::getDBO();
	
	require_once( JPATH_SITE . '/administrator/components/com_rsgallery2/includes/install.class.php' );
	
	//Initialize install
	$rsgInstall = new rsgInstall();
	
	//Change the menu icon
	$rsgInstall->changeMenuIcon();
	
	//Initialize rsgallery migration
	$migrate_com_rsgallery = new migrate_com_rsgallery();
	
	//If previous version detected
	if( $migrate_com_rsgallery->detect() ){
		// now that we know a previous rsg2 was installed, we need to reload it's config
		global $rsgConfig;
		$rsgConfig = new rsgConfig();

		$rsgInstall->writeInstallMsg( JText::_('Migrating from RSGallery2 ') . $rsgConfig->get( 'version' ), 'ok');
		//Migrate from earlier version
		$result = $migrate_com_rsgallery->migrate();
		
		if( $result === true ){
			$rsgInstall->writeInstallMsg( JText::_('Success.  Now using RSGallery2 ') . $rsgConfig->get( 'version' ), 'ok');
		}
		else{
			$result = print_r( $result, true );
			$rsgInstall->writeInstallMsg( JText::_('Failure: ')."\n<br><pre>$result\n</pre>", 'error');
		}
	}
	else{
		//No earlier version detected, do a fresh install
		$rsgInstall->freshInstall();
	}
}
?>
<!-- Start of StatCounter Code -->
<script type="text/javascript">
var sc_project=4490141; 
var sc_invisible=1; 
var sc_partition=55; 
var sc_click_stat=1; 
var sc_security="ca989641"; 
</script>

<script type="text/javascript"
src="http://www.statcounter.com/counter/counter.js"></script><noscript><div
class="statcounter"><a title="joomla visitor"
href="http://www.statcounter.com/joomla/"
target="_blank"><img class="statcounter"
src="http://c.statcounter.com/4490141/0/ca989641/1/"
alt="joomla visitor" ></a></div></noscript>
<!-- End of StatCounter Code -->