<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'helpers/htmlHelper.php';
require_once $pathbase . 'defines.php';
require_once $pathbase . 'helpers/BFCHelper.php';
$currentComponent = BFCHelper::getCmd( 'option' );
$currentView = BFCHelper::getCmd( 'view' );
$currentlayout = BFCHelper::getString('layout','default');


if ($currentComponent != 'com_bookingforconnector') return;

//if ($currentView == 'resource' && $currentlayout == 'form' ) return;



$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));



//Load language translation from component
$lang = JFactory::getLanguage();
$lang->load('com_bookingforconnector', $pathbase, 'en-EN', true);
$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);

$document		= JFactory::getDocument();
$language 	= $document->getLanguage();
$mainframe = JFactory::getApplication();

$config = JComponentHelper::getParams('com_bookingforconnector');
$isportal = $config->get('isportal', 1);

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


	$document->addStyleSheet('modules/'.$module->module.'/assets/css/style.css');

if (is_file(JPATH_SITE.'/templates/'.$mainframe->getTemplate().'/css/'.$module->module.".css")){
	$document->addStyleSheet('templates/'.$mainframe->getTemplate().'/css/'.$module->module.".css");
}


// load scripts
	$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor.css');
	$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');
	$document->addStyleSheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

// load scripts
$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
	$document->addScript('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
	$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
	$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
	$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		
	$document->addScript('components/com_bookingforconnector/assets/js/bf.js');
	$document->addScript('components/com_bookingforconnector/assets/js/jquery.xml2json.js');

?>
