<?php

defined('_JEXEC') or die();

$module_name = JRequest::getString('module','mod_latestnews');

$db		=& JFactory::getDBO();
$query = "SELECT DISTINCT * from #__modules where title='".$module_name."'";
$db->setQuery( $query );
$result = $db->loadObject();


if ($result) {

    $document	= &JFactory::getDocument();
    $renderer	= $document->loadRenderer( 'module' );
    $options	 = array( 'style' => "raw" );
    $module	 = JModuleHelper::getModule( $result->module );
    $module->params = $result->params;

    $output = $renderer->render( $module, $options );

    echo $output;
    
}


?>