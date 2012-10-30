<?php
/*
// "Simple Image Gallery" (in content items) Plugin for Joomla 1.5 - Version 1.2.1
// License: http://www.gnu.org/copyleft/gpl.html
// Authors: Fotis Evangelou - George Chouliaras
// Copyright (c) 2008 JoomlaWorks.gr - http://www.joomlaworks.gr
// Project page at http://www.joomlaworks.gr - Demos at http://demo.joomlaworks.gr
// ***Last update: January 6th, 2007***

// Modified by Andreas Berger - http://www.bretteleben.de
// Plugin Name changed to "Very Simple Image Gallery" - Version 1.2.2
// Lightbox removed, large image added, further modifications.
// Modifications and additions Copyright (c) 2009 Andreas Berger - andreas_berger@bretteleben.de
// License: http://www.gnu.org/copyleft/gpl.html
// Project page and Demo at http://www.bretteleben.de
// ***Last update: 2009-04-05***
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

//meta initialisieren
$mainframe->registerEvent( 'onAfterDisplayContent', 'switchmeta' );

// Import library dependencies
jimport('joomla.event.plugin');

// Simple Language Defines for English
// <--------------- English language Defines ------------------------------------->
define('_VSIG_GD_LIBMISSING','<b>Error</b>: GD2 library is not enabled in your server!');
define('_VSIG_GD_LIBNOJPG','<b>Error</b>: GD2 library does not support JPG!');
define('_VSIG_GD_LIBNOGIF','<b>Error</b>: GD2 library does not support GIF!');
define('_VSIG_GD_LIBNOPNG','<b>Error</b>: GD2 library does not support PNG!');
// <--------------- END --------------------------------------------------------->

class plgContentVsig extends JPlugin
{
	//Constructor
	function plgContentVsig( &$subject )
	{
		parent::__construct( $subject );
		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'content', 'vsig' );
		$this->_params = new JParameter( $this->_plugin->params );
	}

	function onPrepareContent(&$row, &$params, $limitstart) {

		// just startup
		global $mainframe;
		// root folder
		$rootfolder = '/images/stories/';
		//notice: if you want to use folders outside images/stories set $rootfolder = '/'; to start from joomla-root

		// checking
		if ( !preg_match("#{vsig}(.*?){/vsig}#s", $row->text) ) {
			return;
		}

		$plugin =& JPluginHelper::getPlugin('content', 'vsig');
		$pluginParams = new JParameter( $plugin->params );

		// j!1.5 paths
		$mosConfig_absolute_path = JPATH_SITE;
		$mosConfig_live_site = JURI :: base();
		if(substr($mosConfig_live_site, -1)=="/") $mosConfig_live_site = substr($mosConfig_live_site, 0, -1);

		// Parameters
		$_width_ 			= $pluginParams->get('th_width', 120);	//thumbs
		$_height_ 		= $pluginParams->get('th_height', 90);	//thumbs
		$_quality_ 		= $pluginParams->get('th_quality', 80);	//thumbs
		$_space_ 			= $pluginParams->get('th_space', 5);		//thumbs
		$_imwidth_ 		= $pluginParams->get('im_width', 90);		//image
		$_impercent_	= $pluginParams->get('im_percent', 1);	//image
		$_im_align_ 	= $pluginParams->get('im_align', 1);		//image
		$_usescript_ 	= $pluginParams->get('usescript', 1);		//javascript
		$_th_right_ 	= $pluginParams->get('th_right', 2);		//layout
		$_th_area_ 		= $pluginParams->get('th_area', 30);		//layout
		$_setid_ 			= $pluginParams->get('setid', 0);				//foldername
		$_cap_show_ 	= $pluginParams->get('cap_show', 1);		//captions
		$_cap_pos_ 		= $pluginParams->get('cap_pos', 1);			//captions
		$_link_use_ 	= $pluginParams->get('link_use', 1);		//captions

		//calculations
		$_tempwidth=($_impercent_)?100:$_imwidth_;
		$_im_area_=$_tempwidth;
		if($_th_right_=="1"){
			$_im_area_=intval($_tempwidth-$_tempwidth/100*$_th_area_);
			$_rulerspace_=intval(($_tempwidth-$_im_area_)/10*9);
			$_rulerspace_.=($_impercent_)?"%":"px";
		}
		//justify
		$therealimagearea=$_im_area_;
		if(!$_impercent_&&$_th_right_=="2"){
			$countthumbs=intval($_im_area_/($_width_+10+$_space_));
			$toremove=$_im_area_-($countthumbs*($_width_+10+$_space_));
			$newwidth=$_width_+intval($toremove/$countthumbs);
			$_height_=intval($_height_/$_width_*$newwidth);
		  $_width_=$newwidth;
			$therealimagearea=$countthumbs*($_width_+10+$_space_)-10-$_space_;
		}
		$_im_area_.=($_impercent_)?"%":"px";
		//set Imagewidth according the settings to percent/pixel
		$_imwidth_.=($_impercent_)?"%":"px";
		$therealimagearea.=($_impercent_)?"%":"px";
		// GD2 Library Check
		if(function_exists("gd_info")) {
			$gdinfo = gd_info();
			$gdsupport = array();
			$version = intval(ereg_replace('[[:alpha:][:space:]()]+', '', $gdinfo['GD Version']));
			if($version!=2) $gdsupport[] = '<div class="message">'._VSIG_GD_LIBMISSING.'</div>';
			if (!$gdinfo['JPG Support']) $gdsupport[] = '<div class="message">'._VSIG_GD_LIBNOJPG.'</div>';
			if (!$gdinfo['GIF Create Support']) $gdsupport[] = '<div class="message">'._VSIG_GD_LIBNOGIF.'</div>';
			if (!$gdinfo['PNG Support']) $gdsupport[] = '<div class="message">'._VSIG_GD_LIBNOPNG.'</div>';
			if(count($gdsupport)) {
				foreach ($gdsupport as $k=>$v) {echo $v;}
			}
		}
//captions
		if (preg_match_all("#{vsig_c}(.*?){/vsig_c}#s", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
			$vsig_captions=array();
			$document =& JFactory::getDocument();
			foreach ($matches[0] as $match) {
				$_raw_cap_ = preg_replace("/{.+?}/", "", $match);
				$_raw_cap_exp_ = explode("|",$_raw_cap_);
				$cap1=($_raw_cap_exp_[1]&&trim($_raw_cap_exp_[1])!="")?(trim($_raw_cap_exp_[1])):("CAPDEFAULT");
				$cap2=($_raw_cap_exp_[2]&&trim($_raw_cap_exp_[2])!="")?(trim($_raw_cap_exp_[2])):("");
				$cap3=($_raw_cap_exp_[3]&&trim($_raw_cap_exp_[3])!="")?(trim($_raw_cap_exp_[3])):("");
				$caparray="cap_ar".$_raw_cap_exp_[0];
				if(!isset($$caparray)){$$caparray=array();};
				${$caparray}[$cap1]=array($cap2,$cap3);
				$_raw_cap_ = preg_quote($_raw_cap_, '#'); 
				$row->text = preg_replace( "#{vsig_c}".$_raw_cap_."{/vsig_c}#s", '' , $row->text );
			}
		}
//captions
//links
		if (preg_match_all("#{vsig_l}(.*?){/vsig_l}#s", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
			$vsig_captions=array();
			$document =& JFactory::getDocument();
			foreach ($matches[0] as $match) {
				$_raw_link_ = preg_replace("/{.+?}/", "", $match);
				$_raw_link_exp_ = explode("|",$_raw_link_);
				$_link1=($_raw_link_exp_[1]&&trim($_raw_link_exp_[1])!="")?(trim($_raw_link_exp_[1])):("LINKDEFAULT");
				$_link2=($_raw_link_exp_[2]&&trim($_raw_link_exp_[2])!="")?(trim($_raw_link_exp_[2])):("");
				$_link3=($_raw_link_exp_[3]&&trim($_raw_link_exp_[3])!="")?(trim($_raw_link_exp_[3])):($_link2);
				$_link4=($_raw_link_exp_[4]&&trim($_raw_link_exp_[4])!="")?(trim($_raw_link_exp_[4])):("_self");
				$_linkarray="_linkar".$_raw_link_exp_[0];
				if(!isset($$_linkarray)){$$_linkarray=array();};
				${$_linkarray}[$_link1]=array($_link2,$_link3,$_link4);
				$_raw_link_ = preg_quote($_raw_link_, '#'); 
				$row->text = preg_replace( "#{vsig_l}".$_raw_link_."{/vsig_l}#s", '' , $row->text );
			}
		}
//links
//images
		if (preg_match_all("#{vsig}(.*?){/vsig}#s", $row->text, $matches, PREG_PATTERN_ORDER) > 0) {
			$document =& JFactory::getDocument();
			$sigcount = -1;
			$_target=$_SERVER['REQUEST_URI'];
			$vsig_cssadd="<style type='text/css'>\n";
			foreach ($matches[0] as $match) {
				$sigcount++;
				$_images_dir_ = preg_replace("/{.+?}/", "", $match);
				unset($images);
				$noimage = 0;
				// read directory
				if ($dh = opendir($mosConfig_absolute_path.$rootfolder.$_images_dir_)) {
					while (($f = readdir($dh)) !== false) {
						if((substr(strtolower($f),-3) == 'jpg') || (substr(strtolower($f),-3) == 'gif') || (substr(strtolower($f),-3) == 'png')) {
							$noimage++;
							$images[] = array('filename' => $f);
							array_multisort($images, SORT_ASC, SORT_REGULAR);
						}
					}
					closedir($dh);
				}

				if($noimage) {
					$vsig_cssadd.=".vsig_cont {width:".($_width_+10+$_space_)."px;height:".($_height_+10+$_space_)."px;}\n";
					if($_th_right_!="1"){$vsig_cssadd.=".vsig_top {width:".$_im_area_.";margin:5px 5px 5px -5px;}\n.vsig_top img {width:".$therealimagearea.";}\n";}
					else{$vsig_cssadd.=".vsig_top {width:".$_im_area_.";float:left;margin:-5px 15px 5px -5px;}\n.vsig_top img {width:".$therealimagearea.";}\n.vsig_ruler {width:".$_rulerspace_.";}\n";}
					if($_im_align_==0){$vsig_cssadd.=".vsig {margin:0 0 0 auto;padding:0;display:block;width:".$_imwidth_.";}\n";}
					elseif($_im_align_==1){$vsig_cssadd.=".vsig {margin:auto;padding:0;display:block;width:".$_imwidth_.";}\n";}
					else{$vsig_cssadd.=".vsig {width:".$_imwidth_.";}\n";}
					if($_setid_){
						$_tempstring=explode("/",$_images_dir_);
						$_tempstring=$_tempstring[count($_tempstring)-1];
						$html2 = "\n<div class='vsig' id='".$_tempstring."'>";
					}else{
						$html2 = "\n<div class='vsig'>";
					}							
					//manipulate uri
					$aktimg=0;
					$itemid = explode(":",$row->slug);
					$identifier=$itemid[0]."-".$sigcount;
					if(!empty($_GET['vsig'.$identifier])){$aktimg=$_GET['vsig'.$identifier];}
					$target[$identifier] = preg_replace('@[&|&amp;]?vsig'.$identifier.'=[0-9]+@', '', $_target );
					$vsig_adqm = strpos($target[$identifier], '?');
					$target[$identifier].=($vsig_adqm===false)?("?"):("");
					$target[$identifier].=(substr($target[$identifier], -1)!="?"&&substr($target[$identifier], -1)!="&")?("&amp;"):("");    

					//top image
					if($images[$aktimg]['filename'] != '') {
					$html2 .= "\n<div class='vsig_top'>";
					$captions="cap_ar".$sigcount;
					$vsiglinks="_linkar".$sigcount;
					if(isset($currentlink)){unset($currentlink);};
					if($_link_use_&&isset($$vsiglinks)){
						if(array_key_exists($images[$aktimg]['filename'],$$vsiglinks)){$currentlink=${$vsiglinks}[$images[$aktimg]['filename']];}
						elseif(array_key_exists("LINKDEFAULT",$$vsiglinks)){$currentlink=${$vsiglinks}["LINKDEFAULT"];}
						else{$currentlink=array("","","_self");}
					$html2 .= "\n<a href='".$currentlink[0]."' title='".$currentlink[1]."' target='".$currentlink[2]."'>";
					}
					//check for captions and use set title instead of imagename for alt and titel plus set params for js
					$topalttxt=(isset(${$captions}[$images[$aktimg]['filename']][0])&&${$captions}[$images[$aktimg]['filename']][0]!="")?(${$captions}[$images[$aktimg]['filename']][0]):(substr($images[$aktimg]['filename'], 0, -4));
					$topalttxt=(isset($currentlink)&&$currentlink[1]!="")?($currentlink[1]):($topalttxt);
					$html2 .= "\n<img id='topimg".$identifier."' src='".$mosConfig_live_site.$rootfolder.$_images_dir_."/".$images[$aktimg]["filename"]."' title='".$topalttxt."' alt='".$topalttxt."'/>";
					//caption
					if($_cap_show_&&isset($$captions)){
						if(array_key_exists($images[$aktimg]['filename'],$$captions)){$currentarray=${$captions}[$images[$aktimg]['filename']];}
						elseif(array_key_exists("CAPDEFAULT",$$captions)){$currentarray=${$captions}["CAPDEFAULT"];}
						if(isset($currentarray)){
					$html2 .= "\n<div class='".(($_cap_pos_)?'inside':'outside')."' style='width:".$therealimagearea.";'>";
					$html2 .= "<span>".$currentarray[0]."</span><span>".$currentarray[1]."</span>";
					$html2 .= "\n</div>".(($_cap_pos_)?"":"<br class='vsig_clr' />");
						unset($currentarray);
						}
					}
						if(isset($currentlink)){
					$html2 .= "\n</a>";
						}
					$html2 .= "\n</div>\n";
					if($_th_right_=="1"){$html2 .= "<div class='vsig_ruler'>";}
					}
					//thumbnails
					for($a = 0;$a<$noimage;$a++) {
						if($images[$a]['filename'] != '') {
							$baseimg=$mosConfig_absolute_path.$rootfolder.$_images_dir_."/".$images[$a]['filename'];
							$imagedata = getimagesize($baseimg);
							
							//calculate thumbnails size
							$new_tw = $_width_;
							$new_th = (int)($imagedata[1]*($new_tw/$imagedata[0]));
							if($new_th > $_height_) {
								$new_th = $_height_;
								$new_tw = (int)($imagedata[0]*($new_th/$imagedata[1]));
							}

							//set thumb_name
							$thumbname=substr_replace($images[$a]['filename'], '', -4, 4);
							$thumbtype=strtolower(substr($images[$a]['filename'], -3, 3));
							$thumbname=$thumbname."_".$new_tw."_".$new_th."_".$_quality_.".".$thumbtype;
							$thumbdir=$mosConfig_absolute_path.$rootfolder.$_images_dir_."/vsig_thumbs/";
							$thumbabs=$thumbdir.$thumbname;

							//check_existence_of/create thumbdirectory
							jimport('joomla.filesystem.file');
							jimport('joomla.filesystem.folder');
							if(!JFolder::create($thumbdir)) {
								echo "Failed creating thumbnail directory ".$thumbdir;
							}
							JFile::write($thumbdir."index.html", "<html>\n<body bgcolor=\"#FFFFFF\">\n</body>\n</html>");

							//check_existence_of/create thumb
							if(!is_file($thumbabs)){
								if($thumbtype=="jpg"){
									$image = imagecreatefromjpeg($baseimg);
									$image_dest = imagecreatetruecolor($new_tw, $new_th);
									imagecopyresampled($image_dest, $image, 0, 0, 0, 0, $new_tw, $new_th, $imagedata[0], $imagedata[1]);
									ob_start(); // start a new output buffer
								  imagejpeg($image_dest, '', $_quality_);
									$buffer = ob_get_contents();
									ob_end_clean(); // stop this output buffer
								}
								elseif($thumbtype=="gif"){
									$image = ImageCreateFromGif($baseimg);
									$image_dest = ImageCreate($new_tw, $new_th);
									ImagePaletteCopy($image_dest,$image);
									ImageCopyResized($image_dest,$image,0,0,0,0,$new_tw,$new_th,$imagedata[0],$imagedata[1]);
									ob_start(); // start a new output buffer
									Imagegif($image_dest, '', $_quality_);
									$buffer = ob_get_contents();
									ob_end_clean(); // stop this output buffer
								}
								elseif($thumbtype=="png"){
									$image = ImageCreateFromPng($baseimg);
									$image_dest = imagecreatetruecolor($new_tw, $new_th);
									ImagePaletteCopy($image_dest,$image);
									ImageCopyResized($image_dest, $image, 0, 0, 0, 0, $new_tw, $new_th, $imagedata[0], $imagedata[1]);
									if(substr(phpversion(), 0, 1)>=5){$_pngquality_=intval(($_quality_-10)/10);}
									ob_start(); // start a new output buffer
									Imagepng($image_dest, '', $_pngquality_);
									$buffer = ob_get_contents();
									ob_end_clean(); // stop this output buffer
								}
								JFile::write($thumbabs, $buffer);
								imagedestroy($image_dest);
							}
							$html2 .= '<div class="vsig_cont"><div class="vsig_thumb">';
							$html2 .= '<a href="'.$target[$identifier].'vsig'.$identifier.'='.$a.'"';
							if($_usescript_>=1){
							//check for captions and use set title instead of imagename for alt and titel plus set params for js
							$alttxt=(isset(${$captions}[$images[$a]['filename']][0])&&${$captions}[$images[$a]['filename']][0]!="")?(${$captions}[$images[$a]['filename']][0]):(substr($images[$a]['filename'], 0, -4));
							$captitle="";
							$captxt="";
							$thb_linktitle="";
							$thb_linkhref="";
							$thb_linktarget="_self";
							if($_cap_show_&&isset($$captions)){
								if(array_key_exists($images[$a]['filename'],$$captions)){$captitle=${$captions}[$images[$a]['filename']][0];$captxt=${$captions}[$images[$a]['filename']][1];}
								elseif(array_key_exists("CAPDEFAULT",$$captions)){$captitle=${$captions}["CAPDEFAULT"][0];$captxt=${$captions}["CAPDEFAULT"][1];}
							}
							if($_link_use_&&isset($$vsiglinks)){
								if(array_key_exists($images[$a]['filename'],$$vsiglinks)){$thb_linktitle=${$vsiglinks}[$images[$a]['filename']][0];$thb_linkhref=${$vsiglinks}[$images[$a]['filename']][1];$thb_linktarget=${$vsiglinks}[$images[$a]['filename']][2];}
								elseif(array_key_exists("LINKDEFAULT",$$vsiglinks)){$thb_linktitle=${$vsiglinks}["LINKDEFAULT"][0];$thb_linkhref=${$vsiglinks}["LINKDEFAULT"][1];$thb_linktarget=${$vsiglinks}["LINKDEFAULT"][2];}
							}
							$html2 .= ' onclick=\'switchimg("topimg'.$identifier.'","'.$mosConfig_live_site.$rootfolder.$_images_dir_.'/'.$images[$a]['filename'].'", "'.$alttxt.'", "'.$captitle.'", "'.$captxt.'", "'.$thb_linktitle.'", "'.$thb_linkhref.'", "'.$thb_linktarget.'");return false;\'';
							}
							$html2 .= ' title="'.$alttxt.'">';
							$html2 .= '<img src="'.$mosConfig_live_site.$rootfolder.$_images_dir_.'/vsig_thumbs/'.$thumbname.'" alt="'.$alttxt.'"/>';
							$html2 .= "</a></div></div>\n";
						}
					}
					if($_th_right_=="1"){$html2 .="</div>\n";}
					$html2 .="<div class=\"vsig_clr\"></div>\n</div>\n";
				}
				if($_usescript_==1){
					$document->addScript($mosConfig_live_site.'/plugins/content/plugin_vsig/vsig.js');
				}
				$_images_dir_ = preg_quote($_images_dir_, '#');
				$row->text = preg_replace( "#{vsig}".$_images_dir_."{/vsig}#s", $html2 , $row->text );
			}
				$vsig_cssadd.="</style>\n";
				$document->addCustomTag($vsig_cssadd);
				$document->addCustomTag('<link href="'.$mosConfig_live_site.'/plugins/content/plugin_vsig/vsig.css" rel="stylesheet" type="text/css" />' );
		}
//images
	}
}

//swich metatag robots to noindex on follow-up pages to prevent duplicate content
function switchmeta(){
	if(preg_match('@vsig[0-9]+-@', $_SERVER['REQUEST_URI'])>=1){
		$document =& JFactory::getDocument();
		$document->setMetaData("robots", "noindex, nofollow");
	}
}
?>