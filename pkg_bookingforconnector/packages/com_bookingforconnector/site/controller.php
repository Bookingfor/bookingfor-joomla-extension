<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
require_once( JPATH_COMPONENT. DIRECTORY_SEPARATOR .'helpers/BFCHelper.php' );
/**
 * Hello World Component Controller
 */
class BookingForConnectorController extends JControllerLegacy 
{
	private $serviceUri = null;
	private $apikey = null;
	private $formlabel = null;

	public function __construct($properties = null)
	{
		parent::__construct($properties);
		JLoader::import('joomla.application.component.helper');
		$params = JComponentHelper::getParams('com_bookingforconnector');
		$this->serviceUri = $params->get('wsurl', '');
		$this->apikey = $params->get('apikey', '');
		$this->formlabel = $params->get('formlabel', JPATH_SITE);
	}

	public function display($cachable = false, $urlparams = false)
	{
		// Set the default view name and format from the Request.
		$vName	= BFCHelper::getCmd('view', 'merchants');
		BFCHelper::setVar('view', $vName);

		$safeurlparams = array('typeId'=>'INT', 'rating'=>'INT');

		parent::display($cachable, $safeurlparams);

		return $this;
	}

	function listDate(){
//		global $mainframe;
		$resourceId=BFCHelper::getVar( 'resourceId');
		$ci=BFCHelper::getVar( 'checkin');
//		$return = "--listDate" . $resourceId;
//		$return .= "--checkin" . $ci;
//		$return .= "--fine listDate";
//		$model = $this->getModel('Resource'); //JModel::getInstance('Resource', 'BookingForConnectorModel');
//		$return .= $model->getCheckOutDatesFromService($resourceId ,$ci);
//		JModel::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
//		$model = JModel::getInstance('Resource', 'BookingForConnectorModel');
//		$return .= $model->getCheckOutDatesFromService($resourceId ,$ci);
		$checkin = DateTime::createFromFormat('Ymd',$ci);
		$return = BFCHelper::getCheckOutDates($resourceId ,$checkin);
		
		echo $return;      
		
		// use die() because in IIS $mainframe->close() raise a 500 error 
		$app = JFactory::getApplication();
		$app->close();
		//$mainframe->close();
	}

	function updateCCdataOrder(){
		$formData = BFCHelper::getArray('form');
		if(empty($formData)){
		}
		$ccdata = null;
		$ccdata = json_encode(BFCHelper::getCCardData($formData));
		$ccdata = BFCHelper::encrypt($ccdata);
		$orderId=BFCHelper::getVar('OrderId');
		$order = BFCHelper::updateCCdata(
				$orderId,        
				$ccdata, 
				null
                );
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		if (empty($order)){
			$order ="";
			$redirect = $redirecterror;
		}
		$app = JFactory::getApplication();
		$app->redirect($redirect, false);
		$app->close();
	}

	function sendOrder(){ //Prenotazione risorsa (orderA)
//		$formData = BFCHelper::getArray('form');
		$formData = BFCHelper::getArray('form');

		if(empty($formData)){
		}
		$customer = BFCHelper::getCustomerData($formData);

		$suggestedStay = json_decode($formData['staysuggested']);
		$req = json_decode($formData['stayrequest'], true);
		
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];

		$isgateway = $formData['isgateway'];

		// eta persone 
//		$otherData = "paxages:". str_replace("]", "" ,str_replace("[", "" , implode($req['paxages'], ',') ));
		$otherData = "paxages:". str_replace("]", "" ,str_replace("[", "" , $req['paxages'] ))
					."|"."checkin_eta_hour:".$formData['checkin_eta_hour'];
//					."|"."pageurl:".$formData['pageurl']
//					."|"."title:".$formData['title']
//					."|"."accettazione:".$formData['confirmprivacy'];
		
		$ccdata = null;
		if (BFCHelper::canAcquireCCData($formData)) { 
			$ccdata = json_encode(BFCHelper::getCCardData($formData));
			$ccdata = BFCHelper::encrypt($ccdata);
//			$ccdata = BFCHelper::getCCardData($formData);
			}
		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, $ccdata);
		$orderData['pricetype'] = $req['pricetype'];
		$orderData['label'] = $formData['label'];
		$orderData['checkin_eta_hour'] = $formData['checkin_eta_hour'];

		$processOrder = null;
		if(!empty($isgateway) && ($isgateway =="true" ||$isgateway =="1")){
			$processOrder=false;
		}

		$order = BFCHelper::setOrder(
                $orderData['customerData'], 
                $orderData['suggestedStay'], 
                $orderData['creditCardData'], 
                $orderData['otherNoteData'], 
                $orderData['merchantId'], 
                $orderData['orderType'], 
                $orderData['userNotes'], 
                $orderData['label'], 
                $orderData['cultureCode'], 
				$processOrder,
				$orderData['pricetype']
                );

		if (empty($order)){
			$order ="";
			$redirect = $redirecterror;
		}
		if (!empty($order)){
			if(!empty($isgateway) && ($isgateway =="true" ||$isgateway =="1")){
			$redirect = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);

			}else{
				$numAdults = 0;
				$persons= explode("|", $suggestedStay->Paxes);
				foreach($persons as $person) {
					$totper = explode(":", $person);
					$numAdults += (int)$totper[1];
				}
				$act = "OrderResource";
				if(!empty($order->OrderType) && strtolower($order->OrderType) =="b"){
					$act = "QuoteRequest";
				}

				$startDate = DateTime::createFromFormat('Y-m-d',BFCHelper::parseJsonDate($order->StartDate,'Y-m-d'));
				$endDate = DateTime::createFromFormat('Y-m-d',BFCHelper::parseJsonDate($order->EndDate,'Y-m-d'));
				
				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}

				$redirect = $redirect . 'act=' . $act  
				 . '&orderid=' . $order->OrderId 
				 . '&merchantid=' . $order->MerchantId 
				 . '&OrderType=' . $order->OrderType 
				 . '&OrderTypeId=' . $order->OrderTypeId 
				 . '&totalamount=' . ($order->TotalAmount *100)
				 . '&startDate=' . $startDate->format('Y-m-d')
				 . '&endDate=' . $endDate->format('Y-m-d')
				 . '&numAdults=' . $numAdults
				;
			}
//			$urlredirpayment = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
//			$redirect = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
		}
//		echo json_encode($return);      
		$app = JFactory::getApplication();
		$app->redirect($redirect, false);
		$app->close();

	}

	function sendRequestOnSell(){  //Richiesta informazioni Vendita a scalare
		$formData = BFCHelper::getArray('form');
//		$cultureCode = BFCHelper::getVar('cultureCode');
		$customerData = FormHelper::getCustomerData($formData);
//		$customerData['Culture'] = $cultureCode;
//		$customerData['UserCulture'] = $cultureCode;
		
		$searchData = 'MasterUnitCategoryId:'. BFCHelper::getInt('MasterUnitCategoryId') .'|' .
				'UnitCategoryId:'. BFCHelper::getInt('unitCategoryId') .'|' .
				'Points:'. BFCHelper::getVar('Points') .'|' .
				'ContractType:'.  BFCHelper::getInt('contractTypeId') .'|' .
				'MinPrice:'. BFCHelper::getInt('pricemin') .'|' .
				'MaxPrice:'. BFCHelper::getInt('pricemax') .'|' .
				'LocationID:'. BFCHelper::getInt('zoneId') .'|' .
				'MinArea:'. BFCHelper::getInt('areamin') .'|' .
				'MaxArea:'. BFCHelper::getInt('areamax') .'|' .
				'MinPaxes:'. BFCHelper::getInt('MinPaxes') .'|' .
				'MaxPaxes:'. BFCHelper::getInt('MaxPaxes') .'|' .
				'MinBaths:'. BFCHelper::getInt('bathsmin') .'|' .
				'MaxBaths:'. BFCHelper::getInt('bathsmax') .'|' .
				'MinRooms:'. BFCHelper::getInt('roomsmin') .'|' .
				'MaxRooms:'. BFCHelper::getInt('roomsmax') .'|' .
				'MinBedRooms:'. BFCHelper::getInt('bedroomsmin') .'|' .
				'MaxBedRooms:'. BFCHelper::getInt('bedroomsmax') .'|' .
				'maxReplies:'. BFCHelper::getInt('maxReplies') .'|' .
				'note:'. str_replace("|", " " , str_replace(":", " " ,BFCHelper::getVar('notes')));

		$merchantId = BFCHelper::getVar('merchantId');
		$orderType = $formData['orderType'];
		$label = BFCHelper::getVar('label');
		$processRequest = BFCHelper::getVar('processRequest');
		if($orderType=='i'){
		$searchData = 'MasterUnitCategoryId:'. BFCHelper::getInt('MasterUnitCategoryId') .'|' .
				'UnitCategoryId:'. BFCHelper::getInt('unitCategoryId') .'|' .
				'Points:'. BFCHelper::getVar('Points') .'|' .
				'ContractType:'.  BFCHelper::getInt('contractTypeId') .'|' .
				'MinPrice:'. BFCHelper::getInt('pricemin') .'|' .
				'MaxPrice:'. BFCHelper::getInt('pricemin') .'|' .
				'LocationID:'. BFCHelper::getInt('zoneId') .'|' .
				'MinArea:'. BFCHelper::getInt('areamin') .'|' .
				'MaxArea:'. BFCHelper::getInt('areamin') .'|' .
				'MinPaxes:'. BFCHelper::getInt('MinPaxes') .'|' .
				'MaxPaxes:'. BFCHelper::getInt('MinPaxes') .'|' .
				'MinBaths:'. BFCHelper::getInt('bathsmin') .'|' .
				'MaxBaths:'. BFCHelper::getInt('bathsmax') .'|' .
				'MinRooms:'. BFCHelper::getInt('roomsmin') .'|' .
				'MaxRooms:'. BFCHelper::getInt('roomsmin') .'|' .
				'MinBedRooms:'. BFCHelper::getInt('bedroomsmin') .'|' .
				'MaxBedRooms:'. BFCHelper::getInt('bedroomsmax') .'|' .
				'maxReplies:'. BFCHelper::getInt('maxReplies') .'|' .
				'note:'. str_replace("|", " " , str_replace(":", " " ,BFCHelper::getVar('notes')));

		}

		$return = BFCHelper::sendRequestOnSell($customerData, $searchData, $merchantId, $orderType, $label, $cultureCode, $processRequest);	
		if (empty($return)){
			$return ="";
		}
		$app = JFactory::getApplication();
		if (empty($redirect)){
			echo json_encode($return);      
		}else{
			$app->redirect($redirect, false);
		}
		$app->close();
	}

	function sendSimpleRequestOnSell(){
		$formData = BFCHelper::getArray('form');
		$cultureCode = BFCHelper::getVar('cultureCode');
		$customerData = FormHelper::getCustomerData($formData);
		$customerData['Culture'] = $cultureCode;
		$customerData['UserCulture'] = $cultureCode;
// create otherData (string)
		$otherData = "pageurl:".$formData['pageurl']
					."|"."title:".$formData['title']
					."|"."accettazione:".$formData['confirmprivacy'];
			
		$orderData =  BFCHelper::prepareOrderData($formData, $customerData, null, $otherData, null);

				$merchantId = BFCHelper::getVar('merchantId');
				$type = BFCHelper::getVar('type');
				$label = BFCHelper::getVar('label');
		$orderData['processOrder'] = BFCHelper::getVar('processRequest');
		$orderData['orderType'] = $type;

		$return = BFCHelper::setInfoRequest(
					$orderData['customerData'], 
					$orderData['suggestedStay'],
					$orderData['otherNoteData'], 
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'],
					$orderData['processOrder']
					);
		if (isset($return)){
			echo $return->InfoRequestId;      
		}
//		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function sendAlertOnSell(){
		$formData = BFCHelper::getArray('form');

		$cultureCode = BFCHelper::getVar('cultureCode');
		$customerData = FormHelper::getCustomerData($formData);
		$customerData['Culture'] = $cultureCode;
		$customerData['UserCulture'] = $cultureCode;
		$searchData = array(
				'MasterUnitCategoryId' =>BFCHelper::getInt('MasterUnitCategoryId'),
				'UnitCategoryId' => BFCHelper::getInt('unitCategoryId'),
				'Points' => BFCHelper::getVar('Points'),
				'ContractType' =>  BFCHelper::getInt('contractTypeId'),
				'MinPrice' => BFCHelper::getInt('pricemin'),
				'MaxPrice' => BFCHelper::getInt('pricemax'),
				'LocationID' => BFCHelper::getInt('zoneId'),
				'MinArea' => BFCHelper::getInt('areamin'),
				'MaxArea' => BFCHelper::getInt('areamax'),
				'MinPaxes' => BFCHelper::getInt('MinPaxes'),
				'MaxPaxes' => BFCHelper::getInt('MaxPaxes'),
				'MinBaths' => BFCHelper::getInt('bathsmin'),
				'MaxBaths' => BFCHelper::getInt('bathsmax'),
				'MinRooms' => BFCHelper::getInt('roomsmin'),
				'MaxRooms' => BFCHelper::getInt('roomsmax'),
				'MinBedRooms' => BFCHelper::getInt('bedroomsmin'),
				'MaxBedRooms' => BFCHelper::getInt('bedroomsmax')
		);
		$merchantId = BFCHelper::getVar('merchantId');
		$type = BFCHelper::getVar('type');
		$label = BFCHelper::getVar('label');
		$processAlert = BFCHelper::getVar('processAlert');
		$enabled = BFCHelper::getVar('enabled');

		$return = BFCHelper::setAlertOnSell($customerData, $searchData, $merchantId, $type, $label, $cultureCode, $processAlert, $enabled);	
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function sendContact(){ //Richiesta informazioni Merchant (infoRequestA)
		$formData = BFCHelper::getArray('form');
//		$checkrecaptcha = true;
//		JPluginHelper::importPlugin('captcha');
//		$dispatcher = JDispatcher::getInstance();
//		if (!empty($formData['recaptcha_response_field'])) {
//			$result = $dispatcher->trigger('onCheckAnswer',$formData['recaptcha_response_field']);
//			if(!$result[0]){
//				$checkrecaptcha = false;
//				//die('Invalid Captcha Code');
//			}
//		}
//		if($checkrecaptcha){
		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;
		$redirect = $formData['Redirect'];
		// create otherData (string)
		$numAdults = BFCHelper::getOptionsFromSelect($formData,'Totpersons');
		
		$otherData = "persone:".$numAdults."|"
			."accettazione:".BFCHelper::getOptionsFromSelect($formData,'accettazione');
		// create SuggestedStay
		$startDate = null;
		$endDate = null;

		if ($formData['CheckIn'] != null && $formData['CheckOut'] != null) {
			
			$startDate = DateTime::createFromFormat('d/m/Y',$formData['CheckIn']);
			$endDate = DateTime::createFromFormat('d/m/Y',$formData['CheckOut']);
			
			$sStay = array(
						'CheckIn' => DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d\TH:i:sO'),
						'CheckOut' => DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d\TH:i:sO')
					);

			$suggestedStay = new stdClass(); 
			foreach ($sStay as $key => $value) 
			{ 
				$suggestedStay->$key = $value; 
			}
			$otherData .= "|" . "CheckIn:" . $startDate->format('Y-m-d') ."|" ."CheckOut:" . $endDate->format('Y-m-d');
		}
					
		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;

		$return = BFCHelper::setInfoRequest(
					$orderData['customerData'], 
					$orderData['suggestedStay'],
					$orderData['otherNoteData'], 
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'],
					$orderData['processOrder']
					);	
		if (empty($return)){
			$return ="";
		}

		if (!empty($return)){

				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}
				$redirect = $redirect . 'act=ContactMerchant&orderid=' . $return->OrderId 
				 . '&merchantid=' . $return->MerchantId 
				 . '&OrderType=' . $return->OrderType 
				 . '&OrderTypeId=' . $return->OrderTypeId
				 . '&RequestType=' . $return->RequestType
				 . '&numAdults=' . $numAdults
				;
			if (!empty($startDate)){
				$redirect = $redirect . '&startDate=' . $startDate->format('Y-m-d')
				 . '&endDate=' . $endDate->format('Y-m-d')
				;
			}
		}

//		echo json_encode($return);      
		$app = JFactory::getApplication();
//		$app->redirect($redirect, false);
		if (empty($redirect)){
			echo json_encode($return);      
		}else{
			$app->redirect($redirect, false);
		}
		$app->close();
		}
//	}

	function sendInforequest(){ //Richiesta informazioni Risorsa (infoRequestC)
		$formData = BFCHelper::getArray('form');
//		JPluginHelper::importPlugin('captcha');
//		$dispatcher = JDispatcher::getInstance();
//		$result = $dispatcher->trigger('onCheckAnswer',$formData['recaptcha_response_field']);
//		if(!$result[0]){
//			die('Invalid Captcha Code');
//		}else{


		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;
		$redirect = $formData['Redirect'];
		// create otherData (string)
		$otherData = "persone:".BFCHelper::getOptionsFromSelect($formData,'Totpersons')."|"
			."accettazione:".BFCHelper::getOptionsFromSelect($formData,'accettazione');
		$numAdults = BFCHelper::getOptionsFromSelect($formData,'Totpersons');
		// create SuggestedStay
		$startDate = null;
		$endDate = null;
			if (!empty($formData['CheckIn']) && !empty($formData['CheckOut'])) {
				$startDate = DateTime::createFromFormat('d/m/Y',$formData['CheckIn']);
				$endDate = DateTime::createFromFormat('d/m/Y',$formData['CheckOut']);
					$sStay = array(
								'CheckIn' => DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d\TH:i:sO'),
								'CheckOut' => DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d\TH:i:sO'),
								'UnitId' => $formData['resourceId']
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "CheckIn:" . DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d') . "|" ."CheckOut:" . DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d') . "|" . "UnitId:" . $formData['resourceId'];
				}else{
			if (!empty($formData['resourceId']))  {
					$sStay = array(
								'UnitId' => $formData['resourceId']
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "UnitId:" . $formData['resourceId'];
				}
			}
					
		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;


		$return = BFCHelper::setInfoRequest(
					$orderData['customerData'], 
					$orderData['suggestedStay'],
					$orderData['otherNoteData'], 
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'],
					$orderData['processOrder']
					);	
//}

		if (empty($return)){
			$return ="";
		}else{
			
			if(strpos($redirect, "?")=== false){
				$redirect = $redirect . '?';
			}else{
				$redirect = $redirect . '&';
			}

			$redirect = $redirect . 'act=ContactResource&orderid=' . $return->OrderId 
					 . '&merchantid=' . $return->MerchantId 
					 . '&OrderType=' . $return->OrderType 
					 . '&OrderTypeId=' . $return->OrderTypeId
					 . '&RequestType=' . $return->RequestType
					 . '&numAdults=' . $numAdults
					;
				if (!empty($startDate)){
					$redirect = $redirect . '&startDate=' . $startDate->format('Y-m-d')
					 . '&endDate=' . $endDate->format('Y-m-d')
					;
				}
		}
//		echo json_encode($return);      
		$app = JFactory::getApplication();
		$app->redirect($redirect, false);
		$app->close();

	}

	function sendOnSellrequest(){ //Richiesta informazioni Vendita (infoRequestB)
		$formData = BFCHelper::getArray('form');

		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		$otherData = array();
		if (!empty($formData['resourceId']))  {
				$sStay = array(
							'UnitId' => $formData['resourceId']
						);

				$suggestedStay = new stdClass(); 
				foreach ($sStay as $key => $value) 
				{ 
					$suggestedStay->$key = $value; 
				}
				$otherData["UnitId:"] = "UnitId:" . $formData['resourceId'];
			}
		if (!empty($formData['pageurl']))  {
				$otherData["pageurl:"] = "pageurl:" . $formData['pageurl'];
		}
		if (!empty($formData['title']))  {
				$otherData["title:"] = "title:" . $formData['title'];
		}
		if (!empty($formData['resourceId']))  {
				$otherData["onsellunitid:"] = "onsellunitid:" . $formData['resourceId'];
		}
		if (!empty($formData['accettazione']))  {
				$otherData["accettazione:"] = "accettazione:" . BFCHelper::getOptionsFromSelect($formData,'accettazione');
		}
		
				

		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, implode("|",$otherData), null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;

		$return = BFCHelper::setInfoRequest(
					$orderData['customerData'], 
					$orderData['suggestedStay'],
					$orderData['otherNoteData'], 
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'],
					$orderData['processOrder']
					);	

		if (empty($return)){
			$return ="";
			$redirect = $redirecterror;
		}
		if (!empty($return)){

				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}
				$redirect = $redirect . 'act=ContactSale&orderid=' . $return->OrderId 
				 . '&merchantid=' . $return->MerchantId 
				 . '&OrderType=' . $return->OrderType 
				 . '&OrderTypeId=' . $return->OrderTypeId
				 . '&RequestType=' . $return->RequestType
				;
		}
//		echo json_encode($return);      
		$app = JFactory::getApplication();
		if (empty($redirect)){
			echo json_encode($return);      
		}else{
			$app->redirect($redirect, false);
		}
		$app->close();

	}

	function sendOffer(){ //Richiesta informazioni pacchetto (in divenire) (infoRequestE)
		$formData = BFCHelper::getArray('form');

		$customer = BFCHelper::getCustomerData($formData);
		$suggestedStay = null;
		$paxages = BFCHelper::getStayParam('paxages');
		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];

		// create otherData (string)
		$otherData = 'offerId:'.$formData['offerId']."|"		
					."persone:".$formData['persons']."|"
					."accettazione:".BFCHelper::getOptionsFromSelect($formData,'accettazione')."|"
					."paxages:". implode(',',$paxages)."|"
					."checkin_eta_hour:".$formData['checkin_eta_hour'];
		
		// create SuggestedStay
		$startDate = null;
		$endDate = null;
		if (!empty($formData['CheckIn']) && !empty($formData['CheckOut'])) {
				$startDate = DateTime::createFromFormat('d/m/Y',$formData['CheckIn']);
				$endDate = DateTime::createFromFormat('d/m/Y',$formData['CheckOut']);
					$sStay = array(
								'CheckIn' => DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d\TH:i:sO'),
								'CheckOut' => DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d\TH:i:sO'),
								'UnitId' => $formData['resourceId']
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "CheckIn:" . DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d') . "|" ."CheckOut:" . DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d') . "|" . "UnitId:" . $formData['resourceId'];
		}else{
			if (!empty($formData['resourceId']))  {
					$sStay = array(
								'UnitId' => $formData['resourceId']
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "UnitId:" . $formData['resourceId'];
				}
		}

		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;
		$orderData['checkin_eta_hour'] = $formData['checkin_eta_hour'];

		$return = BFCHelper::setOrder(
					$orderData['customerData'], 
					$orderData['suggestedStay'], 
					$orderData['creditCardData'], 
					$orderData['otherNoteData'],
					$orderData['merchantId'], 
					$orderData['orderType'], 
					$orderData['userNotes'], 
					$orderData['label'], 
					$orderData['cultureCode'], 
					true,
					null
					);	

		if (empty($return)){
			$return ="";
			$redirect = $redirecterror;
		}
		if (!empty($return)){

				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}
				$redirect = $redirect . 'act=ContactPackage&orderid=' . $return->OrderId 
				 . '&merchantid=' . $return->MerchantId 
				 . '&OrderType=' . $return->OrderType 
				 . '&OrderTypeId=' . $return->OrderTypeId;
//				 . '&RequestType=' . $return->RequestType
				
		}
		echo json_encode($return);      
		$app = JFactory::getApplication();
		$app->redirect($redirect, false);
		$app->close();

	}

	function sendScalarRequest(){
		$formData = BFCHelper::getArray('form');

		$customer = BFCHelper::getCustomerData($formData);

		$redirect = $formData['Redirect'];

// create otherData (string)
		$otherData = "adulti:".BFCHelper::getOptionsFromSelect($formData,'TotPersons')."|"
			."bambini:".BFCHelper::getOptionsFromSelect($formData,'Totchildrens')."|"
			."etabambini:".$formData['ChildrenAge']."|"
			."tipologiastruttura:".BFCHelper::getOptionsFromSelect($formData,'merchantcategory')."|"
			."trattamento:".BFCHelper::getOptionsFromSelect($formData,'treatments')."|"
			."maxrisposte:".BFCHelper::getOptionsFromSelect($formData,'Maxresponse')."|"
			."accettazione:".BFCHelper::getOptionsFromSelect($formData,'accettazione');

		$suggestedStay = null;
		// create SuggestedStay
				if ($formData['CheckIn'] != null && $formData['CheckOut'] != null) {
					$sStay = array(
								'CheckIn' => DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d\TH:i:sO'),
								'CheckOut' => DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d\TH:i:sO')
							);

					$suggestedStay = new stdClass(); 
					foreach ($sStay as $key => $value) 
					{ 
						$suggestedStay->$key = $value; 
					}
					$otherData .= "|" . "CheckIn:" . DateTime::createFromFormat('d/m/Y',$formData['CheckIn'])->format('Y-m-d') ."|" ."CheckOut:" . DateTime::createFromFormat('d/m/Y',$formData['CheckOut'])->format('Y-m-d');
				}
					
		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, null);

		$orderData['processOrder'] = true;
		$orderData['label'] = $this->formlabel;

		$order = BFCHelper::setOrder(
			$orderData['customerData'], 
			$orderData['suggestedStay'], 
			$orderData['creditCardData'], 
			$orderData['otherNoteData'], 
			$orderData['merchantId'], 
			$orderData['orderType'], 
			$orderData['userNotes'], 
			$orderData['label'], 
			$orderData['cultureCode']);
		/*if (isset($order))
			$thankYouMessage = "Grazie";
		else
		$thankYouMessage = "Errore invio dati";*/

		if (empty($order)){
			$order ="";
		}
		echo json_encode($order);      
		$app = JFactory::getApplication();
		$app->redirect($redirect, false);
		$app->close();
	}

	function sendRating(){
		$formData = BFCHelper::getArray('form');		
		$name=BFCHelper::getVar('name');
		$city=BFCHelper::getVar('city');
		$typologyid=BFCHelper::getVar('typologyid');
		$nation=BFCHelper::getVar('nation');
//		$typologyid=FormHelper::getOptionsFromSelect($formData, 'typologyid');
//		$nation= FormHelper::getOptionsFromSelect($formData, 'nation'); // BFCHelper::getVar('nation');
		$email=BFCHelper::getVar('email');
		$value1=BFCHelper::getVar('hfvalue1');
		$value2=BFCHelper::getVar('hfvalue2');
		$value3=BFCHelper::getVar('hfvalue3');
		$value4=BFCHelper::getVar('hfvalue4');
		$value5=BFCHelper::getVar('hfvalue5');
		$totale=BFCHelper::getVar('hftotale');
		$pregi=BFCHelper::getVar('pregi');
		$difetti=BFCHelper::getVar('difetti');
		$merchantId=BFCHelper::getVar('merchantid');
		$label=BFCHelper::getVar('label');
		$user = JFactory::getUser();
		$cultureCode = BFCHelper::getVar('cultureCode');
		$userId=null;
		if ($user->id != 0) {
			$userId=$user->id ;
		}
		$checkin=BFCHelper::getVar('checkin');
		$resourceId=BFCHelper::getVar('resourceId');
		$hashorder=BFCHelper::getVar('hashorder');
		$orderId = null;
		
		$redirect = BFCHelper::getVar('Redirect'); 
		$redirecterror =  BFCHelper::getVar('Redirecterror'); 

		if (empty($resourceId)){
			$resourceId = null;
		}
		if (!empty($hashorder)){
			//	 controllo se ho un ordine
			$orderId = BFCHelper::decrypt($hashorder);
			// controllo che l'ordine sia numerico altrimenti non lo considero
			if (!is_numeric($orderId))
			{
				$orderId = null;
			}
		}
		$return = BFCHelper::setRating($name, $city, $typologyid, $email, $nation, $merchantId,$value1, $value2, $value3, $value4, $value5, $totale, $pregi, $difetti, $userId, $cultureCode, $checkin, $resourceId, $orderId, $label);	
		if ($return < 1){
			$return ="";
			$redirect = $redirecterror;
		}
		if ($return >0 && !empty($redirect)){
			if(strpos($redirect, "?")=== false){
				$redirect = $redirect . '?';
			}else{
				$redirect = $redirect . '&';
			}
			$redirect = $redirect . 'act=Rating';
		}

		$app = JFactory::getApplication();
		if (empty($redirect)){
			echo $return;      
		}else{
			$app->redirect($redirect, false);
		}
		$app->close();
	}

	function CheckAvailabilityCalendar(){
		$resourceId=BFCHelper::getVar('resourceId');
			$checkIn =  BFCHelper::getStayParam('checkin',null);
			$duration  =  BFCHelper::getStayParam('duration');
			if(!isset($duration)){
				$duration =  BFCHelper::$defaultDaysSpan;
			}
			$checkOut = null;
			if(isset($checkIn)){
				$checkOut =   BFCHelper::getStayParam('checkout', $checkIn->modify($duration));
			}
		$return = BFCHelper::getCheckAvailabilityCalendar($resourceId,$checkIn,$checkOut);
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	
	}
	function CheckAvailabilityCalendarFromlList(){
		$resourcesId=BFCHelper::getVar('resourcesId');
			$checkIn =  BFCHelper::getStayParam('checkin',null);
			$duration  =  BFCHelper::getStayParam('duration');
			if(!isset($duration)){
				$duration =  BFCHelper::$defaultDaysSpan;
			}
			$checkOut = null;
			if(isset($checkIn)){
				$checkOut =   BFCHelper::getStayParam('checkout', $checkIn->modify($duration));
			}
		$return = BFCHelper::getCheckAvailabilityCalendarFromlList($resourcesId,$checkIn,$checkOut);
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	
	}
	function GetMerchantsByIds(){
		$app = JFactory::getApplication();
		$listsId=BFCHelper::getVar('merchantsId');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::getMerchantsByIds($listsId,$language);
		echo $return;      
		$app->close();
	
	}
	function getDiscountDetails(){
		$ids=BFCHelper::getVar('discountId');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::getDiscountDetails($ids,$language);
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	
	}
	function GetDiscountsByResourceId(){
		$resourcesId=BFCHelper::getVar('resourcesId');
		$hasRateplans=BFCHelper::getVar('hasRateplans');
		$language=BFCHelper::getVar('language');		
		$return = BFCHelper::GetDiscountsByResourceId($resourcesId,$hasRateplans);
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	
	}
	
	function getRateplanDetails(){
		$rateplanId=BFCHelper::getVar('rateplanId');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::getRateplanDetails($rateplanId);
		if(!empty($return)){
			$return = json_encode($return);
			}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	
	}
	function GetResourcesByIds(){
		$listsId=BFCHelper::getVar('resourcesId');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::GetResourcesByIds($listsId,$language);
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	
	}
	function GetResourcesCalculateByIds(){
		$listsId=BFCHelper::getVar('resourcesId');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::GetResourcesCalculateByIds($listsId,$language);
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	
	}
	
	function GetResourcesOnSellByIds(){
		$listsId=BFCHelper::getVar('resourcesId');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::GetResourcesOnSellByIds($listsId,$language);
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	
	}
	function GetCondominiumsByIds(){
		$listsId=BFCHelper::getVar('ids');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::GetCondominiumsByIds($listsId,$language);
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	
	}
	function GetPhoneByMerchantId(){
		$merchantId=BFCHelper::getVar('merchantid');
		$number=BFCHelper::getVar('n');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::GetPhoneByMerchantId($merchantId,$language,$number);
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function GetFaxByMerchantId(){
		$merchantId=BFCHelper::getVar('merchantid');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::GetFaxByMerchantId($merchantId,$language);
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function getMerchantGroups(){
		$return = BFCHelper::getTags("","1"); //getTags
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo json_encode($return);      
		$app = JFactory::getApplication();
		$app->close();
	}

	function getResourceGroups(){
		$return = BFCHelper::getTags("","4"); //getTags
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo json_encode($return);      
		$app = JFactory::getApplication();
		$app->close();
	}


	function getMerchantGroupsByMerchantId(){
		$merchantId=BFCHelper::getVar('merchantid');
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::getMerchantGroupsByMerchantId($merchantId,$language);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function getResourcesOnSellShowcase(){
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::getResourcesOnSellShowcase($language);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}
	
	function getCategoryPriceMqAverages(){
		$language=BFCHelper::getVar('language');
		$locationid=BFCHelper::getInt('locationid');
		$return = BFCHelper::getCategoryPriceMqAverages($language,$locationid);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function getAvailableLocations(){
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::getAvailableLocationsAverages($language);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}
	function getPriceAverages(){
		$language=BFCHelper::getVar('language');
		$unitcategoryid=BFCHelper::getVar('unitcategoryid');
		$locationid=BFCHelper::getVar('locationid');
		$return = BFCHelper::getPriceAverages($language,$locationid ,$unitcategoryid);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function getPriceHistory(){
		$language=BFCHelper::getVar('language');
		$unitcategoryid=BFCHelper::getVar('unitcategoryid');
		$locationid=BFCHelper::getVar('locationid');
		$return = BFCHelper::getPriceHistory($language,$locationid ,$unitcategoryid);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}
	
	function getPriceMqAverageLastYear(){
		$language=BFCHelper::getVar('language');
		$unitcategoryid=BFCHelper::getVar('unitcategoryid');
		$locationid=BFCHelper::getVar('locationid');
		$contracttype=BFCHelper::getVar('contracttypeid');
		$return = BFCHelper::getPriceMqAverageLastYear($language,$locationid ,$unitcategoryid,$contracttype);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function getResourcesOnSellGallery(){
		$language=BFCHelper::getVar('language');
		$return = BFCHelper::getResourcesOnSellGallery($language);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}
	

	function getResourceRatingAverage(){
		$language=BFCHelper::getVar('language');
		$resourceId=BFCHelper::getVar('resourceId');
		$merchantId=BFCHelper::getVar('merchantId');
		$return = null;
//		$return = $language.$resourceId.$merchantId;
		$summaryRatings = BFCHelper::getResourceRatingAverage($merchantId, $resourceId);
		if (!empty($summaryRatings)){
			$total = number_format((float)$summaryRatings->Average, 1, '.', '');
			$totalInt = BFCHelper::convertTotal($total);
			$text = JText::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_VALUATION_'.$totalInt);
			$return = json_encode(array($total , $text) );

//			$return = $total . "|" . $text ;
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function getCompleteRateplansStay(){
		$language=BFCHelper::getVar('language');
		$resourceId=BFCHelper::getVar('resourceId');
//		$merchantId=BFCHelper::getVar('merchantId');
		$ratePlanId=BFCHelper::getVar('pricetype');
		$variationPlanId=BFCHelper::getVar('variationPlanId');
		$selectablePrices=BFCHelper::getStayParam('extras');
		$pricetype=BFCHelper::getVar('pricetype');

		$selectablePrices=isset($_REQUEST['selectableprices'])?$_REQUEST['selectableprices']:$selectablePrices;
		
		$availabilitytype=isset($_REQUEST['availabilitytype'])?$_REQUEST['availabilitytype']:null; 

		$checkIn =  BFCHelper::getStayParam('checkin',null);
		$duration  =  BFCHelper::getStayParam('duration');
		if ($availabilitytype == 0 || $availabilitytype ==1 ) // product TimePeriod
		{
			$checkIn->setTime(0,0,0);
		}

		if ($availabilitytype ==2 ) // product TimePeriod
		{
			$duration = isset($_REQUEST['duration'])?$_REQUEST['duration']:null; 
			$checkIn = DateTime::createFromFormat("YmdHis", $_REQUEST['CheckInTime']);
		}
		
		if(!isset($duration)){
			$duration =  BFCHelper::$defaultDaysSpan;
		}
		if(empty($duration)){
			$duration = 1;
		}
		$packages  =  BFCHelper::getStayParam('packages');
		$paxages =  BFCHelper::getStayParam('paxages',null);

		$return = null;
//		$return = $language.$resourceId.$merchantId;
//		$return = BFCHelper::getCompleteRateplansStayFromParameter($resourceId,$checkIn,$duration,$paxages,$selectablePrices,$packages,$pricetype,$ratePlanId,$variationPlanId);
		$return = BFCHelper::GetCompleteRatePlansStayWP($resourceId,$checkIn,$duration,$paxages,$selectablePrices,$packages,$pricetype,$ratePlanId,$variationPlanId,null,null);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function getListCheckInDayPerTimes(){
//		$language=$_REQUEST['language'];
		$resourceId = isset($_REQUEST['resourceId'])?$_REQUEST['resourceId']:null;
		$fromDate=isset($_REQUEST['fromDate'])?$_REQUEST['fromDate']:null;

		$return = null;
		$return = BFCHelper::GetListCheckInDayPerTimes($resourceId,$fromDate);
		if (!empty($return)){
				$return = json_encode($return);
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}


/*-------------------------*/
	function getLocationZone(){
//		$model = new BookingForConnectorModelMerchants;
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Merchants', 'BookingForConnectorModel');
		$items = $model->getItemsJson(true);
		echo $items;	
	}

	//new send orders...
	function sendOrders(){
		$formData = $_POST['form'];
		if(empty($formData)){
		}
 		
		$customer = BFCHelper::getCustomerData($formData);

		$userNotes = $formData['note'];
		$cultureCode = $formData['cultureCode'];
		$merchantId = $formData['merchantId'];
		$orderType = $formData['orderType'];
		$label = $formData['label'];
		$OrderJson = $formData['hdnOrderData'];
		$bookingTypeSelected = $formData['bookingtypeselected'];

//		$suggestedStays =  BFCHelper::CreateOrder($OrderJson,$cultureCode,$bookingTypeSelected);
		$suggestedStays = null;

//		$listCartorderid = array();
//		// recupero tutti i cartorderid per la cancellazione del carrello
//// WP =>			$orderModel = json_decode(stripslashes($OrderJson));
//			$orderModel = json_decode($OrderJson);
//            if ($orderModel->Resources != null && count($orderModel->Resources) > 0 )
//            {
//                foreach ($orderModel->Resources as $resource)
//                {
//					if(!empty($resource->CartOrderId)){
//						$listCartorderid[] = $resource->CartOrderId;
//					}
//				}
//			}
//		$listCartorderidstr = implode(",",$listCartorderid);

//		$suggestedStay = json_decode(stripslashes($formData['staysuggested']));
//		$req = json_decode(stripslashes($formData['stayrequest']), true);

		$redirect = $formData['Redirect'];
		$redirecterror = $formData['Redirecterror'];
		$isgateway = $formData['isgateway'];


//		$otherData = "paxages:". str_replace("]", "" ,str_replace("[", "" , $req['paxages'] ))
//					."|"."checkin_eta_hour:".$formData['checkin_eta_hour'];
		$otherData = "checkin_eta_hour:".$formData['checkin_eta_hour'];
//		$customerDatas = array($customerData);

		$ccdata = null;
//		if (BFCHelper::canAcquireCCData($formData)) { 
		$ccdata = BFCHelper::getCCardData($formData);
		if (!empty($ccdata)) {
			$ccdata = BFCHelper::encrypt(json_encode($ccdata));
		}
//			}
		$orderData = array(
				'customerData' =>  array($customer),
				'suggestedStay' =>$suggestedStays,
				'creditCardData' => $ccdata,
				'otherNoteData' => $otherData,
				'merchantId' => $merchantId,
				'orderType' => $orderType,
				'userNotes' => $userNotes,
				'label' => $label,
				'cultureCode' => $cultureCode
				);

//		$orderData =  BFCHelper::prepareOrderData($formData, $customer, $suggestedStay, $otherData, $ccdata);
		$orderData['pricetype'] = "";
		if(isset($formData['pricetype'])){
			$orderData['pricetype'] = $formData['pricetype'];
		}
		$orderData['label'] = $formData['label'];
		$orderData['checkin_eta_hour'] = $formData['checkin_eta_hour'];
		$orderData['merchantBookingTypeId'] = $formData['bookingtypeselected'];
		$orderData['policyId'] = $formData['policyId'];

		$processOrder = null;
		if(!empty($isgateway) && ($isgateway =="true" ||$isgateway =="1")){
			$processOrder=false;
		}


		$order = BFCHelper::setOrder(
                $orderData['customerData'], 
                $orderData['suggestedStay'], 
                $orderData['creditCardData'], 
                $orderData['otherNoteData'], 
                $orderData['merchantId'], 
                $orderData['orderType'], 
                $orderData['userNotes'], 
                $orderData['label'], 
                $orderData['cultureCode'], 
				$processOrder,
				$orderData['pricetype'],
				$orderData['merchantBookingTypeId'],
				$orderData['policyId']
                );
		$tmpUserId = BFCHelper::bfi_get_userId();
		$currCart = BFCHelper::GetCartByExternalUser($tmpUserId, $language, true);

		if (empty($order)){
			$order ="";
			$redirect = $redirecterror;
		}
		if (!empty($order)){
			// cancello il carrello
//			BFCHelper::setSession('hdnBookingType', '', 'bfi-cart');
//			BFCHelper::setSession('hdnOrderData', '', 'bfi-cart');
//			if(!empty($listCartorderidstr)){
//				$tmpUserId = BFCHelper::bfi_get_userId();
//				$currCart = BFCHelper::DeleteFromCartByExternalUser($tmpUserId, $cultureCode, $listCartorderidstr);
////				$tmpUserId = bfi_get_userId();
////				$model = new BookingForConnectorModelOrders;
////				$currCart = $model->DeleteFromCartByExternalUser($tmpUserId, $cultureCode, $listCartorderidstr);
//			}	

			
			if(!empty($isgateway) && ($isgateway =="true" ||$isgateway =="1")){
//WP->			$payment_page = get_post( bfi_get_page_id( 'payment' ) );
//			$url_payment_page = get_permalink( $payment_page->ID );
//
////			$redirect = $url_payment_page .'?orderId=' . $order->OrderId;
//			$redirect = $url_payment_page .'/' . $order->OrderId;
			$redirect = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);

			}else{
				$numAdults = 0;
//				if(isset($suggestedStays->Paxes)){
//					$persons= explode("|", $suggestedStays->Paxes);
//					foreach($persons as $person) {
//						$totper = explode(":", $person);
//						$numAdults += (int)$totper[1];
//					}
//				}

				$act = "OrderResource";
				if(!empty($order->OrderType) && strtolower($order->OrderType) =="b"){
					$act = "QuoteRequest";
				}

				$startDate = DateTime::createFromFormat('Y-m-d',BFCHelper::parseJsonDate($order->StartDate,'Y-m-d'));
				$endDate = DateTime::createFromFormat('Y-m-d',BFCHelper::parseJsonDate($order->EndDate,'Y-m-d'));
				
				if(strpos($redirect, "?")=== false){
					$redirect = $redirect . '?';
				}else{
					$redirect = $redirect . '&';
				}

				$redirect = $redirect . 'act=' . $act  
				 . '&orderid=' . $order->OrderId 
				 . (!empty($order->MerchantId )?'&merchantid=' . $order->MerchantId :"") 
				 . '&OrderType=' . $order->OrderType 
				 . '&OrderTypeId=' . $order->OrderTypeId 
				 . '&totalamount=' . ($order->TotalAmount *100)
				 . '&startDate=' . $startDate->format('Y-m-d')
				 . '&endDate=' . $endDate->format('Y-m-d')
				 . (!empty($numAdults)?'&numAdults=' . $numAdults:"")
				;
			}
//			$urlredirpayment = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
//			$redirect = JRoute::_('index.php?view=payment&orderId=' . $order->OrderId);
		}
		$app = JFactory::getApplication();
		$app->redirect($redirect, false);
		$app->close();
//WP->		wp_redirect($redirect);
//		exit;

	}
	
	function addToCart(){
		//clear session data from request
		BFCHelper::setSession('hdnBookingType', '', 'bfi-cart');
		BFCHelper::setSession('hdnOrderData', '', 'bfi-cart');
		
//WP->		$OrderJson = stripslashes(BFCHelper::getVar("hdnOrderData"));
		$OrderJson = (BFCHelper::getVar("hdnOrderData"));
		$bfiResetCart = (BFCHelper::getVar("bfiResetCart","0"));

		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$return = null;
		if(!empty($OrderJson)){
			$tmpUserId = BFCHelper::bfi_get_userId();
//			$currCart = BFCHelper::AddToCartByExternalUser($tmpUserId, $language, $OrderJson, $bfiResetCart);
			$currCart = BFCHelper::AddToCart($tmpUserId, $language, $OrderJson, $bfiResetCart);
			if(!empty($currCart)){
				$return = json_encode($currCart);
			}
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function DeleteFromCart(){
		$return = null;
		$CartOrderId = stripslashes(BFCHelper::getVar("bfi_CartOrderId"));
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$redirect = JURI::root();
		BFCHelper::setSession('hdnBookingType', '', 'bfi-cart');
		BFCHelper::setSession('hdnOrderData', '', 'bfi-cart');
		if(!empty($CartOrderId)){
			$tmpUserId = BFCHelper::bfi_get_userId();
			$currCart = BFCHelper::DeleteFromCartByExternalUser($tmpUserId, $language, $CartOrderId);
//WP->			$cartdetails_page = get_post( bfi_get_page_id( 'cartdetails' ) );
//			$url_cart_page = get_permalink( $cartdetails_page->ID );
//			wp_redirect($url_cart_page);
//			exit;
			$redirect = JRoute::_('index.php?option=com_bookingforconnector&view=cart');

//			if(!empty($currCart)){
//				$return = json_encode($currCart);
//			}
		}
		$app = JFactory::getApplication();
		$app->redirect($redirect, false);
		$app->close();

//WP->		$base_url = get_site_url();
//		if(defined('ICL_LANGUAGE_CODE') &&  class_exists('SitePress')){
//				global $sitepress;
//				if($sitepress->get_current_language() != $sitepress->get_default_language()){
//					$base_url = "/" .ICL_LANGUAGE_CODE;
//				}
//		}
//		wp_redirect($base_url);
//		exit;
	}

	function addDiscountCodesToCart(){		
		$bficoupons = BFCHelper::getVar("bficoupons");
		$language = BFCHelper::getVar("bfilanguage");
		$redirect = JRoute::_('index.php?option=com_bookingforconnector&view=cart');
		if(!empty($bficoupons)){
			$tmpUserId = BFCHelper::bfi_get_userId();
			$currCart = BFCHelper::AddDiscountCodesCartByExternalUser($tmpUserId, $language, $bficoupons);
		}
		$app = JFactory::getApplication();
		$app->redirect($redirect, false);
		$app->close();
	}

	public function SearchByText() {
		$return = '[]';
		$term = stripslashes(BFCHelper::getVar("bfi_term"));
		$maxresults = stripslashes(BFCHelper::getVar("bfi_maxresults"));
		$onlyLocations = stripslashes(BFCHelper::getVar("bfi_onlyLocations"));
		if(!isset($maxresults) || empty($maxresults)) {
			$maxresults = 5;
		} else {
			$maxresults = (int)$maxresults;
		}
		$language = isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		if(!empty($term)) {
//			$model = new BookingForConnectorModelSearch;
//			$results = $model->SearchResult($term, $language, $maxresults, $onlyLocations);
			$results = BFCHelper::SearchByText($term, $language, $maxresults, $onlyLocations);
			if(!empty($results)){
				$return = json_encode($results);
			}
		}
		echo $return;      
		$app = JFactory::getApplication();
		$app->close();
	}

	function searchjson(){
		bfi_setSessionFromSubmittedData();
//		$model = new BookingForConnectorModelSearch;
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('Search', 'BookingForConnectorModel');
		$items = $model->getItems(true,true);
		echo $items;	
		$app = JFactory::getApplication();
		$app->close();
	}

	function searchonselljson(){
		bfi_setSessionFromSubmittedData();
//		$model = new BookingForConnectorModelSearch;
		JModelLegacy::addIncludePath(JPATH_ROOT. DIRECTORY_SEPARATOR .'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector'. DIRECTORY_SEPARATOR . 'models', 'BookingForConnectorModel');
		$model = JModelLegacy::getInstance('SearchOnSell', 'BookingForConnectorModel');
		$items = $model->getItems(true,true);
		echo $items;	
		$app = JFactory::getApplication();
		$app->close();
	}

	function getmarketinfomerchant(){
		$resource_id=BFCHelper::getVar('merchantId');
		$language=BFCHelper::getVar('language');
		$merchant = BFCHelper::getMerchantFromServicebyId($resource_id);
		$merchantName = BFCHelper::getLanguage($merchant->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$indirizzo = isset($merchant->AddressData->Address)?$merchant->AddressData->Address:"";
		$cap = isset($merchant->AddressData->ZipCode)?$merchant->AddressData->ZipCode:""; 
		$comune = isset($merchant->AddressData->CityName)?$merchant->AddressData->CityName:"";
		$stato = isset($merchant->AddressData->StateName)?$merchant->AddressData->StateName:"";
		
		$db   = JFactory::getDBO();
		$uri  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
		$itemId = intval($db->loadResult());
		$currUriMerchant = $uri.'&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchantName);
		if ($itemId<>0)
			$currUriMerchant.='&Itemid='.$itemId;
		$routeMerchant = JRoute::_($currUriMerchant.'&fromsearch=1');

//		$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
//		$url_resource_page = get_permalink( $accommodationdetails_page->ID );

		$output = '<div class="bfi-mapdetails">
					<div class="bfi-item-title">
						<a href="'.$routeMerchant.'" target="_blank">'.$merchant->Name.'</a> 
					</div>
					<div class="bfi-item-address"><span class="street-address">'.$indirizzo .'</span>, <span class="postal-code ">'.$cap .'</span> <span class="locality">'.$comune .'</span>, <span class="region">'.$stato .'</span></div>
				</div>';    
		die($output);    	
	}

	function getmarketinforesource(){
		$resource_id=BFCHelper::getVar('resourceId');
		$language=BFCHelper::getVar('language');
		$resource = BFCHelper::GetResourcesById($resource_id);
		$merchant = $resource->Merchant;
		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$indirizzo = isset($resource->Address)?$resource->Address:"";
		$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
		$comune = isset($resource->CityName)?$resource->CityName:"";
		$stato = isset($resource->StateName)?$resource->StateName:"";
		
		$db   = JFactory::getDBO();
		$uri  = 'index.php?option=com_bookingforconnector&view=resource';
		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
		$itemId = intval($db->loadResult());
		$currUriresource = $uri.'&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName);
		if ($itemId<>0)
			$currUriresource.='&Itemid='.$itemId;
		if (!empty($resource->RateplanId)){
			 $currUriresource .= "&pricetype=" . $resource->RateplanId;
		}
		$resourceRoute = JRoute::_($currUriresource.'&fromsearch=1');

//		$accommodationdetails_page = get_post( bfi_get_page_id( 'accommodationdetails' ) );
//		$url_resource_page = get_permalink( $accommodationdetails_page->ID );

		$output = '<div class="bfi-mapdetails">
					<div class="bfi-item-title">
						<a href="'.$resourceRoute.'" target="_blank">'.$resource->Name.'</a> 
					</div>
					<div class="bfi-item-address"><span class="street-address">'.$indirizzo .'</span>, <span class="postal-code ">'.$cap .'</span> <span class="locality">'.$comune .'</span>, <span class="region">'.$stato .'</span></div>
				</div>';    
		die($output);    	
	}
	function getmarketinfocondominium(){
		$resource_id=BFCHelper::getVar('resourceId');
		$language=BFCHelper::getVar('language');
		$resource = BFCHelper::getCondominiumFromServicebyId($resource_id);
		$resourceName = BFCHelper::getLanguage($resource->Name, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		$indirizzo = isset($resource->Address)?$resource->Address:"";
		$cap = isset($resource->ZipCode)?$resource->ZipCode:""; 
		$comune = isset($resource->CityName)?$resource->CityName:"";
		$stato = isset($resource->StateName)?$resource->StateName:"";
		
		$db   = JFactory::getDBO();
		$uri  = 'index.php?option=com_bookingforconnector&view=condominium';
		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
		$itemId = intval($db->loadResult());
		$currUriresource = $uri.'&resourceId=' . $resource->CondominiumId . ':' . BFCHelper::getSlug($resourceName);
		if ($itemId<>0)
			$currUriresource.='&Itemid='.$itemId;
		$resourceRoute = JRoute::_($currUriresource.'&fromsearch=1');

//		$condominiumdetails_page = get_post( bfi_get_page_id( 'condominiumdetails' ) );
//		$url_condominium_page = get_permalink( $condominiumdetails_page->ID );
//		$routeCondominium = $url_condominium_page . $resource->CondominiumId.'-'.BFI()->seoUrl($resource->Name);

		$output = '<div class="bfi-mapdetails">
					<div class="bfi-item-title">
						<a href="'.$resourceRoute.'" target="_blank">'.$resource->Name.'</a> 
					</div>
					<div class="bfi-item-address"><span class="street-address">'.$indirizzo .'</span>, <span class="postal-code ">'.$cap .'</span> <span class="locality">'.$comune .'</span>, <span class="region">'.$stato .'</span></div>
				</div>';    
		die($output);    	
	}

	function GetServicesByIds(){
		$listsId=isset($_REQUEST['ids']) ? $_REQUEST['ids'] : '' ;  
		$language= isset($_REQUEST['language']) ? $_REQUEST['language'] : '' ;
		$return = BFCHelper::GetServicesByIds($listsId,$language);
		echo json_encode($return);      	
		$app = JFactory::getApplication();
		$app->close();
	}

	public function bfi_change_currency(){ 
		$return = "";
		if(isset($_REQUEST['bfiselectedcurrency'])){ 
			$return = bfi_set_currentCurrency($_REQUEST['bfiselectedcurrency']);
		} 
		echo json_encode($return);      	
		$app = JFactory::getApplication();
		$app->close();
	} 


}	



