<?php
/**
 * @version		$Id: router.php 7380 2007-05-06 21:26:03Z eddieajau $
 * @package		Joomla
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

function Rsgallery2BuildRoute(&$query)
{
	static $items;
	
	$segments	= array();
	$itemid		= null;
	
	// Get the menu items for this component.
	if (!$items) {
		$component	= &JComponentHelper::getComponent('com_contact');
		$menu		= &JSite::getMenu();
		$items		= $menu->getItems('componentid', $component->id);
	}
	
	if(isset($query['gid'])){
		$segments[] = 'category';
		$segments[] = $query['gid'];
		unset($query['gid']);
	}
	if(isset($query['catid'])){
		$segments[] = 'category';
		$segments[] = $query['catid'];
		unset($query['catid']);
	}
	
	if(isset($query['id'])){
		$segments[] = 'item';
		$segments[] = $query['id'];
		unset($query['id']);
	}
	
	if(isset($query['page'])){
		$segments[] = 'as' . ucfirst($query['page']);
		unset($query['page']);
	}
	


	
	return $segments;
}

function Rsgallery2ParseRoute($segments)
{
	$vars	= array();
	
	// Get the active menu item.
	$menu	= &JSite::getMenu();
	$item	= &$menu->getActive();
	
	for ($index = 0 ; $index < count($segments) ; $index++){
		
		switch ($segments[$index]){
			case 'category':
			{
				$vars['gid'] = $segments[++$index];
				break;
			}
			case 'item':
			{
				$vars['id'] = $segments[++$index];
				break;
			}
			
		}
	
		if(strstr($segments[$index],'as'))
		{
			$vars['page'] = strtolower(substr($segments[$index],2));
		}
	}
	
	return $vars;
}