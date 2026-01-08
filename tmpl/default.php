<?php 
    // No direct access
    defined('_JEXEC') or die; 

    $moduleID = $module->id;
    $mobileEnabled = $params->get('mobile_opt') ? $params->get('mobile_opt') : 0;
    $mobileCells = ($mobileEnabled == 1) ? $params->get('mobile_cells_per_row') : 0;

    $document = JFactory::getDocument();
    JHtml::_('jquery.framework');

    //$document->addScript(JUri::base() . 'media/mod_image_wall/js/domhelper.js');
    $document->addStyleSheet(JUri::base() .  'media/mod_image_wall/css/main.css');

    $justify_content = array("initial", "flex-start", "flex-end", "center", "space-around", "space-between", "space-evenly");
    
    $header_style = ".imgwall-caption" . "-" . $moduleID . " h3 {" . $params->get('header_css') . "}";
    $caption_style = ".imgwall-caption" . "-" . $moduleID . " p {" . $params->get('caption_css') . "}";
    $document->addStyleDeclaration($header_style);
    $document->addStyleDeclaration($caption_style);

    $general_style = 

        "
        .imgwall-row" . "-" . $moduleID . " {
          flex-wrap:" . ($params->get('auto_wrap_row') == 1 ? "wrap" : "nowrap") . ";
          justify-content: " . $justify_content[$params->get('image_spacing')] . ";
        }

        .imgwall-cell" . "-" . $moduleID . " {
            margin: " . ($params->get('inner_margin') > 0 ? "0px " . $params->get('inner_margin') . "px" : "0px") . ";
            margin-bottom: " . ($params->get('row_margin') > 0 ?  $params->get('row_margin') . "px" : "0px") . ";
            background-color: " . ($params->get('background_color') ? $params->get('background_color') : "rgba(0, 0, 0, 0)") . ";
        }

        .imgwall-cell" . "-" . $moduleID . " figure {
          padding: " . ($params->get('padding') > 0 ?  $params->get('padding') . "px" : "0px") . ";
        }" . 

        ($params->get('auto_wrap_row') == 0 ? 

            ".imgwall-cell-" . $moduleID . ":last-child {
                margin-right: 0px;
            }
            .imgwall-cell-" . $moduleID . ":first-child {
                margin-left: 0px;
            }" 

        : "") . 

        ($mobileEnabled > 0 ? 

            "@media (max-width: 767px) {
                .imgwall-responsive-" . $moduleID . " {
                    display: flex;
                }

                .imgwall-default-" . $moduleID . " {
                    display: none;
                }
            }"
        : "")
    ;

    $document->addStyleDeclaration($general_style);
?>
<div class="imgwall-wrapper-<?php echo $moduleID ?>" <?php echo "data-mid=\"" . $moduleID . "\""?>>
    <?php
        foreach($image_paths as $row) {
            $row_as_string = "";
            foreach($row as $cell_string) {
                $row_as_string .= $cell_string;
            }
            echo "<div class=\"imgwall-row" . "-" . $moduleID . " imgwall-default" . "-" . $moduleID . "\">" . $row_as_string . "</div>";
        }
        if($mobileEnabled == 1) {
            //$cells_per_row = ceil(count($image_paths[0]) / $mobileRowMax);
            $loopCount = $mobileCells;
            $row_string = "";

            foreach($image_paths as $row2) {
                foreach($row2 as $cell_string2) {

                    $row_string .= $cell_string2;

                    $loopCount -= 1;

                    if($loopCount == 0) {
                        echo "<div class=\"imgwall-row" . "-" . $moduleID . " imgwall-responsive" . "-" . $moduleID . "\">" . $row_string . "</div>";
                        $row_string = "";
                        $loopCount = $mobileCells;
                    }
                }
            }

            if($row_string !== "") {
                echo "<div class=\"imgwall-row" . "-" . $moduleID . " imgwall-responsive" . "-" . $moduleID . "\">" . $row_string . "</div>";
                $row_string = "";
            }
        }
    ?>
</div>
<script defer type="text/javascript" src="<?php echo JUri::base()?>/media/mod_image_wall/js/domhelper.js"></script>