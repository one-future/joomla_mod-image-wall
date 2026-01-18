<?php
    /**
     * Helper class for Hello World! module
     * 
     * @package    Joomla.Tutorials
     * @subpackage Modules
     * @link http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
     * @license        GNU/GPL, see LICENSE.php
     * mod_helloworld is free software. This version may have been modified pursuant
     * to the GNU General Public License, and as distributed it includes or
     * is derivative of works licensed under the GNU General Public License or
     * other free or open source software licenses.
     */
    jimport('joomla.filesystem.file');

    class ImageGenerator
    {
        private $moduleID;

        function __construct($id) {
            $this->moduleID = $id;
        }

        /**
         * Retrieves the hello message
         *
         * @param   array  $params An object containing the module parameters
         *
         * @access public
         */    
        public function getRowCount(&$params)
        {
            $total_images = $this->getTotalImageCount($params->get('data_source'));

            if($params->get('auto_wrap_row') == 1) {
                $row_count = 0;
            } else {
                $row_count = ceil($total_images / $params->get('row_number'));
            }

            return $row_count;
        }

        public function getTotalImageCount($source) {
            $dir = JPATH_SITE . "/images/" . $source;
            $images = JFolder::files($dir, '.jpg|.png');

            if(is_array($images) and isset($images)) {
                return count($images);
            } else {
                return 0;
            }
        }

        public function appendImages(&$params) {
            $directory = "images/". $params->get('data_source');
            $images = JFolder::files(JPATH_SITE . '/' . $directory, '.jpg|.png|.svg');

            if(is_array($images) && isset($images) && file_exists($directory)) {
                $row = array();
                $result = array();
                $string_to_add = "";
                $label_def = false;

                if(file_exists($directory . "/captions.txt")) {
                    $labels = fopen(JPATH_SITE . "/" . $directory . "/captions.txt", "r");
                    if(count(file($directory . "/captions.txt")) > 0) { $label_def = true; }
                }

                if($label_def) {
                    $counter = 0;
                    if($params->get('auto_wrap_row') == 0 && $params->get('row_number') > 0) {
                        $counter = $params->get('row_number');
                    } else {
                        $counter = -1;
                    }
                    $loop_counter = $counter;

                    while(! feof($labels)) {
                        $line = fgets($labels);
                        $line_parts = explode("|", $line);
                        $cell = "";
                        $caption = "";
                        $header = "";
                        $reference = "";
                        $webp_available = false;
                        $webp_image = "";
                        $height_restr = null;
                        $IMG_MISSING = false;
                        $break = false;
                        $break_mobile = false;

                        if(isset($line_parts[0]) && file_exists($directory . "/" . $line_parts[0])) {
                            $current_image = $line_parts[0];
                            $webp_image = explode(".", $line_parts[0])[0] . ".webp";
                            if(file_exists($directory . "/" . $webp_image)) {
                                $webp_available = true;
                            }
                        } else if ($line_parts[0] == "break") {
                            $break = true;
                        } else if ($line_parts[0] == "break-mobile") {
                            $break_mobile = true;
                        } else {
                           $IMG_MISSING = true;
                           $current_image = "";
                        }

                        if(isset($line_parts[1])) {
                            $header = $line_parts[1];
                        }

                        if(isset($line_parts[2])) {
                            $caption = $line_parts[2];
                        }

                        if(isset($line_parts[3])) {
                            $reference = $line_parts[3];
                        }

                        if($params->get('height_restriction_combo')->height_radio == 1) {
                            $height_restr = $params->get('height_restriction_combo')->height_restriction;
                        }
                        
                        if(!$break and !$break_mobile) {
                            if($webp_available) {
                                $img_tag = $this->buildImageTag($directory . "/" . $webp_image, $directory . "/" . $current_image, $height_restr);
                            } else {
                                $img_tag = $this->buildImageTag($directory . "/" . $current_image, "none", $height_restr);
                            }

                            $cell = "
                            <div class=\"imgwall-cell" . "-" . $this->moduleID . "\">" . ($reference !== '' ? "<a target=\"_blank\" href=\"" . $reference . "\">" : "") . "
                                <figure data-imgtype=\"" . $img_tag[2] . "\" data-hr=\"" . $height_restr . "\" data-test=\"" . $webp_image . "\">" . 
                                    $img_tag[0] . "
                                    <figcaption class=\"imgwall-caption" . "-" . $this->moduleID . "\" style=\"max-width:" . $img_tag[1] . "\" >
                                        <h3>" . $header .
                                        "</h3>
                                        <p>" . ($IMG_MISSING ? "Image missing" : $caption) . 
                                        "</p>
                                    </figcaption>
                                </figure>". 
                                ($reference !== '' ? "</a>" : "") .
                            "</div>\n";
                        } else if ($break) {
                            $cell = "<div class=\"imgwall-break\"></div>";
                        } else {
                            $cell = "<div class=\"imgwall-mobile-break\"></div>";
                        }

                        $row[] = $cell;

                        if(!$break and !$break_mobile) {
                            $loop_counter -= 1;
                        }

                        if($loop_counter == 0) {
                            $result[] = $row;
                            $loop_counter = $counter;
                            $row = array();
                        }              
                    }

                    if(!empty($row)) {
                        $result[] = $row;
                        $row = array();
                    }
                } else {
                    if($params->get('auto_wrap_row') == 0 && $params->get('row_number') > 0) {
                        $counter = $params->get('row_number');
                    } else {
                        $counter = -1;
                    }
                    $loop_counter = $counter;

                    foreach($images as $image)
                    {
                        $cell = "";
                        $height_restr = null;

                        if($params->get('height_restriction_combo')->height_radio == 1) {
                            $height_restr = $params->get('height_restriction_combo')->height_restriction;
                        }

                        list($imgwidth, $imgheight) = getimagesize($directory . "/" . $image);
                        $maxwidth_container = (isset($height_restr) ? (($height_restr * ($imgwidth / $imgheight)) . "px;") : ("none;"));

                        $cell = "
                        <div class=\"imgwall-cell" . "-" . $this->moduleID . "\" >
                            <figure>
                                <img src=" . $directory . "/" . $image . " style=\"max-width: " . $maxwidth_container . "\" loading=\"lazy\">
                            </figure>
                        </div>\n";

                        $row[] = $cell;

                        $loop_counter -= 1;

                        if($loop_counter == 0) {
                            $result[] = $row;
                            $loop_counter = $counter;
                            $row = array();
                        }
                    }

                    if($string_to_add !== "") {
                        $result[] = $row;
                        $row = array();
                    }
                }
            } else {
                $result[] = "Error: Couldn't find directory or directory does not contain any comaptible images.";
            }
            if(isset($labels)) {
                fclose($labels);
            }
            return $result;
        }

        private function buildImageTag($src, $fallback, $height_restr) {
            $result = "";
            $filetype = pathinfo($src)['extension'];
            $maxwidth = "none;";

            if($filetype == "svg") {
                $result = file_get_contents($src);

            } else if ($filetype == "webp") {
                list($imgwidth, $imgheight) = getimagesize($src);
                $maxwidth = (isset($height_restr) ? (($height_restr * ($imgwidth / $imgheight)) . "px;") : ("none;"));

                $result = "<img class=\"webp\" src=" . $src . " data-fallback=\"" . $fallback . "\" style=\"max-width: " . $maxwidth . "\" loading=\"lazy\" >";

            } else {
                list($imgwidth, $imgheight) = getimagesize($src);
                $maxwidth = (isset($height_restr) ? (($height_restr * ($imgwidth / $imgheight)) . "px;") : ("none;"));

                $result = "<img src=" . $src . " style=\"max-width: " . $maxwidth . "\" loading=\"lazy\" >";
            }

            return array($result, $maxwidth, $filetype);
        }
    }
?>