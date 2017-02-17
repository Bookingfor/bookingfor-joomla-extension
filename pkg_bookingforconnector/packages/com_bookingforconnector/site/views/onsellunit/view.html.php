<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . '/views/onsellunit/resourceview.php';

/**
 * HTML View class for the HelloWorld Component
 */
class BookingForConnectorViewOnSellUnit extends BookingForConnectorViewOnSellUnitBase
{
	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false)
	{

		BookingForConnectorViewOnSellUnitBase::basedisplay($tpl);
		$item		= $this->get('Item');
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$config = JComponentHelper::getParams('com_bookingforconnector');

		BFCHelper::setState($item, 'onsellunit', 'onsellunit');
		
//		$document->addStyleSheet('components/com_bookingforconnector/assets/css/resource.css');
		// add stylesheet
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');
		$document->addStyleSheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');

		// load scripts
		$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
		
		$document->addScript('//ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/jquery.validate.min.js');
		$document->addScript('//ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/additional-methods.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bf.js');
		
		if(substr($language,0,2)!='en'){
			$document->addScript('//ajax.aspnetcdn.com/ajax/jquery.validate/1.15.0/localization/messages_' . substr($language,0,2) . '.js');
		}

		if($this->checkAnalytics("Sales Resources Page") && $config->get('eecenabled', 0) == 1) {
			$obj = new stdClass;
			$obj->id = "" . $item->ResourceId . " - Sales Resource";
			$obj->name = $item->Name;
			$obj->category = $item->MerchantCategoryName;
			$obj->brand = $item->MerchantName;
			$obj->variant = 'NS';
			$document->addScriptDeclaration('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
		}
		
		$this->assignRef('document', $document);
		$this->assignRef('language', $language);
		$this->assignRef('item', $item);
		$this->assignRef('config', $config);
//		$item		= $this->get('Item');
		//$item->Name = $item->nome;
		//$this->setBreadcrumb($item, 'resources');
		$this->setBreadcrumb($item, 'onsellunits', $language);
		
		parent::display($tpl, true);
	}
}
