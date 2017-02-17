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

$config = JComponentHelper::getParams('com_bookingforconnector');
$XGooglePosDef = htmlspecialchars($config->get('posx', 0));;
$YGooglePosDef = htmlspecialchars($config->get('posy', 0));
$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');


$document		= JFactory::getDocument();
$language 	= $document->getLanguage();
$mainframe = JFactory::getApplication();
$currentComponent = JRequest::getCmd( 'option' );
$currentView = JRequest::getCmd( 'view' );

$instance['tablistSelected'] = $params->get('tablistSelected');
$instance['groupbycondominium'] = $params->get('groupbycondominium');
$instance['showdirection'] = $params->get('show_direction');
$instance['showLocation'] = $params->get('showLocation');
$instance['showMapIcon'] = $params->get('showMapIcon');
$instance['showAccomodations'] = $params->get('showAccomodations');
$instance['showDateRange'] = $params->get('showDateRange');
$instance['showNightSelector'] = $params->get('showNightSelector');
$instance['showAdult'] = $params->get('showAdult');
$instance['showChildren'] = $params->get('showChildren');
$instance['showSenior'] = $params->get('showSenior');
$instance['showServices'] = $params->get('showServices');
$instance['showOnlineBooking'] = $params->get('showOnlineBooking');
$instance['showMaxPrice'] = $params->get('showMaxPrice');
$instance['showMinFloor'] = $params->get('showMinFloor');
$instance['showContract'] = $params->get('showContract');
$instance['showBedRooms'] = $params->get('showBedRooms');
$instance['showRooms'] = $params->get('showRooms');
$instance['showBaths'] = $params->get('showBaths');
$instance['showOnlyNew'] = $params->get('showOnlyNew');
$instance['showServicesList'] = $params->get('showServicesList');
$instance['merchantcategoriesbooking'] = $params->get('merchantcategoriesbooking');
$instance['merchantcategoriesservices'] = $params->get('merchantcategoriesservices');
$instance['merchantcategoriesactivities'] = $params->get('merchantcategoriesactivities');
$instance['merchantcategoriesrealestate'] = $params->get('merchantcategoriesrealestate');
$instance['unitcategoriesbooking'] = $params->get('unitcategoriesbooking');
$instance['unitcategoriesservices'] = $params->get('unitcategoriesservices');
$instance['unitcategoriesactivities'] = $params->get('unitcategoriesactivities');
$instance['unitcategoriesrealestate'] = $params->get('unitcategoriesrealestate');
$instance['blockmonths'] = $params->get('blockmonths');
$instance['blockdays'] = $params->get('blockdays');



//Load language translation from component
$lang = JFactory::getLanguage();
$lang->load('com_bookingforconnector', $pathbase, 'en-GB', true);
$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);

//-----------------------------------------------------------------------------------------
//	implementazione css 
//	per uno style personalizzato usare il seguente file css nella cartella css del template
//	"mod_bookingforsearchbymerchanttype.css"
//-----------------------------------------------------------------------------------------

JHTML::stylesheet('modules/'.$module->module.'/assets/style.css');

if (is_file(JPATH_SITE.'/templates/'.$mainframe->getTemplate().'/css/'.$module->module.".css")){
	JHTML::stylesheet('templates/'.$mainframe->getTemplate().'/css/'.$module->module.".css");
}
$document->addStyleSheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
$document->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');
$document->addScript('modules/mod_bookingfor/assets/bfsearchonsellunits.js');
JHTML::script('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');
$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.additional-custom-methods.js');
$document->addScript('components/com_bookingforconnector/assets/js/bfi.js');
if(substr($language,0,2)!='en'){
//	$document->addScript('//jquery-ui.googlecode.com/svn/tags/legacy/ui/i18n/ui.datepicker-' . substr($language,0,2) . '.js');
	$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/i18n/datepicker-' . substr($language,0,2) . '.min.js?ver=1.11.4');
}
$document->addScript('components/com_bookingforconnector/assets/js/calendar.js');

JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor.css');


// if($params->get("type")=='multi'){
// 	require JModuleHelper::getLayoutPath('mod_bookingfor', $params->get('layout', 'multi'));
// }elseif($params->get("type")=='mono'){
// 	require JModuleHelper::getLayoutPath('mod_bookingfor', $params->get('layout', 'mono'));
// }else{
	require JModuleHelper::getLayoutPath('mod_bookingfor', $params->get('layout', 'default'));
//}

