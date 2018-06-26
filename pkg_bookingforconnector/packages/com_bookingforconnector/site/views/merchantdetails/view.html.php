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
class BookingForConnectorViewMerchantDetails extends BFCView
{
//	protected $state = null;
//	protected $item = null;
//	protected $items = null;
//	protected $pagination = null;
//	protected $document = null;
//	protected $language = null;
			
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

		$layout = BFCHelper::getString('layout', "default");

		if(!isset($item)){
			header ("Location: ". JURI::root()); 
			$app->close();
		}
		if ($item->HasResources) {
			// load scripts
			$document->addScript('components/com_bookingforconnector/assets/js/bf_cart.js');
			$document->addScript('components/com_bookingforconnector/assets/js/bf_appTimePeriod.js');
			$document->addScript('components/com_bookingforconnector/assets/js/bf_appTimeSlot.js');
			if ($layout == 'resourcesajax') {
				$items = $this->get('ItemsResourcesAjax');
			}
		}

		if ($item->HasResources) {
			if ($layout == 'resources') {
				$items = $this->get('Items');
				$checkAnalytics = true;
				$pagination	= $this->get('Pagination');
			}
		}
		
		if ($layout == 'offers') {
			$items = $this->get('ItemsOffer');
			$checkAnalytics = true;
			$pagination	= $this->get('PaginationOffers');
		}
		
		if ($layout == 'offer') {
			$items = $this->get('Offer');
			$checkAnalytics = true;
		}

		if ($layout == 'onsellunits') {
			$items = $this->get('ItemsOnSellUnit');
			$checkAnalytics = true;
			$pagination	= $this->get('PaginationOnSellUnits');
		}

		if ($layout == 'onsellunit') {
			$items = array($this->get('OnSellUnit'));
			$checkAnalytics = true;
		}
		
		if ($layout == 'ratings') {
			$items = $this->get('ItemsRating');
			$pagination	= $this->get('PaginationRatings');
		}
		if ($layout == 'rating') {
			// load scripts
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.rating.pack.js');
		}
		$listName = "";
		$itemType = 0;
		$totalItems = array();
		$type = "";
		$listNameAnalytics =  BFCHelper::getVar('lna','0');

		if($checkAnalytics && !empty($items)) {
			//$checkAnalytics = false;
			switch($layout) {
				case "resources":
					$listNameAnalytics =  5;
//					$listName = "Resources List";
					$type = "Resource";
					$itemType = 1;
					foreach ($items as $key => $value) {
						$obj = new stdClass;
						$obj->Id = $value->ResourceId;
						$obj->Name = $value->Name;
						$totalItems[] = $obj;
					}
					break;
				case "offers":
					$listNameAnalytics =  6;
					//$listName = "Offers List";
					$type = "Offer";
					$itemType = 1;
					foreach ($items as $key => $value) {
						$obj = new stdClass;
						$obj->Id = $value->VariationPlanId;
						$obj->Name = $value->Name;
						$totalItems[] = $obj;
					}
					break;
				case "onsellunits":
					$listNameAnalytics =  7;
//					$listName = "Sales Resources Merchant List";
					$type = "Sales Resource";
					$itemType = 1;
					foreach ($items as $key => $value) {
						$obj = new stdClass;
						$obj->Id = $value->ResourceId;
						$obj->Name = $value->Name;
						$totalItems[] = $obj;
					}
					break;
				case "offer":
//					$listName = "Offers Page";
					$type = "Offer";
					$itemType = 0;
					$obj = new stdClass;
					$obj->Id = $items->VariationPlanId;
					$obj->Name = $items->Name;
					$totalItems[] = $obj;
					break;
				case "default":
//					$listName = "Merchants Page";
					$type = "Merchant";
					$itemType = 0;
					$obj = new stdClass;
					$obj->Id = $item->MerchantId;
					$obj->Name = $item->Name;
					$totalItems[] = $obj;
					break;
			}
		}else{
			$checkAnalytics = false;
			if ($layout == 'default') {
				$checkAnalytics = true;
//				$listName = "Merchants Page";
				$type = "Merchant";
				$itemType = 0;
				$obj = new stdClass;
				$obj->Id = $item->MerchantId;
				$obj->Name = $item->Name;
				$totalItems[] = $obj;
			}
			if ($layout == 'thanks') {
				$checkAnalytics = true;
				$itemType = 2;
			}								
		}

		$listName = BFCHelper::$listNameAnalytics[$listNameAnalytics];

		if($checkAnalytics && $this->checkAnalytics($listName) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			$checkAnalytics = true;
			switch($itemType) {
				case 0:
					$value = $totalItems[0];
					$obj = new stdClass;
					$obj->id = "" . $value->Id . " - " . $type;
					$obj->name = $value->Name;
					$obj->category = $item->MainCategoryName;
					$obj->brand = $item->Name;
					$obj->variant = 'NS';
					$document->addScriptDeclaration('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
					break;
				case 1:
					$allobjects = array();
					foreach ($totalItems as $key => $value) {
						$obj = new stdClass;
						$obj->id = "" . $value->Id . " - " . $type;
						$obj->name = $value->Name;
						$obj->category = $item->MainCategoryName;
						$obj->brand = $item->Name;
						$obj->position = $key;
						$allobjects[] = $obj;
					}
					$document->addScriptDeclaration('callAnalyticsEEc("addImpression", ' . json_encode($allobjects) . ', "list");');					
					break;
				case 2:
					$orderid = 	BFCHelper::getString('orderid');
					$act = 	BFCHelper::getString('act');
					if(!empty($orderid) && $act!="Contact" && $act!="ContactSale"  && $act!="ContactMerchant"  && $act!="ContactResource"){
//						if(!empty($orderid)){
						$order = BFCHelper::getSingleOrderFromService($orderid);
						$purchaseObject = new stdClass;
						$purchaseObject->id = "" . $order->OrderId;
						$purchaseObject->affiliation = "" . $order->Label;
						$purchaseObject->revenue = $order->TotalAmount;
						$purchaseObject->tax = 0.00;
						
						$allobjects = array();
						$allservices = array();
						$svcTotal = 0;
						
						if(!empty($order->NotesData) && !empty(simpledom_load_string($order->NotesData)->xpath("//price"))) {
							$allservices = array_values(array_filter(simpledom_load_string($order->NotesData)->xpath("//price"), function($prc) {
								return (string)$prc->tag == "extrarequested";
							}));
							
							
							if(!empty($allservices )){
								foreach($allservices as $svc) {
									$svcObj = new stdClass;
									$svcObj->id = "" . (int)$svc->priceId . " - Service";
									$svcObj->name = (string)$svc->name;
									$svcObj->category = "Services";
									$svcObj->brand = $item->Name;
									$svcObj->variant = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
									$svcObj->price = round((float)$svc->discountedamount / (int)$svc->quantity, 2);
									$svcObj->quantity = (int)$svc->quantity;
									$allobjects[] = $svcObj;
									$svcTotal += (float)$svc->discountedamount;
								}
							}
						
							$mainObj = new stdClass;
							$mainObj->id = "" . $order->RequestedItemId . " - Resource";
							$mainObj->name = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
							$mainObj->variant = (string)BFCHelper::getItem($order->NotesData, 'refid', 'rateplan');
							$mainObj->category = $item->MainCategoryName;
							$mainObj->brand = $item->Name;
							$mainObj->price = $order->TotalAmount - $svcTotal;
							$mainObj->quantity = 1;
							
							$allobjects[] = $mainObj;
							
		
					$document->addScriptDeclaration('
					callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "checkout", "", {
						"step": 3,
					});
					callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "purchase", "", ' . json_encode($purchaseObject) . ');');	
					}
					}

					break;
			}
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED && $layout == "thanks" || $layout == "default" ) {
			$merchants = array();
			$merchants[] = $item->MerchantId;
			if($layout == "thanks") {
				$orderid = BFCHelper::getString('orderid');
				$criteoConfig = BFCHelper::getCriteoConfiguration(4, $merchants, $orderid);	
			} else if ($layout == "default") {
				$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
			}
			if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
				$document->addScript('//static.criteo.net/js/ld/ld.js');
				if($layout == "thanks") {
					$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
					window.criteo_q.push( 
						{ event: "setAccount", account: ' . $criteoConfig->campaignid . '}, 
						{ event: "setSiteType", type: "d" }, 
						{ event: "setEmail", email: "" }, 
						{ event: "trackTransaction", id: "' . $criteoConfig->transactionid . '",  item: [' . json_encode($criteoConfig->orderdetails) . '] }
					);');
				} else if ($layout == "default") {
					$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
					window.criteo_q.push( 
						{ event: "setAccount", account: ' . $criteoConfig->campaignid . '}, 
						{ event: "setSiteType", type: "d" }, 
						{ event: "setEmail", email: "" }, 
						{ event: "viewItem", item: "' . $criteoConfig->merchants[0] . '" }
					);');
				}
			}
		}
		
//		BFCHelper::setState($item, 'merchant', 'merchant');
		
		if($layout != "contactspopup") {
			$this->setBreadcrumb($item, $layout);
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
		$this->checkAnalytics = $checkAnalytics;
		$this->analyticsListName = $listName;
		$this->listNameAnalytics = $listNameAnalytics;
		
		// Display the view
		parent::display($tpl);
	}
	
	function setBreadcrumb($merchant, $layout = '') {
		$mainframe = JFactory::getApplication();
		$pathway   = $mainframe->getPathway();
		$items   = $pathway->getPathWay();
		$count = count($items);
		$newPathway = array();
		if($count>1){
			$newPathway = array_pop($items);
		}
		$pathway->setPathway($newPathway);

		$pathway->addItem(
			$merchant->Name,
			JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name))
		);
		
		if ($layout != '' && $layout != 'default' && $layout != 'contacts') {
			$pathway->addItem(
				JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_' . strtoupper($layout) ),
				JRoute::_('index.php?layout=' . $layout . '&option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name))
			);
		}
	}
}