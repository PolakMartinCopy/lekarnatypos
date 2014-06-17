<?php  
class TinyImagesController extends AppController { 

    var $name = 'TinyImages'; 

    var $uses = array('TinyImage'); 

    var $helpers = array( 
        'Html', 
        'Form', 
        'Javascript', 
        'Number' // Used to show readable filesizes 
    ); 

    function admin_index() { 
        $this->layout = 'image_browser';
    	
    	$this->set( 
            'images', 
            $this->TinyImage->readFolder(APP.WEBROOT_DIR.DS.'files/content-images') 
        ); 
    } 

    function admin_upload() { 
        // Upload an image 
        if (!empty($this->data)) { 
            // Validate and move the file 
            if($this->TinyImage->upload($this->data)) { 
                $this->Session->setFlash('The image was successfully uploaded.'); 
            } else { 
                $this->Session->setFlash('There was an error with the uploaded file.'); 
            } 
             
            $this->redirect( 
                array( 
                    'action' => 'admin_index' 
                ) 
            ); 
        } else { 
            $this->redirect( 
                array( 
                    'action' => 'admin_index' 
                ) 
            ); 
        } 
    } 
} 
?>