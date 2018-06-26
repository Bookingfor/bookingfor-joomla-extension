<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . 'helpers/BFCHelper.php';

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$document		= JFactory::getDocument();
$language 	= JFactory::getLanguage()->getTag();
$mainframe = JFactory::getApplication();
$currentComponent = BFCHelper::getCmd( 'option' );
$currentView = BFCHelper::getCmd( 'view' );

//Load language translation from component
$lang = JFactory::getLanguage();
$lang->load('com_bookingforconnector', $pathbase, 'en-EN', true);
$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);

bfi_load_scripts();


//JHTML::stylesheet('modules/'.$module->module.'/assets/css/slick-theme.css');
//$document->addStyleSheet('//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css');
//$document->addScript('//cdn.jsdelivr.net/jquery.slick/1.6.0/slick.min.js');

JHTML::stylesheet('components/com_bookingforconnector/assets/js/slick/slick.css');
JHTML::stylesheet('components/com_bookingforconnector/assets/js/slick/slick-theme.css');
JHTML::script('components/com_bookingforconnector/assets/js/slick/slick.min.js');

require JModuleHelper::getLayoutPath('mod_bookingformerchants', $params->get('layout', 'default'));