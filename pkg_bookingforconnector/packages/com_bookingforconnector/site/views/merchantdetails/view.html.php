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

/**
 * HTML View class for the HelloWorld Component
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
	function display($tpl = null, $preparecontent = false) 
	{
		JHtml::_('bootstrap.framework');

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
		
		// add stylesheet
		JHTML::stylesheet('components/com_bookingforconnector/assets/css/font-awesome.css');
		JHTML::stylesheet('components/com_bookingforconnector/assets/bootstrap/css/bootstrap.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor.css');
		$document->addStyleSheet('components/com_bookingforconnector/assets/css/bookingfor-responsive.css');

		$document->addStyleSheet('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css');
		$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.shorten.js');
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.expander.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/calendar.js');
		if(substr($language,0,2)!='en'){
//			$document->addScript('//jquery-ui.googlecode.com/svn/tags/legacy/ui/i18n/ui.datepicker-' . substr($language,0,2) . '.js');
			$document->addScript('//ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/i18n/datepicker-' . substr($language,0,2) . '.min.js?ver=1.11.4');
		}
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.form.js');
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.blockUI.js');

		$checkAnalytics = false;
		if(!isset($item)){
			header ("Location: ". JURI::root()); 
			$app->close();
		}
		if ($item->HasResources) {
			if (BFCHelper::getString('layout') == 'resourcesajax') {
				$items = $this->get('ItemsResourcesAjax');
			}
		}

		if ($item->HasResources) {
			if (BFCHelper::getString('layout') == 'resources') {
				$items = $this->get('Items');
				$checkAnalytics = true;
				$pagination	= $this->get('Pagination');
			}
		}
		
		if (BFCHelper::getString('layout') == 'packages') {
			$items = $this->get('ItemsPackages');
			$checkAnalytics = true;
			$pagination	= $this->get('PaginationPackages');
		}

		if (BFCHelper::getString('layout') == 'package') {
			$items = $this->get('Package');
			$checkAnalytics = true;
			BFCHelper::setState($items, 'packages', 'merchant');
		}

		if (BFCHelper::getString('layout') == 'offers') {
			$items = $this->get('ItemsOffer');
			$checkAnalytics = true;
			$pagination	= $this->get('PaginationOffers');
		}
		
		if (BFCHelper::getString('layout') == 'offer') {
			$items = $this->get('Offer');
			$checkAnalytics = true;
			BFCHelper::setState($items, 'offers', 'merchant');
		}

		if (BFCHelper::getString('layout') == 'onsellunits') {
			$items = $this->get('ItemsOnSellUnit');
			$checkAnalytics = true;
			$pagination	= $this->get('PaginationOnSellUnits');
		}

		if (BFCHelper::getString('layout') == 'onsellunit') {
			$items = array($this->get('OnSellUnit'));
			$checkAnalytics = true;
			BFCHelper::setState($items, 'onsellunits', 'merchant');
		}
		
		if (BFCHelper::getString('layout') == 'ratings') {
			$items = $this->get('ItemsRating');
			$pagination	= $this->get('PaginationRatings');
			// load scripts
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.expander.min.js');
		}
		if (BFCHelper::getString('layout', 'default') == 'default') {
			$checkAnalytics = true;
		}
		if (BFCHelper::getString('layout', 'default') == 'thanks') {
			$checkAnalytics = true;
		}								
		if (BFCHelper::getString('layout') == 'rating') {
			// load css
			$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.rating.css');
			$document->addStyleSheet('components/com_bookingforconnector/assets/css/jquery.validate.css');

			// load scripts
			$document->addScript('components/com_bookingforconnector/assets/js/jquery.rating.pack.js');
		}
		$listName = "";
		if($checkAnalytics && !empty($items)) {
			$checkAnalytics = false;
			$itemType = 0;
			$totalItems = array();
			$type = "";
			switch(BFCHelper::getString('layout', 'default')) {
				case "resources":
					$listName = "Resources List";
					$type = "Resource";
					$itemType = 1;
					foreach ($items as $key => $value) {
						$obj = new stdClass;
						$obj->Id = $value->ResourceId;
						$obj->Name = $value->Name;
						$totalItems[] = $obj;
					}
					break;
				case "packages":
					$listName = "Packages List";
					$type = "Package";
					$itemType = 1;
					foreach ($items as $key => $value) {
						$obj = new stdClass;
						$obj->Id = $value->PackageId;
						$obj->Name = $value->Name;
						$totalItems[] = $obj;
					}
					break;
				case "offers":
					$listName = "Offers List";
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
					$listName = "Sales Resources Merchant List";
					$type = "Sales Resource";
					$itemType = 1;
					foreach ($items as $key => $value) {
						$obj = new stdClass;
						$obj->Id = $value->ResourceId;
						$obj->Name = $value->Name;
						$totalItems[] = $obj;
					}
					break;
				case "package":
					$listName = "Packages Page";
					$type = "Package";
					$itemType = 0;
					$obj = new stdClass;
					$obj->Id = $items->PackageId;
					$obj->Name = $items->Name;
					$totalItems[] = $obj;
					break;
				case "offer":
					$listName = "Offers Page";
					$type = "Offer";
					$itemType = 0;
					$obj = new stdClass;
					$obj->Id = $items->VariationPlanId;
					$obj->Name = $items->Name;
					$totalItems[] = $obj;
					break;
				case "onsellunit":
					$listName = "Sales Resources Merchant Page";
					$type = "Sales Resource";
					$itemType = 0;
					$obj = new stdClass;
					$obj->Id = $items->ResourceId;
					$obj->Name = $items->Name;
					$totalItems[] = $obj;
					break;
				case "thanks":
					$itemType = 2;
					break;
				case "default":
					$listName = "Merchants Page";
					$type = "Merchant";
					$itemType = 0;
					$obj = new stdClass;
					$obj->Id = $item->MerchantId;
					$obj->Name = $item->Name;
					$totalItems[] = $obj;
					break;
			}
			if($this->checkAnalytics($listName) && $config->get('eecenabled', 0) == 1) {
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
						if(!empty($orderid) && $act!="Contact" ){
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
		}

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		$layout = BFCHelper::getString('layout', "default");
		
		if($layout == "thanks" || $layout == "default") {
			$merchants = array();
			$merchants[] = $item->MerchantId;
			if($layout == "thanks") {
				$orderid = BFCHelper::getString('orderid');
				$criteoConfig = BFCHelper::getCriteoConfiguration(4, $merchants, $orderid);	
			} else if ($layout == "") {
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
		
		BFCHelper::setState($item, 'merchant', 'merchant');
		
		$this->setBreadcrumb($item, BFCHelper::getString('layout'));
		
		$this->assignRef('params', $params);
		$this->assignRef('item', $item);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('document', $document);
		$this->assignRef('language', $language);
		$this->assignRef('sitename', $sitename);
		$this->assignRef('config', $config);
		$this->assignRef('state', $state);
		$this->assignRef('checkAnalytics', $checkAnalytics);
		$this->assignRef('analyticsListName', $listName);
		
		// Display the view
		parent::display($tpl, true);
	}
	
	function setBreadcrumb($merchant, $layout = '') {
		$mainframe = JFactory::getApplication();
		$pathway   = $mainframe->getPathway();


		$count = count($pathway);
		$newPathway = array();
		if($count>1){
			$newPathway = array_pop($pathway);
		}
		$pathway->setPathway($newPathway);

		
		$pathway->addItem(
			$merchant->Name,
			JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name))
		);
		
		if ($layout != '') {
			$pathway->addItem(
				JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_' . strtoupper($layout) ),
				JRoute::_('index.php?layout=' . $layout . '&option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name))
			);
		}
	}
}