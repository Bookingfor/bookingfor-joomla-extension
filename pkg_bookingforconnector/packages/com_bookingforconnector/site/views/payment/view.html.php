<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

class BookingForConnectorViewPayment extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $params = null;
	protected $language = null;
	protected $actionmode = null;
	protected $hasPayed = null;
		
	// Overwriting JView display method
	function display($tpl = NULL, $preparecontent = false)
	{
		$document 	= JFactory::getDocument();
		$language 	= JFactory::getLanguage()->getTag();		
		$state		= $this->get('State');
		$params = $state->params;

		$this->state = $state;		
		$this->params = $params;
		$this->language = $language;
		$this->orderId = $params['orderId'];
								
//		if(isset($trackorder) && $trackorder) {
//			$merchants = array();
//			$merchants[] = $item->order->MerchantId;
//			
//			$criteoConfig = BFCHelper::getCriteoConfiguration(4, $merchants, $item->order->OrderId);
//			if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
//				$document->addScript('//static.criteo.net/js/ld/ld.js');
//				$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
//				window.criteo_q.push( 
//					{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
//					{ event: "setSiteType", type: "d" }, 
//					{ event: "setEmail", email: "" }, 
//					{ event: "trackTransaction", id: "'. $criteoConfig->transactionid .'",  item: ['. json_encode($criteoConfig->orderdetails) .'] }
//				);');
//			}
//			
//			$analyticsEnabled = $this->checkAnalytics("Sales Resource List");
//			if($analyticsEnabled && $config->get('eecenabled', 0) == 1) {
//				$purchaseObject = new stdClass;
//				$purchaseObject->id = "" . $item->order->OrderId;
//				$purchaseObject->affiliation = "" . $item->order->Label;
//				$purchaseObject->revenue = "" . $item->order->TotalAmount;
//				$purchaseObject->tax = 0.00;
//				
//				$allobjects = array();
//				$svcTotal = 0;
//				
//				$allservices = array_values(array_filter(simpledom_load_string($order->NotesData)->xpath("//price"), function($prc) {
//					return (string)$prc->tag == "extrarequested";
//				}));
//				
//				foreach($allservices as $svc) {
//					$svcObj = new stdClass;
//					$svcObj->id = "" . (int)$svc->priceId . " - Service";
//					$svcObj->name = (string)$svc->name;
//					$svcObj->category = "Services";
//					$svcObj->brand = $item->Name;
//					$svcObj->variant = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
//					$svcObj->price = round((float)$svc->discountedamount / (int)$svc->quantity, 2);
//					$svcObj->quantity = (int)$svc->quantity;
//					$allobjects[] = $svcObj;
//					$svcTotal += (float)$svc->discountedamount;
//				}
//				
//				$mainObj = new stdClass;
//				$mainObj->id = "" . $item->order->RequestedItemId . " - Resource";
//				$mainObj->name = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
//				$mainObj->variant = (string)BFCHelper::getItem($order->NotesData, 'refid', 'rateplan');
//				$mainObj->category = $item->MainCategoryName;
//				$mainObj->brand = $item->Name;
//				$mainObj->price = $item->order->TotalAmount - $svcTotal;
//				$mainObj->quantity = 1;
//				
//				$allobjects[] = $mainObj;
//				
//				$document->addScriptDeclaration('
//						callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "checkout", "", {
//							"step": 3,
//						});
//						callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "purchase", "", ' . json_encode($purchaseObject) . ');');
//			}
//		}

		parent::display($tpl);
	}
}
