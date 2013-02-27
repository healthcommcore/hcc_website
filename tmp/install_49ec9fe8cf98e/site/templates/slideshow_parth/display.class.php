<?php
/**
 * @version $Id$
 * @package RSGallery2
 * @copyright (C) 2003 - 2007 RSGallery2
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined( '_JEXEC' ) or die( 'Restricted Access' );

/**
 * Slideshow class for RSGallery2
 * Based on Smoothgallery from Johndesign.net
 * @package RSGallery2
 * @author Ronald Smit <ronald.smit@rsdev.nl>, based on contribution by Parth<parth.lawate@tekdi.net>
 */
class rsgDisplay_slideshow_parth extends rsgDisplay{

	function showSlideShow(){
		global $rsgConfig;
		
		$gallery = rsgGalleryManager::get();
		
		// show nothing if there are no items
		if( ! $gallery->itemCount() )
			return;
		
		$k = 0;
		$text = "";
		foreach ($gallery->items() as $item){
			if( $item->type != 'image' ) return;

			$display = $item->display();
			$thumb = $item->thumb();

			$text .= "<div class=\"imageElement\">" .
					"<h3>$item->title</h3>" .
					"<p>$item->descr</p>" .
					"<a href=\"#\" title=\"open image\" class=\"open\"></a>" .
					"<img src=\"".$display->url()."\" class=\"full\" />" .
					"<img src=\"".$thumb->url()."\" class=\"thumbnail\" />" .
					"</div>";
			$k++;
		}
		$this->slides = $text;
		$this->galleryname = $gallery->name;
		$this->gid = $gallery->id;
		
		$this->display('slideshow.php');
	}
}