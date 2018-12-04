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

if(!empty( COM_BOOKINGFORCONNECTOR_CRAWLER )){
	$listCrawler = json_decode(COM_BOOKINGFORCONNECTOR_CRAWLER , true);
	foreach( $listCrawler as $key=>$crawler){
	if (preg_match('/'.$crawler['pattern'].'/', $_SERVER['HTTP_USER_AGENT'])) return;
	}
	
}

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$config = JComponentHelper::getParams('com_bookingforconnector');
$XGooglePosDef = htmlspecialchars($config->get('posx', 0));
$YGooglePosDef = htmlspecialchars($config->get('posy', 0));
$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');


$document		= JFactory::getDocument();
$language 	= JFactory::getLanguage()->getTag();
$mainframe = JFactory::getApplication();

		  $instance['tablistSelected'] =  $params->get('tablistSelected');
		  $instance['blockmonths'] = $params->get('blockmonths');
		  $instance['blockdays'] = $params->get('blockdays');
		  $instance['onlystay'] = $params->get('onlystay');
		  
		  $instance['tabnamebooking'] = $params->get('tabnamebooking');
		  $instance['tabnameservices'] = $params->get('tabnameservices');
		  $instance['tabnameactivities'] = $params->get('tabnameactivities');
		  $instance['tabnameothers'] = $params->get('tabnameothers');
		  $instance['tabnamecatalog'] = $params->get('tabnamecatalog');  
		  
		  $instance['tabiconbooking'] = $params->get('tabiconbooking');
		  $instance['tabiconservices'] = $params->get('tabiconservices');
		  $instance['tabiconactivities'] = $params->get('tabiconactivities');
		  $instance['tabiconothers'] = $params->get('tabiconothers');
		  $instance['tabiconrealestate'] = $params->get('tabiconrealestate');
		  $instance['tabiconcatalog'] = $params->get('tabiconcatalog');

		  $instance['merchantcategoriesbooking'] = $params->get('merchantcategoriesbooking');
		  $instance['merchantcategoriesservices'] = $params->get('merchantcategoriesservices');
		  $instance['merchantcategoriesactivities'] = $params->get('merchantcategoriesactivities');
		  $instance['merchantcategoriesothers'] = $params->get('merchantcategoriesothers');
		  $instance['merchantcategoriesrealestate'] = $params->get('merchantcategoriesrealestate');
		  
		  $instance['unitcategoriesbooking'] = $params->get('unitcategoriesbooking');
		  $instance['unitcategoriesservices'] = $params->get('unitcategoriesservices');
		  $instance['unitcategoriesactivities'] = $params->get('unitcategoriesactivities');
		  $instance['unitcategoriesothers'] = $params->get('unitcategoriesothers');
		  $instance['unitcategoriesrealestate'] = $params->get('unitcategoriesrealestate');

		  $instance['availabilitytypesbooking'] = $params->get('availabilitytypesbooking');
		  $instance['availabilitytypesservices'] = $params->get('availabilitytypesservices');
		  $instance['availabilitytypesactivities'] = $params->get('availabilitytypesactivities');
		  $instance['availabilitytypesothers'] = $params->get('availabilitytypesothers');

		  $instance['itemtypesbooking'] = $params->get('itemtypesbooking');
		  $instance['itemtypesservices'] = $params->get('itemtypesservices');
		  $instance['itemtypesactivities'] = $params->get('itemtypesactivities');
		  $instance['itemtypesothers'] = $params->get('itemtypesothers');

		  $instance['groupbybooking'] = $params->get('groupbybooking');
		  $instance['groupbyservices'] = $params->get('groupbyservices');
		  $instance['groupbyactivities'] = $params->get('groupbyactivities');
		  $instance['groupbyothers'] = $params->get('groupbyothers');

		  $instance['showdirection'] = $params->get('showdirection');
		  $instance['fixedontop'] = $params->get('fixedontop');
		  $instance['fixedontopcorrection'] = $params->get('fixedontopcorrection');
		  $instance['fixedonbottom'] = $params->get('fixedonbottom');

		  $instance['showLocation'] = $params->get('showLocation');
		  $instance['showMapIcon'] = $params->get('showMapIcon');
		  $instance['showSearchText'] = $params->get('showSearchText');
		  $instance['showAccomodations'] = $params->get('showAccomodations');
		  $instance['showDateRange'] = $params->get('showDateRange');
		  $instance['showAdult'] = $params->get('showAdult');
		  $instance['showChildren'] = $params->get('showChildren');
		  $instance['showSenior'] = $params->get('showSenior');
		  $instance['showServices'] = $params->get('showServices');
		  $instance['showOnlineBooking'] = $params->get('showOnlineBooking');
		  $instance['showMaxPrice'] = $params->get('showMaxPrice');
		  $instance['showMinFloor'] = $params->get('showMinFloor');
		  $instance['showContract'] = $params->get('showContract');
		  
		  $instance['showResource'] = $params->get('showResource');

		  $instance['showSearchTextOnSell'] = $params->get('showSearchTextOnSell');
		  $instance['showMapIconOnSell'] = $params->get('showMapIconOnSell');
		  $instance['showAccomodationsOnSell'] = $params->get('showAccomodationsOnSell');
		  $instance['showBedRooms'] = $params->get('showBedRooms');
		  $instance['showRooms'] = $params->get('showRooms');
		  $instance['showBaths'] = $params->get('showBaths');
		  $instance['showOnlyNew'] = $params->get('showOnlyNew');
		  $instance['showServicesList'] = $params->get('showServicesList');


//Load language translation from component
$lang = JFactory::getLanguage();
$lang->load('com_bookingforconnector', $pathbase, 'en-GB', true);
$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);

bfi_load_scripts();
//-----------------------------------------------------------------------------------------
//	implementazione css 
//	per uno style personalizzato usare il seguente file css nella cartella css del template
//	"mod_bookingforsearchbymerchanttype.css"
//-----------------------------------------------------------------------------------------
//JHtml::_('jquery.framework');
//JHTML::script('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
//
//JHTML::stylesheet('components/com_bookingforconnector/assets/css/jquery.validate.css');
//JHTML::stylesheet('components/com_bookingforconnector/assets/jquery-ui/themes/smoothness/jquery-ui.min.css');
//JHTML::stylesheet('components/com_bookingforconnector/assets/css/font-awesome.min.css');
//JHTML::stylesheet('components/com_bookingforconnector/assets/css/magnific-popup.css');
//JHTML::stylesheet('components/com_bookingforconnector/assets/js/webui-popover/jquery.webui-popover.min.css');
//JHTML::stylesheet('components/com_bookingforconnector/assets/css/bookingfor.css');
//
//JHTML::script('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
//JHTML::script('components/com_bookingforconnector/assets/js/jquery.form.js');
//JHtml::script('components/com_bookingforconnector/assets/js/jquery-validation/jquery.validate.min.js');
//JHtml::script('components/com_bookingforconnector/assets/js/jquery-validation/additional-methods.min.js');
//JHtml::script('components/com_bookingforconnector/assets/js/jquery.validate.additional-custom-methods.js');
//JHtml::script('components/com_bookingforconnector/assets/js/jquery.magnific-popup.min.js');
//JHtml::script('components/com_bookingforconnector/assets/js/webui-popover/jquery.webui-popover.min.js');
//JHtml::script('components/com_bookingforconnector/assets/js/jquery.shorten.js');
//JHtml::script('components/com_bookingforconnector/assets/js/bfi.js');
//JHtml::script('components/com_bookingforconnector/assets/js/bfisearchonmap.js');
//JHtml::script('components/com_bookingforconnector/assets/js/bfi_calendar.js');
//if(substr($language,0,2)!='en'){
//	JHtml::script('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/i18n/datepicker-' . substr($language,0,2) . '.min.js?ver=1.11.4');
//}


// if($params->get("type")=='multi'){
// 	require JModuleHelper::getLayoutPath('mod_bookingfor', $params->get('layout', 'multi'));
// }elseif($params->get("type")=='mono'){
// 	require JModuleHelper::getLayoutPath('mod_bookingfor', $params->get('layout', 'mono'));
// }else{
//	require JModuleHelper::getLayoutPath('mod_bookingfor', $params->get('layout', 'default'));
	require JModuleHelper::getLayoutPath('mod_bookingforsearch');
//}

