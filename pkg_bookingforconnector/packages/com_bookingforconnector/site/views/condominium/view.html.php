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
		$language 	= JFactory::getLanguage()->getTag();
		$params 	= $state->params;
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$config = JComponentHelper::getParams('com_bookingforconnector');
 		$orderid = 0;
		
		$items=null;
		$pagination=null;
		$checkAnalytics = false;
		$isFromSearch = false;

		$document->addScript('components/com_bookingforconnector/assets/js/bf_cart.js');
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
		
		$listNameAnalytics =2;
		$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";

		$analyticsEnabled = $this->checkAnalytics($listName);
		if($analyticsEnabled && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			$obj = new stdClass;
			$obj->id = "" . $item->CondominiumId . " - Resource Group";
			$obj->name = $item->Name;
			$obj->category = $item->MrcCategoryName;
			$obj->brand = $item->MerchantName;
			$obj->variant = 'NS';
			$document->addScriptDeclaration('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
			
			$document->addScriptDeclaration('callAnalyticsEEc("addImpression", ' . json_encode($allobjects) . ', "list", "'.$listName.'");');
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
		$this->listNameAnalytics = $listNameAnalytics;

		$this->setBreadcrumb($item, 'condominiums', $language);
		
		parent::display($tpl);
	}
	function setBreadcrumb($resource, $layout = '', $language) {
		if (!empty($resource)){
				$mainframe = JFactory::getApplication();
				$pathway   = $mainframe->getPathway();
				$items   = $pathway->getPathWay();
				$count = count($items);
				$newPathway = array();
				if($count>1){
					$newPathway = array_pop($items);
				}
				$pathway->setPathway($newPathway);

				$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 

				$pathway->addItem(
					$resource->MerchantName,
					JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $resource->MerchantId . ':' . BFCHelper::getSlug($resource->MerchantName))
				);
				$pathway->addItem(
					$resourceName,
					JRoute::_('index.php?option=com_bookingforconnector&view=condominium&resourceId=' . $resource->CondominiumId . ':' . BFCHelper::getSlug($resourceName))
				);
		}
	}
}
