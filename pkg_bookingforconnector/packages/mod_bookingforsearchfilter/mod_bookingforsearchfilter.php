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
//if(BFCHelper::getVar( 'view')!=="search") return ;
if(BFCHelper::getVar( 'view')!=="merchants" && BFCHelper::getVar( 'view')!=="search") return ;


$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$document		= JFactory::getDocument();
$language 	= $document->getLanguage();
$mainframe = JFactory::getApplication();
$db   = JFactory::getDBO();

//Load language translation from component
$lang = JFactory::getLanguage();
$lang->load('com_bookingforconnector', $pathbase, 'en-EN', true);
$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);

bfi_load_scripts();
$currLayout = 'default';
if(BFCHelper::getVar( 'view')=="merchants"){
	$currLayout = 'defaultmerchants';
}
require JModuleHelper::getLayoutPath('mod_bookingforsearchfilter', $params->get('layout', $currLayout));
