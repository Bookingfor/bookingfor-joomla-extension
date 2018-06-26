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

$currentComponent = BFCHelper::getCmd( 'option' );
$currentView = BFCHelper::getCmd( 'view' );
$currentlayout = BFCHelper::getString('layout','default');

if ($currentComponent != 'com_bookingforconnector') return;

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));


//Load language translation from component
$lang = JFactory::getLanguage();
$lang->load('com_bookingforconnector', $pathbase, 'en-EN', true);
$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);

$document		= JFactory::getDocument();
$language 	= JFactory::getLanguage()->getTag();

$config = JComponentHelper::getParams('com_bookingforconnector');
$isportal = $config->get('isportal', 1);

bfi_load_scripts();

$orderType = "a";
$task = "sendContact";
switch (strtolower($currentView)) {
	case 'onsellunit':
		$resource = BFCHelper::getOnSellUnit();
		$orderType = "b";
		$task = "sendOnSellrequest";
		if ($resource != null) {
			$merchant=BFCHelper::getMerchantFromServicebyId($resource->MerchantId);
			require JModuleHelper::getLayoutPath('mod_bookingforconnector');
		}
		break;
	case 'resource':
		$resource = BFCHelper::getResource();
		$orderType = "c";
		$task = "sendInforequest";
		if ($resource != null) {
			$merchant=BFCHelper::getMerchantFromServicebyId($resource->MerchantId);
			require JModuleHelper::getLayoutPath('mod_bookingforconnector');
		}
		break;
	case 'merchantdetails':
		$merchant = BFCHelper::getMerchant();
		if ($merchant != null) {
			require JModuleHelper::getLayoutPath('mod_bookingforconnector');
		}
		break;
	default:
		return;
		break;
}



?>
