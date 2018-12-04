<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class BookingForConnectorViewTag extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $params = null;
	protected $language = null;
	protected $actionmode = null;

		
	// Overwriting JView display method
	function display($tpl = NULL)
	{

		// Initialise variables
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$params 	= $state->params;
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');

		$document 	= JFactory::getDocument();
		$language 	= JFactory::getLanguage()->getTag();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		
		$category = 0;
		$showGrouped = 0;
		$list = "";
		$listNameAnalytics = 0;
		$totalItems = array();
		$items = array();
		$sendData = true;

		if(!empty($item)) {
			$category = $item->SelectionCategory;
			$showGrouped  = BFCHelper::getVar('show_grouped',0);
			if ($category  == 1) {
				$items = $this->get('ItemsMerchants');
				$pagination	= $this->get('Pagination');
//				$list = " Merchants";
				$listNameAnalytics = 1;
//				if (!empty($items)) {
//					foreach($items as $itemkey => $itemValue) {
//						$objRes = new stdClass();
//						$objRes->Id = $itemValue->ResourceId . " - Resource";
//						$objRes->MerchantId = $itemValue->MerchantId;
//						$objRes->Name = $itemValue->ResName;
//						$objRes->MrcName = $itemValue->MrcName;
//						$objRes->MrcCategoryName = $itemValue->MrcCategoryName;
//						$objRes->Position = $itemkey;// $resIndex;
//						$totalItems[] = $objRes;
//					}
//				}
			}
			if ($category == 2) {
				$listNameAnalytics = 7;
				$items = $this->get('ItemsOnSellUnit');
				$pagination	= $this->get('Pagination');
//				$list = " OnSellUnits";
			}
			if ($category == 4) {
				$listNameAnalytics = 5;
				$items = $this->get('ItemsResources');
				$pagination	= $this->get('Pagination');
				$showGrouped  = $params['show_grouped'];
//				$list = " Resources";
				if (!empty($items)) {
					foreach($items as $mrckey => $mrcValue) {
						$obj = new stdClass();
						$obj->Id = $mrcValue->ResourceId . " - Resource";
						$obj->MerchantId = $mrcValue->MerchantId;
						$obj->MrcCategoryName = $mrcValue->DefaultLangMrcCategoryName;
						$obj->Name = $mrcValue->ResName;
						$obj->MrcName = $mrcValue->MrcName;
						$obj->Position = $mrckey;
						$totalItems[] = $obj;
					}
				    
				}
			}

			$this->items = $items;
			$this->pagination = $pagination;
			$this->totalAvailable =  $this->get('TotalAvailable');;

			$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
			$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
			$pagination->setAdditionalUrlParam("newsearch",0);
		}

		$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];// "Resources Search List";
		$analyticsEnabled = count($items) > 0 && $this->checkAnalytics($listName) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;
		if(count($totalItems) > 0 && COM_BOOKINGFORCONNECTOR_GAENABLED == 1 && !empty(COM_BOOKINGFORCONNECTOR_GAACCOUNT) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {

			$allobjects = array();
			$initobjects = array();
			foreach ($totalItems as $key => $value) {
				$obj = new stdClass;
				$obj->id = "" . $value->Id;
				if(isset($value->GroupId) && !empty($value->GroupId)) {
					$obj->groupid = $value->GroupId;
				}
				$obj->name = $value->Name;
				$obj->category = $value->MrcCategoryName;
				$obj->brand = $value->MrcName;
				$obj->position = $value->Position;
				if(!isset($value->ExcludeInitial) || !$value->ExcludeInitial) {
					$initobjects[] = $obj;
				} else {
					///$obj->merchantid = $value->MerchantId;
					//$allobjects[] = $obj;
				}
			}
			$document->addScriptDeclaration('var currentResources = ' .json_encode($allobjects) . ';
			var initResources = ' .json_encode($initobjects) . ';
			' . ($sendData ? 'callAnalyticsEEc("addImpression", initResources, "list");' : ''));
		}

		$altsearch = BFCHelper::getVar('altsearch','0');
		$this->state = $state;
		$this->language = $language;
		$this->item = $item;
		$this->category = $category;
		$this->showGrouped = $showGrouped ;
		$this->analyticsEnabled = $analyticsEnabled;
		$this->listNameAnalytics = $listNameAnalytics;
		$this->altsearch = $altsearch;
		
		// Display the view
		parent::display($tpl);
	}
		
}
