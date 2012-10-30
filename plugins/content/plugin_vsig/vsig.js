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

//dom
function vsig_dom(obj){return document.getElementById(obj);}
//switch without reload
function switchimg(t_gal,t_img,t_title,t_captitle,t_captxt,t_lihref,t_litxt,t_litrg) {
	//switch caption
	var t_cap=vsig_dom(t_gal).parentNode.getElementsByTagName("div");
	if(t_cap.length>=1){
		var t_ctitle=(t_captitle)?t_captitle:"";
		var t_txt=(t_captxt)?t_captxt:"";
		t_cap[0].innerHTML=(t_ctitle!=""||t_txt!="")?("<span>"+t_ctitle+"</span><span>"+t_txt+"</span>"):"";
	}
	//switch link
	if(typeof($(t_gal).parentNode.href)!="undefined"){
		t_title=(t_litxt!="")?t_litxt:t_title;
		vsig_dom(t_gal).parentNode.href=t_lihref;
		vsig_dom(t_gal).parentNode.title=t_litxt;
		vsig_dom(t_gal).parentNode.target=t_litrg;
		}
	//switch image
	vsig_dom(t_gal).src=t_img;
	vsig_dom(t_gal).alt=t_title;
	vsig_dom(t_gal).title=t_title;
}