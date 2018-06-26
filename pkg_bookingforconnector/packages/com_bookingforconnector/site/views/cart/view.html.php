<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/SimpleDOM.php';

class BookingForConnectorViewCart extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $params = null;
	protected $language = null;
	protected $actionmode = null;

		
	// Overwriting JView display method
	function display($tpl = NULL, $preparecontent = false)
	{

		// Initialise variables
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$document 	= JFactory::getDocument();

		$language 	= JFactory::getLanguage()->getTag();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$layout = BFCHelper::getString('layout', "default");
		
		$checkAnalytics = true;
		$listName = "Cart Page";			
		$itemType = 0;

		if ($layout == 'thanks') {
			$listName = "Cart Page";
			$checkAnalytics = true;
			$itemType = 2;
		}								

	
		if($checkAnalytics && $this->checkAnalytics($listName) && COM_BOOKINGFORCONNECTOR_EECENABLED == 1) {
			$checkAnalytics = true;
			switch($itemType) {
//				case 0:
//					$value = $totalItems[0];
//					$obj = new stdClass;
//					$obj->id = "" . $value->Id . " - " . $type;
//					$obj->name = $value->Name;
//					$obj->category = $item->MainCategoryName;
//					$obj->brand = $item->Name;
//					$obj->variant = 'NS';
//					$document->addScriptDeclaration('callAnalyticsEEc("addProduct", [' . json_encode($obj) . '], "item");');
//					break;
//				case 1:
//					$allobjects = array();
//					foreach ($totalItems as $key => $value) {
//						$obj = new stdClass;
//						$obj->id = "" . $value->Id . " - " . $type;
//						$obj->name = $value->Name;
//						$obj->category = $item->MainCategoryName;
//						$obj->brand = $item->Name;
//						$obj->position = $key;
//						$allobjects[] = $obj;
//					}
//					$document->addScriptDeclaration('callAnalyticsEEc("addImpression", ' . json_encode($allobjects) . ', "list");');					
//					break;
				case 2:
					$orderid = 	BFCHelper::getString('orderid');
					$traceOrder = BFCHelper::IsInCookieOrders($orderid);
					if (!$traceOrder) {
						BFCHelper::AddToCookieOrders($orderid);
					}
					$act = 	BFCHelper::getString('act');
					if(!empty($orderid) && $act!="Contact" && !$traceOrder ){
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

						$orderDetails = BFCHelper::GetOrderDetailsById($orderid,$language);
						if (!empty($orderDetails) && !empty($orderDetails->ResourcesString)) {
							$order_resource_summary = json_decode($orderDetails->ResourcesString);
							$merchantIdsArr = array_unique(array_map(function ($i) { return $i->MerchantId; }, $order_resource_summary));
							$merchantIds = implode(',',$merchantIdsArr);
							$merchantDetails = json_decode(BFCHelper::getMerchantsByIds($merchantIds,$language));

							foreach($order_resource_summary as $orderItem) {
								$currMerchant = null;
								foreach($merchantDetails as $merchantDetail) {
									if ($merchantDetail->MerchantId == $orderItem->MerchantId) {
										$currMerchant = $merchantDetail;
										break;
									}
								}								
								$brand = BFCHelper::string_sanitize($currMerchant->Name);
								$mainCategoryName = BFCHelper::string_sanitize($currMerchant->MainCategoryName);
								foreach($orderItem->Items as $currKey=>$res) {
									if ($currKey==0) {
										$mainObj = new stdClass;
										$mainObj->id = "" . $res->ResourceId . " - Resource";
										$mainObj->name = BFCHelper::string_sanitize($res->Name);
//										$mainObj->variant = (string)BFCHelper::getItem($order->NotesData, 'refid', 'rateplan');
										$mainObj->category = $mainCategoryName;
										$mainObj->brand = $brand;
										$mainObj->price = $res->TotalAmount;
										$mainObj->quantity = $res->Qt;
									    $allobjects[] = $mainObj;
									}else{
										$svcObj = new stdClass;
										$svcObj->id = "" . $res->ResourceId . " - Service";
										$svcObj->name = BFCHelper::string_sanitize($res->Name);
										$svcObj->category = "Services";
										$svcObj->brand = $brand;
//										$svcObj->variant = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
										$svcObj->price = $res->TotalAmount;
										$svcObj->quantity = $res->Qt;
										$allobjects[] = $svcObj;
									}
								}

							}
								$document->addScriptDeclaration('
//								callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "checkout", "", {
//									"step": 3,
//								});
								callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "purchase", "", ' . json_encode($purchaseObject) . ');');	
						    
						}
						

						
//						if(!empty($order->NotesData) && !empty(bfi_simpledom_load_string($order->NotesData)->xpath("//price"))) {
//							$allservices = array_values(array_filter(bfi_simpledom_load_string($order->NotesData)->xpath("//price"), function($prc) {
//								return (string)$prc->tag == "extrarequested";
//							}));
//							
//							
//							if(!empty($allservices )){
//								foreach($allservices as $svc) {
//									$svcObj = new stdClass;
//									$svcObj->id = "" . (int)$svc->priceId . " - Service";
//									$svcObj->name = (string)$svc->name;
//									$svcObj->category = "Services";
//									$svcObj->brand = $item->Name;
//									$svcObj->variant = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
//									$svcObj->price = round((float)$svc->discountedamount / (int)$svc->quantity, 2);
//									$svcObj->quantity = (int)$svc->quantity;
//									$allobjects[] = $svcObj;
//									$svcTotal += (float)$svc->discountedamount;
//								}
//							}
//						
//							$mainObj = new stdClass;
//							$mainObj->id = "" . $order->RequestedItemId . " - Resource";
//							$mainObj->name = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
//							$mainObj->variant = (string)BFCHelper::getItem($order->NotesData, 'refid', 'rateplan');
//							$mainObj->category = $item->MainCategoryName;
//							$mainObj->brand = $item->Name;
//							$mainObj->price = $order->TotalAmount - $svcTotal;
//							$mainObj->quantity = 1;
//							
//							$allobjects[] = $mainObj;
//							
//		
//						$document->addScriptDeclaration('
//						callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "checkout", "", {
//							"step": 3,
//						});
//						callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "purchase", "", ' . json_encode($purchaseObject) . ');');	
//					}
					}

					break;
			}
		}
		
		$this->state = $state;		
		$this->language = $language;
		$this->items = $items;
		$this->sitename = $sitename;
		$this->analyticsEnabled = $checkAnalytics;

		// Display the view
		parent::display($tpl, true);
	}
		
}
