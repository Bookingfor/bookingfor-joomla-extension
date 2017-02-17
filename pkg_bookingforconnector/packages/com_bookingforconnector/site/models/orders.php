<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modellist');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/wsQueryHelper.php';
require_once $pathbase . '/helpers/BFCHelper.php';

/**
 * BookingForConnectorModelOrders Model
 */

class OrderCheckMode {     
	const OrderId = 1;
	const ExternalOrderId = 2;
	const Email = 4;
	const CustomerFirstname = 8;
	const CustomerLastname = 16;
	const CustomerId = 32;
	const ExternalCustomerId = 64;
	const CheckIn = 128;
	const CheckOut = 256;    
	// etc. }
}

class BookingForConnectorModelOrders extends JModelList
{
	private $urlCreateOrder = null;
	private $urlGetOrder = null;
	private $urlProcessOrderStatus = null;
	private $urlUpdateOrderEmail = null;
	private $urlUpdateOrderCreditCardData = null;
        private $urlGetOrdersByExternalUser = null;
        private $urlGetOrderDetailsByExternalUser = null;
        private $urlGetOrdersByExternalUserCount = null;
        private $urlGetContactData = null;
        private $urlInsertContact = null;
        private $urlUpdateContact = null;
        private $urlGetCartByExternalUser = null;
        private $urlAddToCartByExternalUser = null;
        private $urlDeleteFromCartByExternalUser = null;
			
	private $helper = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlCreateOrder = '/CreateOrder';
		$this->urlGetOrder = '/GetOrder';
		$this->urlGetOrdersByExternalUser = '/GetOrdersByExternalUser';
		$this->urlGetOrderDetailsByExternalUser = '/GetOrderDetailsByExternalUser';
		$this->urlGetOrdersByExternalUserCount = '/GetOrdersByExternalUserCount';
		$this->urlGetContactData = '/GetContactData';
		$this->urlInsertContact = '/InsertContact';
		$this->urlUpdateContact = '/UpdateContactData';
		$this->urlProcessOrderStatus = '/ProcessOrderStatus';
		$this->urlUpdateOrderEmail = '/UpdateOrderEmail';
		$this->urlUpdateOrderCreditCardData = '/UpdateOrderCreditCardData';
		$this->urlGetCartByExternalUser = '/GetCartByExternalUser';
		$this->urlAddToCartByExternalUser = '/AddToCartByExternalUser';
		$this->urlDeleteFromCartByExternalUser = '/DeleteFromCartByExternalUser';
		
	}
	
	public function GetOrdersByExternalUserCount() {
//		$uid = get_current_user_id();
//		if(empty($uid )){
//			return 0;
//		}
//		$user = get_user_by('id', $uid);
//		if (!empty($user->ID)) {
//		  $userId = $user->ID."|". $user->user_login . "|" . $_SERVER["SERVER_NAME"];
//		}
		$userId = '';
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}else{
			return 0;
		}
		$data = array(
                          'UserId' => BFCHelper::getQuotedString($userId),
			  'domainLabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
			  '$format' => 'json'
		);

		$options = array(
				'path' => $this->urlGetOrdersByExternalUserCount,
				'data' => $data
		);

		$url = $this->helper->getQuery($options);
		$orderCount = 0;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$orderCount = (int)$res->d->GetOrdersByExternalUserCount;
		}
		return $orderCount;
	}

	public function GetOrderDetailsByExternalUser($orderId) {
//                $uid = get_current_user_id();
//                $user = get_user_by('id', $uid);
//		if (!empty($user->ID)) {
//		  $userId = $user->ID."|". $user->user_login . "|" . $_SERVER["SERVER_NAME"];
//		}
		$userId = '';
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}

		$data = array(
                          'UserId' => BFCHelper::getQuotedString($userId),
                          'orderId' => $orderId,
			  'domainLabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
			  '$format' => 'json'
		);
		$options = array(
				'path' => $this->urlGetOrderDetailsByExternalUser,
				'data' => $data




		);
		$url = $this->helper->getQuery($options);
		$orderDetails = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$orderDetails = $res->d->GetOrderDetailsByExternalUser ?: $res->d;




		}
		return $orderDetails;
	}

	public function insertContact($customerData) {
		$userId = '';
		if(!empty($customerData) && isset($customerData['Email'])){
			$userId = $customerData['Email'];
		}
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}
		$options = array(
			'path' => $this->urlInsertContact,
			'data' => array(
			'customerData' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($customerData)),
			'domainlabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
			'userrefid' => BFCHelper::getQuotedString($userId),
			'$format' => 'json'
			)
		);
		$url = $this->helper->getQuery($options);
		$contact = null;

		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$contact = $res->d->results;
			} elseif(!empty($res->d)){
				$contact = $res->d;
			}
		}
		return $contact;
	}

	public function updateContact($customerData) {
		$userId = '';
		if(!empty($customerData) && isset($customerData['Email'])){
			$userId = $customerData['Email'];
		}
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}
		$options = array(
			'path' => $this->urlUpdateContact,
			'data' => array(
			'customerData' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($customerData)),
			'domainlabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
			'userrefid' => BFCHelper::getQuotedString($userId),
			'$format' => 'json'
			)
		);
		$url = $this->helper->getQuery($options);
		$contact = null;

		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$contact = $res->d->results;
			} elseif(!empty($res->d)){
				$contact = $res->d;
			}
		}
		return $contact;
	}

	public function getContactData($customerData) {
		$userId = '';
		if(!empty($customerData) && isset($customerData['Email'])){
			$userId = $customerData['Email'];
		}
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}
		$data = array(
			'userrefid' => BFCHelper::getQuotedString($userId),
			'domainLabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
			'$format' => 'json'
		);
		$options = array(
			'path' => $this->urlGetContactData,
			'data' => $data
		);
		$url = $this->helper->getQuery($options);

		$contact= null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if(isset($res->error)) {
				return NULL;
			} else {
				if (!empty($res->d->results)){
					$contact = $res->d->results;
				}elseif(!empty($res->d)){
					$contact = $res->d;
				}
				return $contact;
			}
		}
		
		return $contact;
	}

	public function setOrder($customerData = NULL, $suggestedStay = NULL, $creditCardData = NULL, $otherNoteData = NULL, $merchantId = 0, $orderType = NULL, $userNotes = NULL, $label = NULL, $cultureCode = NULL, $processOrder = NULL, $priceType = NULL) {
		if($this->getContactData($customerData[0]) == NULL) {
			$contact = $this->insertContact($customerData[0]);
		}
		else {
			$contact = $this->updateContact($customerData[0]);
		}
		$userId = '';
		if(!empty($customerData[0]) && isset($customerData[0]['Email'])){
			$userId = $customerData[0]['Email'];
		}
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}

		if (!isset($creditCardData) || empty($creditCardData)){
			$creditCardData = "";
//		}else{
//			$creditCardData =  BFCHelper::getJsonEncodeString($creditCardData);
		}
		if (!isset($otherNoteData) || empty($otherNoteData)){
			$otherNoteData = "";
		}
		if (!isset($userNotes) || empty($userNotes)){
			$userNotes = "";
		}		

//$suggestedStay = null;

		$options = array(
				'path' => $this->urlCreateOrder,
				'data' => array(
					'customerData' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($customerData)),
					'suggestedStay' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($suggestedStay)),
					'creditCardData' => BFCHelper::getQuotedString($creditCardData),
					'otherNoteData' => BFCHelper::getQuotedString($otherNoteData),
//					'merchantId' => $merchantId,
					'orderType' => BFCHelper::getQuotedString($orderType),
					'userNotes' => BFCHelper::getQuotedString($userNotes),
					'label' => BFCHelper::getQuotedString($label),
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'processOrder' => $processOrder,
					'priceType' =>  BFCHelper::getQuotedString($priceType),
					'addedBy' =>  BFCHelper::getQuotedString($userId),
					'isCartOrder' =>  1,
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
		$order = null;
		
		if(!empty($$merchantId)){
			$options['data']['merchantId'] = $merchantId;
		}


		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}

		return $order;
	}

	public function updateEmail($orderId = NULL,$email = NULL) 
		{
		$options = array(
				'path' => $this->urlUpdateOrderEmail,
				'data' => array(
						'orderId' => $orderId,
						'email' => BFCHelper::getQuotedString($email),
						'$format' => 'json'
				)
		);
		$url = $this->helper->getQuery($options);
	
		$order = null;
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}
		return $order;
	}
	
	public function updateCCdata($orderId = NULL,$creditCardData = NULL, $processOrder=null) 
		{
		$options = array(
				'path' => $this->urlUpdateOrderCreditCardData,
				'data' => array(
						'orderId' => $orderId,
						'creditCard' => BFCHelper::getQuotedString($creditCardData),
						'processOrder' => $processOrder,
						'$format' => 'json'
				)
		);
		$url = $this->helper->getQuery($options);
	
		$order = null;
	
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}
	
		return $order;
	
	}

	public function setOrderStatus($orderId = NULL, $status = NULL, $sendEmails = false, $setAvailability = false, $paymentData = NULL) 
		{
		$options = array(
				'path' => $this->urlProcessOrderStatus,
				'data' => array(
						'orderId' => $orderId,
						'status' => $status,
						'sendEmails' => $sendEmails,
						'setAvailability' => $setAvailability,
						'paymentData' => BFCHelper::getQuotedString($paymentData),	
						'$format' => 'json'
				)
		);
		$url = $this->helper->getQuery($options);
	
		$order = null;
	
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}

		return $order;
	
	}
	
	
	public function GetOrdersByExternalUser() {
//		$uid = get_current_user_id();
//		$user = get_user_by('id', $uid);
//		if (empty($user->ID)) return null;
//		
//		if (!empty($user->ID)) {
//			$userId = $user->ID."|". $user->user_login . "|" . $_SERVER["SERVER_NAME"];
//		}
		$userId = '';
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}else{
			return null;
		}

		if(isset($_GET['pager'])) {
			$skip = 5 * $_GET['pager'];
		}
		else {
			$skip = 0;
		}

		$data = array(
			'UserId' => BFCHelper::getQuotedString($userId),
			'domainLabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
			'top' => 5,
			'skip' => $skip,
			'$format' => 'json'
		);
		$options = array(
			'path' => $this->urlGetOrdersByExternalUser,
			'data' => $data
		);
		$url = $this->helper->getQuery($options);

		$order= null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}


		return $order;
	}
	
	public function getSingleOrderFromService($orderId = null) {
		$order= null;
		if(!empty($orderId )){
		$data = array(
					'checkMode' => OrderCheckMode::OrderId,
					'orderId' => $orderId,
					'$format' => 'json'
				);
		$options = array(
				'path' => $this->urlGetOrder,
				'data' => $data
		);
		$url = $this->helper->getQuery($options);
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}
		}
		
		return $order;
	}

	public function getOrderFromService($orderId = null)
	{

		$params = $this->getState('params');
		$checkmode = $params['checkmode'];
		$donation = $params['donation'];
		
		$data = array(
					'checkMode' => $checkmode,
					'$format' => 'json'
				);
		
		if ($checkmode & OrderCheckMode::OrderId){
				$orderId = $params['orderId'];
				$data['orderId'] = $orderId;			
		}
		if ($checkmode & OrderCheckMode::ExternalOrderId){
			$externalOrderId = $params['externalOrderId'];
			$data['externalOrderId'] = BFCHelper::getQuotedString($externalOrderId);
		}	
		if ($checkmode & OrderCheckMode::CustomerId){
			$customerId = $params['customerId'];
			$data['customerId'] = $customerId;
		}
		if ($checkmode & OrderCheckMode::ExternalCustomerId){
			$externalCustomerId = $params['externalCustomerId'];
			$data['externalCustomerId'] = BFCHelper::getQuotedString($externalCustomerId);
		}	
		if ($checkmode & OrderCheckMode::CheckIn){
			$checkIn = $params['checkIn'];
			$data['checkIn'] = BFCHelper::getQuotedString($checkIn);
		}
		if ($checkmode & OrderCheckMode::CheckOut){
			$checkOut = $params['checkOut'];
			$data['checkOut'] = BFCHelper::getQuotedString($checkOut);
		}		
		if ($checkmode & OrderCheckMode::CustomerFirstname){
			$customerFirstname = $params['customerFirstname'];
			$data['customerFirstname'] = BFCHelper::getQuotedString($customerFirstname);
		}
		if ($checkmode & OrderCheckMode::CustomerLastname){
			$customerLastname = $params['customerLastname'];
			$data['customerLastname'] = BFCHelper::getQuotedString($customerLastname);
		}
		if ($checkmode & OrderCheckMode::Email){
			$email = $params['email'];
			$data['email'] = BFCHelper::getQuotedString($email);
		}		
		
		if(COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDER){
			
			$this->urlGetOrder = '/GetOrderFrom'.COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDERSYSTEM;
			$data['cultureCode'] = BFCHelper::getQuotedString($params['cultureCode']);
			$data['merchantCode'] = BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_EXTERNALMERCHANTCODE);

		}

		$options = array(
				'path' => $this->urlGetOrder,
				'data' => $data
		);
		$url = $this->helper->getQuery($options);
		
		$order= null;
				
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}

		
		return $order;
	}

	public function GetCartByExternalUser($userId, $culturecode, $includeDetails = true) {
//		$uid = get_current_user_id();
//		$user = get_user_by('id', $uid);
////		if (empty($user->ID) || empty($userId) ) return null;
//
//		if (!empty($user->ID)) {
//			$userId = $user->ID."|". $user->user_login . "|" . $_SERVER["SERVER_NAME"];
//		}
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}
		$data = array(
			'UserId' => BFCHelper::getQuotedString($userId),
			'domainLabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
			'culturecode' => BFCHelper::getQuotedString($culturecode),
			'includeDetails' => $includeDetails?1:0,
			'$format' => 'json'
		);
		$options = array(
			'path' => $this->urlGetCartByExternalUser,
			'data' => $data
		);
		$url = $this->helper->getQuery($options);

		$order= null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}


		return $order;
	}

	public function AddToCartByExternalUser($userId, $culturecode, $cartDetails) {
//		$uid = get_current_user_id();
//		$user = get_user_by('id', $uid);
////		if (empty($user->ID) || empty($userId) ) return null;
//
//		if (!empty($user->ID)) {
//			$userId = $user->ID."|". $user->user_login . "|" . $_SERVER["SERVER_NAME"];
//		}
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}
		$data = array(
			'UserId' => BFCHelper::getQuotedString($userId),
			'domainLabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
			'culturecode' => BFCHelper::getQuotedString($culturecode),
			'cartDetails' => BFCHelper::getQuotedString($cartDetails),
			'$format' => 'json'
		);
		$options = array(
			'path' => $this->urlAddToCartByExternalUser,
			'data' => $data
		);
		$url = $this->helper->getQuery($options);

		$orders= null;

		$r = $this->helper->executeQuery($url,'POST');
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->AddToCartByExternalUser)){
				$orders = $res->d->AddToCartByExternalUser;
			}elseif(!empty($res->d)){
				$orders = $res->d;
			}
		}


		return $orders;
	}


	public function DeleteFromCartByExternalUser($userId, $culturecode, $cartOrderId) {
//		$uid = get_current_user_id();
//		$user = get_user_by('id', $uid);
////		if (empty($user->ID) || empty($userId) ) return null;
//
//		if (!empty($user->ID)) {
//			$userId = $user->ID."|". $user->user_login . "|" . $_SERVER["SERVER_NAME"];
//		}
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}

		$data = array(
			'UserId' => BFCHelper::getQuotedString($userId),
			'domainLabel' => BFCHelper::getQuotedString(COM_BOOKINGFORCONNECTOR_FORM_KEY),
			'culturecode' => BFCHelper::getQuotedString($culturecode),
			'cartOrderId' => BFCHelper::getQuotedString($cartOrderId),
			'$format' => 'json'
		);
		$options = array(
			'path' => $this->urlDeleteFromCartByExternalUser,
			'data' => $data
		);
		$url = $this->helper->getQuery($options);

		$orders= null;

		$r = $this->helper->executeQuery($url,'POST');
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->DeleteFromCartByExternalUser)){
				$orders = $res->d->DeleteFromCartByExternalUser;
			}elseif(!empty($res->d)){
				$orders = $res->d;
			}
		}


		return $orders;
	}


	
	protected function populateState($ordering = NULL, $direction = NULL) {		
		
		$this->setState('params', array(

				'donation' => BFCHelper::getVar('donation',0),
				'checkmode' => BFCHelper::getVar('checkmode',BFCHelper::getDefaultCheckMode()),
				'orderId' => BFCHelper::getVar('orderId'),
				'externalOrderId' => BFCHelper::getVar('externalOrderId'),
				'email' => BFCHelper::getVar('email'),
				'customerFirstname' => BFCHelper::getVar('customerFirstname'),
				'customerLastname' => BFCHelper::getVar('customerLastname'),
				'customerId' => BFCHelper::getVar('customerId'),
				'externalCustomerId' => BFCHelper::getVar('externalCustomerId'),
				'checkIn' => BFCHelper::getVar('checkIn'),
				'checkOut' => BFCHelper::getVar('checkOut'),				
				'cultureCode' => BFCHelper::getVar('cultureCode')				
		));

		return parent::populateState($ordering, $direction);
	}
	
	public function getItem()
	{
		// Get a storage key.
		$store = $this->getStoreId();
		
		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}
		
		$item = $this->getOrderFromService();
		
		// Add the items to the internal cache.
		$this->cache[$store] = $item;
		
		return $this->cache[$store];
		
	}
	
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$items = $this->getOrdersFromService(
			$this->getStart(), 
			$this->getState('list.limit'), 
			$this->getState('list.ordering', 'Name'), 
			$this->getState('list.direction', 'asc')
		);

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
}
