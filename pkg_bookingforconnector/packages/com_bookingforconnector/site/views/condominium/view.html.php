<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Bookingforconnector Component
 */
class BookingForConnectorViewCondominium extends BFCView
{
//	protected $pagination = null;
	// Overwriting JView display method
	function display($tpl = null)
	{

		// Initialise variables
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$params 	= $state->params;
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$config = JComponentHelper::getParams('com_bookingforconnector');
 		$orderid = 0;
		$cartType = 1; //$merchant->CartType;
		
		$items=null;
		$pagination=null;
		$checkAnalytics = false;
		$isFromSearch = false;

		$document->addScript('components/com_bookingforconnector/assets/js/bf_cart_type_1.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bf_appTimePeriod.js');
		$document->addScript('components/com_bookingforconnector/assets/js/bf_appTimeSlot.js');

		$allobjects = array();
		//if (BFCHelper::getString('layout') == 'resources') {
		if (BFCHelper::getString('fromsearch') == '1') {					
			$isFromSearch = true;
//			$items = $this->get('ItemsSearch');
//			$pagination	= $this->get('Pagination');
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
//					$obj->category = $value->DefaultLangMrcCategoryName;
					$obj->brand = $value->MrcName;
				} else {
					$obj->id = "" . $value->ResourceId . " - Resource";
					$obj->name = $value->Name;
//					$obj->category = $value->MerchantCategoryName;
					$obj->brand = $value->MerchantName;
				}
				$obj->position = $key;
				$allobjects[] = $obj;
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
				
		$this->document = $document;
		$this->language = $language;
		$this->params = $params;
		$this->item = $item;
		$this->items = $items;
		$this->pagination = $pagination;
		$this->sitename = $sitename;
		$this->config = $config;
		$this->state = $state;
		$this->isFromSearch = $isFromSearch;
		$this->analyticsEnabled = $analyticsEnabled;

		$this->setBreadcrumb($item, 'condominiums', $language);
		
		parent::display($tpl);
	}
	function setBreadcrumb($resource, $layout = '', $language) {
		if (!empty($resource)){
				$mainframe = JFactory::getApplication();
				$pathway   = $mainframe->getPathway();
				// resetto il pathway				
//				$pathway->setPathway(null);
				$count = count($pathway);
				$newPathway = array();
				if($count>1){
					$newPathway = array_pop($pathway);
				}
				$pathway->setPathway($newPathway);

				$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

				$pathway->addItem(
					$resourceName,
					JRoute::_('index.php?option=com_bookingforconnector&view=condominium&resourceId=' . $resource->CondominiumId . ':' . BFCHelper::getSlug($resourceName))
				);
		}
	}
}
