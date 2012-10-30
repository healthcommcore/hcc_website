<?php
/**
* @version mod_arhiva.php 204 2009-04-19
* @package Joomla
* @copyright Copyright (C) 2009 Maksin.ms.
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* @author Branislav Maksin
* @link www.maksin.ms
**/

// Onemoguci direktan pristup modulu
defined('_JEXEC') or die( 'Direktan pristup ovoj lokaciji je zabranjen.' );

// Parametri
global $mainframe;

$sadrzaj	= $params->def('sadrzaj', 0);
$katid		= trim($params->get('katid'));
$sekid		= trim($params->get('sekid'));
$js			= $params->def('js', 1);
$sirina		= intval($params->def('sirina', 160));
$pozadina	= $params->get('pozadina');
$ivica		= $params->get('ivica');
$alink		= $params->def('link', 1);
$broj		= intval($params->def('broj', 10));
$prikaz		= $params->def('prikaz', 0);
$autor		= $params->def('autor', 1);
$sef		= $params->get('sef', 1);
$naziv		= $params->get('naziv');
$pslika		= $params->get('pslika', 1);
$vpl		= $params->get('vpl');


// Ako nije postavljen, dodeli naziv linka
if (empty($naziv)) {
	$naziv = 'Arhiva';
}

// Ne prikazuj clanke sa dozvolama
if ($prikaz == 0) {
	$pristup = ( !$mainframe->getCfg( 'shownoauth' ) ? "\n AND access <= " . (int) $my->gid : '' );
}

// Pronadji odabrane sekcije i kategorije
if ($katid) {
	$katids = explode( ',', $katid );
	JArrayHelper::toInteger( $katids );
	$wkatid = "\n AND ( catid=" . implode( " OR catid=", $katids ) . " )";
}
if ($sekid) {
	$sekids = explode( ',', $sekid );
	JArrayHelper::toInteger( $sekids );
	$wsekid = "\n AND ( sectionid=" . implode( " OR sectionid=", $sekids ) . " )";
}

// Odredi trazeni sadrzaj
if ($sadrzaj == 0) {
	$go = "\n GROUP BY created_year DESC, created_month DESC";
} else {
	$go = "\n ORDER BY created DESC";
}

// Glavni upit za bazu
$query = "SELECT MONTH( created ) AS created_month, created, id, sectionid, title, YEAR(created) AS created_year"
. "\n FROM #__content"
. "\n WHERE ( state = -1 AND checked_out = 0 )"
. ($katid ? $wkatid : '')
. ($sekid ? $wsekid : '')
. $pristup
. $go;

/* Prikazi link sa ili bez pozadinske slike
if ($pslika == 1) {
	$lnk = '<div align="center"><div style="width:116px; height:36px; background:url(modules/mod_arhiva/slike/arhiva.png);">
			<div style="padding-top:'.$vpl.'px; font-weight:bold;">'.$naziv.'</div></div></div>';
} else {
	$lnk = '<div style="text-align:center; font-weight:bold;">'.$naziv.'</div>';
}*/

// Konstantni parametri
$ajsp = '
<script type="text/javascript">
	function prikazi(a)
	{
		var e=document.getElementById(a);
			if(!e)
			return true;
		if(e.style.display=="none")
		{
			e.style.display="block"
		} else {
			e.style.display="none"
		}
		return true;
	}   
</script>';

$acss = '
<style type="text/css">
	<!--    
	* { padding:0; margin:0; }
	.dropdown dt { text-align:center; cursor:pointer; }
	.dropdown dd { position:absolute; overflow:hidden; display:none; z-index:200; }
	.dropdown ul {border: 1px solid #'.$ivica.'; width:'.$sirina.'px; background-color:#'.$pozadina.'; }
	-->
</style>
';

$ajss = '<script type="text/javascript">
var DDSPEED=10;var DDTIMER=15;function ddMenu(id,d){var h=document.getElementById(id+\'-ddheader\');var c=document.getElementById(id+\'-ddcontent\');clearInterval(c.timer);if(d==1){clearTimeout(h.timer);if(c.maxh&&c.maxh<=c.offsetHeight){return}else if(!c.maxh){c.style.display=\'block\';c.style.height=\'auto\';c.maxh=c.offsetHeight;c.style.height=\'0px\'}c.timer=setInterval(function(){ddSlide(c,1)},DDTIMER)}else{h.timer=setTimeout(function(){ddCollapse(c)},50)}}function ddCollapse(c){c.timer=setInterval(function(){ddSlide(c,-1)},DDTIMER)}function cancelHide(id){var h=document.getElementById(id+\'-ddheader\');var c=document.getElementById(id+\'-ddcontent\');clearTimeout(h.timer);clearInterval(c.timer);if(c.offsetHeight<c.maxh){c.timer=setInterval(function(){ddSlide(c,1)},DDTIMER)}}function ddSlide(c,d){var currh=c.offsetHeight;var dist;if(d==1){dist=(Math.round((c.maxh-currh)/DDSPEED))}else{dist=(Math.round(currh/DDSPEED))}if(dist<=1&&d==1){dist=1}c.style.height=currh+(dist*d)+\'px\';c.style.opacity=currh/c.maxh;c.style.filter=\'alpha(opacity=\'+(currh*100/c.maxh)+\')\';if((currh<2&&d!=1)||(currh>(c.maxh-2)&&d==1)){clearInterval(c.timer)}}
</script>
';
	
// Ako postoji Itemid za Arhivu pronadji ga u menijima
$menu = &JSite::getMenu();
$item = $menu->getItems('link', 'index.php?option=com_content&view=archive', true);
$itemid = isset($item) ? '&Itemid='.$item->id : '';
	
// Odaberi vrstu linka i JavaScript funkciju
if ($alink) {
	$d = '</div>
		 ';
	if ($js == 1) {
		echo $acss.$ajss;
		echo '	<dl class="dropdown">
		<dt id="one-ddheader" onmouseover="ddMenu(\'one\',1)" onmouseout="ddMenu(\'one\',-1)">'.$lnk.'</dt>
		<dd id="one-ddcontent" onmouseover="cancelHide(\'one\')" onmouseout="ddMenu(\'one\',-1)">
		<ul>
		';
		$d = '</dd>
</dl>
			';
	} elseif ($js == 2){
		echo $ajsp;
		echo "<a href=\"javascript:void(0)\" onclick=\"return prikazi('arhiva')\">".$lnk."</a><div id=\"arhiva\" style=\"display:none\"><ul>";
	} else {
		echo $lnk."<div class=\"archiveList\"><ul>";
	}
	
// Pronadji sav arhiviran sadrzaj
	$db =& JFactory::getDBO();			
	$db->setQuery($query, 0, $broj);
	$rows = $db->loadObjectList();
	
		foreach ( $rows as $row ) {
			if ($sadrzaj == 0) {
	
				$datum =& JFactory::getDate($row->created);
				
				$mesec_kreiranja = $datum->toFormat("%m");
				$ime_meseca	= $datum->toFormat("%B");
				$godina_kreiranja = $datum->toFormat("%Y");
							
				// Kreiraj link za svaki arhiviran mesec
				$link = JRoute::_( 'index.php?option=com_content&view=archive&year='.$godina_kreiranja.'&month='.$mesec_kreiranja.$itemid );
				$tekst = $ime_meseca .', '. $godina_kreiranja; 
				echo '<li><a href="'.$link.'">'.$tekst.'</a></li>';
							
			} else {
				
				// Zameni unikodne karaktere u linku (promeniti po zelji)
				if ($sef == 1) {
					$naslov = '%3A'.strtolower(str_replace(
					  array(' ','Ч','Ћ','Ж','Ш','Ђ','Џ','Љ','Њ','ч','ћ','ж','ш','ђ','џ','љ','њ','Č','Ć','Ž','Š','Đ','č','ć','ž','š','đ'),
					  array('-','c','c','z','s','dj','dz','lj','nj','c','c','z','s','dj','dz','lj','nj','c','c','z','s','dj','c','c','z','s','dj'),
					  $row->title));
				} else {
					$naslov = '%3A'.strtolower(strtr($row->title, " ", "-"));
				}
				
				// Skini duplirane crte u linku
				$naslov = preg_replace('{(.)\-}','$1',$naslov);
				
				// Kreiraj link za arhivirane clanke
				$link = JRoute::_( 'index.php?option=com_content&amp;view=article&amp;id='.$row->id.$naslov.$itemid );
				echo '<li><a href="'.$link.'">'.$row->title.'</a></li>';
							
			}
		}
					
	echo '
		</ul>
		'.$d; 
		
} else {
		
	// Kreiraj link prema strani za Arhivu (sa ili bez Itemid-a)
	$arhiva = "view=archive";
	$link = JRoute::_( 'index.php?option=com_content&amp;'.$arhiva.$itemid );
	echo "<a href=\"".$link."\">".$lnk."</a>";
		
}

// Link autora
if ($autor) {
	echo '<br /><div style="font-style:italic;" align="center"><a href="http://www.maksin.ms" target="_blank">Maksin.ms development</a></div>';
}

?>