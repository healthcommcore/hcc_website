<?php

/**
* abstract parent class for xml templates
* @package RSGallery2
* @author Jonah Braun <Jonah@WhaleHosting.ca>
*/
class rsgXmlGalleryTemplate_generic{
    var $gallery;
    var $output;

    /**
        class constructor
        @param rsgGallery object
    **/
    function rsgXmlGalleryTemplate_generic( $gallery ){
        $this->gallery = $gallery;
    }

    function getName(){
        return 'generic xml template';
    }
    
    /**
        Prepare XML first.  Then if there are errors we print an error before changing Content-Type to xml.
    **/
    function prepare(){
        $this->output = '';
        $this->output .= '<gallery name="'. $this->gallery->name .'">';
        
        foreach( $this->gallery->itemRows() as $img ){
            $this->output .= '  <image name="'. $img['name'] .'" />'."\n";
        }
        
        $this->output .= '</gallery>';
    }
    
    /**
        print xml headers
    **/
    function printHead(){
        header('Content-Type: application/xml');
        echo '<?xml version="1.0" encoding="iso-8859-1"?>';
    }
    
    /**
        outputs XML
    **/
    function printGallery(){
        echo $this->output;
    }
}
?>
