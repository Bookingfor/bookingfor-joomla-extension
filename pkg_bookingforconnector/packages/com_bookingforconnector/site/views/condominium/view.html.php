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

require_once $pathbase . '/views/condominium/resourceview.php';

/**
 * HTML View class for the HelloWorld Component
 */
class BookingForConnectorViewCondominium extends BookingForConnectorViewCondominiumBase
{
	protected $pagination = null;
	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false)
	{

		BookingForConnectorViewCondominiumBase::basedisplay($tpl);
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$isFromSearch = false;
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$params		= $this->params;

		BFCHelper::setState($item, 'condominium', 'condominium');
		$allobjects = array();
		if (BFCHelper::getString('layout') == 'resourcesajax') {
			$items = $this->get('ItemsResourcesAjax');
		}else{
		//if (BFCHelper::getString('layout') == 'resources') {
			if (BFCHelper::getString('search') == '1') {					
				$isFromSearch = true;
				$items = $this->get('ItemsSearch');
				$pagination	= $this->get('Pagination');
			} else {
				$items = $this->get('Items');
				$pagination	= $this->get('Pagination');
			}
			
			if(!empty($items)) {
				foreach ($items as $key => $value) {
					$obj = new stdClass;
					if(BFCHelper::getString('search') == '1') {
						$obj->id = "" . $value->ResourceId . " - Resource";
						$obj->name = $value->ResName;
						$obj->category = $value->DefaultLangMrcCategoryName;
						$obj->brand = $value->MrcName;
					} else {
						$obj->id = "" . $value->ResourceId . " - Resource";
						$obj->name = $value->Name;
						$obj->category = $value->MerchantCategoryName;
						$obj->brand = $value->MerchantName;
					}
					$obj->position = $key;
					$allobjects[] = $obj;
				}
			}
		}
		$analyticsEnabled = $this->checkAnalytics("Condominium page");
		if($analyticsEnabled && $config->get('eecenabled', 0) == 1) {
			$obj = new stdClass;
			$obj->id = "" . $item->CondominiumId . " - Resource Group";
			$obj->name = $item->Name;
			$obj->category = $item->MrcCategoryName;
			$obj->brand = $item->MerchantName;
			$obj->variant = 'NS';
			$document->addScriptDeclaration('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
			
			$document->addScriptDeclaration('callAnalyticsEEc("addImpression", ' . json_encode($allobjects) . ', "list", "Condominium Resources Search List");');
		}
		
//		$document->addStyleSheet('components/com_bookingforconnector/assets/css/resource.css');
		// add stylesheet
		$document->addStylesheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		$document->addStylesheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');

		$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.blockUI.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bf.js');
		
		$this->assignRef('document', $document);
		$this->assignRef('language', $language);
		$this->assignRef('item', $item);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('isFromSearch', $isFromSearch);
		$this->assignRef('sitename', $sitename);
		$this->assignRef('config', $config);
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		$this->setBreadcrumb($item, 'condominiums', $language);
		
		parent::display($tpl);
	}
}
