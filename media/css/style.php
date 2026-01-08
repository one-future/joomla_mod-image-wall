<?php 

    $css_string = $params->get('caption_css');

    if(isset($css_string)) {
        echo ".imgwall-caption p {"
            . $css_string .
        "}";
    }

?>