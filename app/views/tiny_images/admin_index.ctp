<?php 
    /*echo $javascript->codeBlock( 
        "function selectURL(url) { 
            if (url == '') return false; 

            url = '".Helper::url('/files/content-images/')."' + url;

            field = window.top.opener.browserWin.document.forms[0].elements[window.top.opener.browserField];

			alert(window.top.opener.browserWin.document.forms[0].elements[window.top.opener.browserField]);
                        
            field.value = url; 
            if (field.onchange != null) field.onchange(); 
            window.top.close(); 
            window.top.opener.browserWin.focus(); 
        }" 
    );*/ 
?> 

<?php 
    echo $form->create( 
        null, 
        array( 
            'type' => 'file', 
            'url' => array( 
                'action' => 'upload' 
            ) 
        ) 
    ); 
    echo $form->label( 
        'TinyImage.image', 
        'Upload image' 
    ); 
    echo $form->file( 
        'TinyImage.image' 
    );     
    echo $form->end('Upload'); 
?> 

<?php if(isset($images[0])) { 
    $tableCells = array(); 

    foreach($images As $the_image) { 
        $tableCells[] = array( 
            $html->link( 
                $the_image['basename'], 
                '#', 
                array(
                	'escape' => false,
                    'onclick' => 'top.tinymce.activeEditor.windowManager.getParams().oninsert(\'/files/content-images/' . $the_image['basename'] . '\'); top.tinymce.activeEditor.windowManager.close();; return false;' 
                ) 
            ), 
            $number->toReadableSize($the_image['size']), 
            date('m/d/Y H:i', $the_image['last_changed']) 
        ); 
    } 

    echo $html->tag( 
        'table', 
        $html->tableHeaders( 
            array( 
                'File name', 
                'Size', 
                'Date created' 
            ) 
        ).$html->tableCells( 
            $tableCells 
        ) 
    ); 
} ?> 