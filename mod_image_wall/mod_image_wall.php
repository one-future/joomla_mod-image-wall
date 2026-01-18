<?php
/**
 * Hello World! Module Entry Point
 * 
 * @package    Joomla.Tutorials
 * @subpackage Modules
 * @license    GNU/GPL, see LICENSE.php
 * @link       http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
 * mod_helloworld is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$document = Factory::getApplication()->getDocument();

$wa = $document->getWebAssetManager();
$wa->useScript('jquery');

$wa->registerAndUseScript(
    'mod_image_wall.script',
    'modules/mod_image_wall/media/js/domhelper.js',
    ['jquery'],
    ['defer' => true]
);

$wa->registerAndUseStyle(
    'mod_image_wall.style',
    'modules/mod_image_wall/media/css/main.css'
);

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$img_generator = new ImageGenerator($module->id);
//$row_count = ImageGenerator::getRowCount($params);
$image_paths = $img_generator->appendImages($params);

require JModuleHelper::getLayoutPath('mod_image_wall');