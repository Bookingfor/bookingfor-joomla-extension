<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
require_once $pathbase . '/helpers/paymentHelper.php';

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
		$language 	= $document->getLanguage();
		// load scripts
		$document->addScript('components/com_bookingforconnector/assets/js/jquery.validate.min.js');
		$document->addScript('components/com_bookingforconnector/assets/js/additional-methods.min.js');		
		
		// Initialise variables
		$trackorder = false;
		$state		= $this->get('State');
		
		$actionmode = BFCHelper::getVar('actionmode',"");
		
		$item = $this->get('Item');
		$params = $state->params;
				
		$this->assignRef('state', $state);		
		$this->assignRef('params', $params);
		$this->assignRef('language', $language);
		
		$this->assignRef('item', $item);
		$this->assignRef('actionmode', $actionmode);
		
		if ($actionmode=="orderpayment"){
			
			//recupero quanti pagamenti sono stati effettuati
			$item->paymentCount =  BFCHelper::getTotalOrderPayments($item->order->OrderId);
			$item->overrideAmount =  BFCHelper::getFloat('overrideAmount');

			//sostituisco i dati dell'ordine da pagare con i dati passati e l'ordine con un suffisso in più
			
			/*$item = $this->get('Item');*/ 
			/*$item = "fet";*/
		}
		if($actionmode == "orderpaid") {
			$trackorder = true;
		}
		if ($actionmode!='' && $actionmode!='cancel' && $actionmode!='donation' && $actionmode!='orderpayment'){
			if ($item->order->Status!=5){
				$hasPayed = $this->processPayment($actionmode,$item->order->OrderId);
				/* eccezione per setefi che pretende un url di ritorno */
				
			}else {
				//$hasPayed = true;
				$hasPayed = $this->processOrderPayment($actionmode,$item->order->OrderId,$language);
			}
			/*
			$link = '';
			if ($hasPayed){
			 	
			}
			$app = JFactory::getApplication();
			$app->redirect($link, $msg);
			*/
		}
				
		if ($actionmode=='' && $actionmode!='donation'){
			 if ($item->order->Status!=5){
				 $this->inizializePayment($item->order->OrderId);
			 }
		}
		if ($actionmode=='orderpaid'){
			 $hasPayed= ($item->order->Status==5);
		}
		
		if(isset($trackorder) && $trackorder) {
			$merchants = array();
			$merchants[] = $item->order->MerchantId;
			
			$criteoConfig = BFCHelper::getCriteoConfiguration(4, $merchants, $item->order->OrderId);
			if(isset($criteoConfig) && isset($criteoConfig->enabled) && $criteoConfig->enabled && count($criteoConfig->merchants) > 0) {
				$document->addScript('//static.criteo.net/js/ld/ld.js');
				$document->addScriptDeclaration('window.criteo_q = window.criteo_q || []; 
				window.criteo_q.push( 
					{ event: "setAccount", account: '. $criteoConfig->campaignid .'}, 
					{ event: "setSiteType", type: "d" }, 
					{ event: "setEmail", email: "" }, 
					{ event: "trackTransaction", id: "'. $criteoConfig->transactionid .'",  item: ['. json_encode($criteoConfig->orderdetails) .'] }
				);');
			}
			
			$analyticsEnabled = $this->checkAnalytics("Sales Resource List");
			if($analyticsEnabled && $config->get('eecenabled', 0) == 1) {
				$purchaseObject = new stdClass;
				$purchaseObject->id = "" . $item->order->OrderId;
				$purchaseObject->affiliation = "" . $item->order->Label;
				$purchaseObject->revenue = "" . $item->order->TotalAmount;
				$purchaseObject->tax = 0.00;
				
				$allobjects = array();
				$svcTotal = 0;
				
				$allservices = array_values(array_filter(simpledom_load_string($order->NotesData)->xpath("//price"), function($prc) {
					return (string)$prc->tag == "extrarequested";
				}));
				
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
				
				$mainObj = new stdClass;
				$mainObj->id = "" . $item->order->RequestedItemId . " - Resource";
				$mainObj->name = (string)BFCHelper::getItem($order->NotesData, 'nome', 'unita');
				$mainObj->variant = (string)BFCHelper::getItem($order->NotesData, 'refid', 'rateplan');
				$mainObj->category = $item->MainCategoryName;
				$mainObj->brand = $item->Name;
				$mainObj->price = $item->order->TotalAmount - $svcTotal;
				$mainObj->quantity = 1;
				
				$allobjects[] = $mainObj;
				
				$document->addScriptDeclaration('
						callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "checkout", "", {
							"step": 3,
						});
						callAnalyticsEEc("addProduct", ' . json_encode($allobjects) . ', "purchase", "", ' . json_encode($purchaseObject) . ');');
			}
		}

// assegno il riferimento dopo gli altri altrimenti non ho nessuna associazione		
		$this->assignRef('hasPayed', $hasPayed);
		/*
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');

		

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}

		$pagination->setAdditionalUrlParam("filter_order", $ordering);
		$pagination->setAdditionalUrlParam("filter_order_Dir", $direction);



		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);

		*/

		
		// Display the view
		parent::display($tpl);
	}
	
	public function inizializePayment($orderId) {
		$order = BFCHelper::setOrderStatus($orderId,1,false,false,'');
	}
	public function processPayment($actionmode,$orderId) {
		$classProcessor = $actionmode . 'Processor';

		if (class_exists($classProcessor)){
			$processor = new $classProcessor();

			if($actionmode=="virtualpay" || $actionmode=="paypalexpress" || $actionmode=="bnlpositivity"){
//				$processor = new classProcessor($data=data);
				$data = explode("|",$this->item->merchantPayment->Data);
				$processor->data = $data;
			}
			if( $actionmode=="paypalexpress" || $actionmode=="bnlpositivity"){
//				$processor = new classProcessor($data=data);
				$processor->order = $this->item->order;
			}


			$result = $processor->getResult();
			$formData = BFCHelper::getArray('form');

			$paymentData ='';
			foreach($_SERVER as $key_name => $key_value) {
				if  ($paymentData!='') $paymentData .=  '&'; 
				$paymentData .= str_replace('$', '', $key_name) . " = " . urlencode($key_value);
			}
			if(!empty($formData)){
				foreach($formData as $key_name => $key_value) {
					if  ($paymentData!='') $paymentData .=  '&';
					$paymentData .= str_replace('$', '', $key_name) . " = " . urlencode($key_value);
				}			
			}
			
			/*$paymentData = iconv('UTF-8','UTF-8//IGNORE',$paymentData);*/
			
			if ($actionmode!="setefi" && $actionmode!="activa" && $actionmode!="bankart"){
				if ($result){
					$order = BFCHelper::setOrderStatus($orderId,5,true,false,$paymentData);
					$result = ($order!=null);
				}else{
					$order = BFCHelper::setOrderStatus($orderId,7,false,false,$paymentData);
				}
				if(method_exists($processor, 'responseRedir')){
					$processor->responseRedir($order->OrderId, $result);
				}
			}
			return $result;
		}
	}
	
	// processamento ulteriori pagamenti & donazioni
	public function processOrderPayment($actionmode,$orderId,$lang) {
		$classProcessor = $actionmode . 'Processor';
		
		if (class_exists($classProcessor)){
			$processor = new $classProcessor();
	
			if($actionmode=="virtualpay" || $actionmode=="paypalexpress" || $actionmode=="bnlpositivity"){
//				$processor = new classProcessor($data=data);
				$data = explode("|",$this->item->merchantPayment->Data);
				$processor->data = $data;
			}
			if( $actionmode=="paypalexpress" || $actionmode=="bnlpositivity"){
//				$processor = new classProcessor($data=data);
				$processor->order = $this->item->order;
			}


			$result = $processor->getResult();
			$bankId = $processor->getBankId();
			$amount = $processor->getAmount();
			$formData = BFCHelper::getArray('form');

			$paymentData ='';
			foreach($_SERVER as $key_name => $key_value) {
				if  ($paymentData!='') $paymentData .=  '&'; 
				$paymentData .= str_replace('$', '', $key_name) . " = " . urlencode($key_value);
			}
			if(!empty($formData)){
				foreach($formData as $key_name => $key_value) {
					if  ($paymentData!='') $paymentData .=  '&';
					$paymentData .= str_replace('$', '', $key_name) . " = " . urlencode($key_value);
				}			
			}
			
			/*$paymentData = iconv('UTF-8','UTF-8//IGNORE',$paymentData);*/
			if ($actionmode!="setefi" && $actionmode!="activa" && $actionmode!="bankart"){
				if ($orderId>0){
				if ($result){
					$order = BFCHelper::setOrderPayment($orderId,5,true,$amount,$bankId,$paymentData,$lang,false);
					$result = ($order!=null);
				}else{
					$order = BFCHelper::setOrderPayment($orderId,7,false,$amount,$bankId,$paymentData,$lang,false);
				}
				}
				if(method_exists($processor, 'responseRedir')){
					$processor->responseRedir($order->OrderId, $result);
				}
				
			}
			return $result;
		}
	}	
}
