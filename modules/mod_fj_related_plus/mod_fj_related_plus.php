<?php
/**
* @version		$Id: mod_fj_related_plus.php 12 2009-07-07 21:39:25Z dextercowley $
* @package		mod_fj_related_plus
* @copyright	Copyright (C) 2008 Mark Dexter. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once (dirname(__FILE__).DS.'helper.php');

$list = modFJRelatedPlusHelper::getList($params); // get return results from the helper
$articleView = modFJRelatedPlusHelper::isArticle(); // is this an article?
$subtitle = '';


if (!count($list)) {  // no articles to list. check whether we want to show some text
	//return;
	if ($articleView != 'true' && ($params->def('notArticleText','')))
	{
		$subtitle = $params->def('notArticleText','');
	}
	else if ($params->def('noRelatedText','') && $articleView == 'true')
	{
		$subtitle = $params->def('noRelatedText','');
	}
	else 
	{
		return;
	}
}

// choose layout based on ordering parameter
if ($params->get('ordering') == 'keyword_article' && count($list))
{
	$layout = 'keyword';
}
else 
{
	$layout = 'default';
}
$path = JModuleHelper::getLayoutPath('mod_fj_related_plus', $layout);
if (file_exists($path)) {
	require($path);
}
