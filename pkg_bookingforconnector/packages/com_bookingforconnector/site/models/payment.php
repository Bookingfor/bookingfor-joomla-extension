<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/wsQueryHelper.php';
require_once $pathbase . '/helpers/BFCHelper.php';

/**
 * BookingForConnectorModelMerchants Model
 */
class BookingForConnectorModelPayment extends JModelItem
{
	
	private $urlGetPayment = null;
	private $urlGetOrder = null;
	private $urlGetOrderPayments = null;
	private $urlGetOrderPaymentsCount = null;
	private $urlCreateOrderPayment = null;
	private $urlGetMerchantBookingTypes = null;
	private $urlGetMerchantPayment = null;
	private $urlGetLastOrderPayment = null;
			
	private $helper = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlGetPayment = '/GetMerchantBookingTypesByMerchantId';
		$this->urlGetOrder = '/GetOrder';
		$this->urlGetMerchantBookingTypes = '/GetMerchantBookingTypes';
		$this->urlGetOrderPayments = '/OrderPayments';
		$this->urlGetOrderPaymentsCount = '/OrderPayments/$count/';
		$this->urlCreateOrderPayment = '/CreateOrderPayment';
		$this->urlGetMerchantPayment = '/MerchantPayments(%d)';
		$this->urlGetLastOrderPayment = '/GetLastOrderPayment';
	}
	public function GetLastOrderPayment($orderid)
	{	
		$orderPayment= null;
		if(!empty( $orderid )){
			$data = array(
					'orderid' => $orderid,
					'$format' => 'json'
			);
			
			$options = array(
					'path' => $this->urlGetLastOrderPayment,
					'data' => $data
			);
			
			$url = $this->helper->getQuery($options);
			
			
			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->GetLastOrderPayment)){
					$orderPayment = $res->d->GetLastOrderPayment;
				}elseif(!empty($res->d)){
					$orderPayment = $res->d;
				}
			}
		}
		return $orderPayment;
	}

	public function getPaymentFromService($paymentsystemid)
	{
		$params = $this->getState('params');
		/*$paymentSystemId = $params['paymentsystemid'];*/
	
		$data = array(
				/*'$filter' => 'Enabled eq true',
				'$top' => 1,
				'$orderby' => 'IsDefault desc',*/
				'$expand' => 'PaymentSystem',
				'$format' => 'json'
		);
		
		$options = array(
				'path' => $this->urlGetPayment,
				'data' => $data
		);
		
		$url = $this->helper->getQuery($options);
		
		$paymentSystem= null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$paymentSystem = $res->d->results ?: $res->d;
		}
		
		
		return $paymentSystem;
	}
		

	public function getPaymentsFromService($cultureCode='',$merchantId='')
	{
	
		$data = array(
				'cultureCode' => '\'' . $cultureCode . '\'',
				'getAllEnabled' => 1,
				'merchantId' => $merchantId,
				'$format' => 'json'
		);
		$options = array(
				'path' => $this->urlGetPayment, 
				'data' => $data
		);
		$url = $this->helper->getQuery($options);
		
		$paymentSystems= null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$paymentSystems = $res->d->results;
			}elseif(!empty($res->d)){
				$paymentSystems = $res->d;
			}
		}
		
		
		return $paymentSystems;
	}

	public function getOrderFromService($orderid)
	{
		$data = array(
				'checkMode' => 1,
				'orderId' => $orderid,
				'$format' => 'json'
		);
		$options = array(
				'path' => $this->urlGetOrder,
				'data' => $data
		);
		$url = $this->helper->getQuery($options);
		
		$order= null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$order = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}
		
		
		return $order;
	}
	
	public function getOrderPayments($start, $limit, $orderid)
	{
		$options = array(
				'path' => $this->urlGetOrderPayments,
				'data' => array(
							'$filter' => 'OrderId eq ' . $orderid,
							'$select' => 'PaymentDate,Value,Status',
							'$format' => 'json'
						)
			);
		if (isset($start) && $start > 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}		

		$url = $this->helper->getQuery($options);
		
		$orderPayments = 0;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$orderPayments = $res->d->results;
			}elseif(!empty($res->d)){
				$orderPayments = $res->d;
			}
		}

		return $orderPayments;
	}


	public function getTotalOrderPayments($orderid)
	{
		$options = array(
				'path' => $this->urlGetOrderPaymentsCount,
				'data' => array(
							'$filter' => 'OrderId eq ' . $orderid
						)
			);
						
		$url = $this->helper->getQuery($options);
		
		$count = 0;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$count = (int)$r;
		}

		return $count;
	}


	public function setOrderPayment($orderId = NULL, $status = 0,$sendEmails = false, $amount, $bankId, $paymentData = NULL, $cultureCode = NULL, $processOrder = NULL) 
		{
		if(COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDER){
			
			$this->urlCreateOrderPayment = '/CreateOrderPaymentFrom'.COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDERSYSTEM;
		}

		$options = array(
				'path' => $this->urlCreateOrderPayment,
				'data' => array(
						'orderId' => $orderId,
						'amount' => BFCHelper::getQuotedString((string)$amount),
						'bankId' => BFCHelper::getQuotedString($bankId),
						'status' => $status,
						'sendEmails' => $sendEmails,
						'cultureCode' => BFCHelper::getQuotedString($cultureCode),
						'processOrder' => $processOrder,
						'paymentData' => BFCHelper::getQuotedString($paymentData),	
						'$format' => 'json'
				)
		);
		$url = $this->helper->getQuery($options);
	
		$order = null;
	
		//$r = $this->helper->executeQuery($url);
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
//			$order = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$order = $res->d->results;
			}elseif(!empty($res->d)){
				$order = $res->d;
			}
		}
	
		return $order;
	
	}

	protected function populateState() {
	
		$actionmode = BFCHelper::getVar('actionmode','');
		$orderId = BFCHelper::getVar('orderId');
		
		if(!isset($orderId)){
			$orderId = BFCHelper::getVar('payedOrderId');
		}
		$this->setState('params', array(
				'actionmode' => $actionmode,
				'orderId' => $orderId			
		));
		
	
		return parent::populateState();
	}
	
	public function getItem()
	{
		$cultureCode = JFactory::getLanguage()->getTag();
		if(empty($item)){
			$item=new stdClass();
		}

		$state = $this->getState();
		$params = $state->params;
		$currentMerchant = null;
		$currentbookingTypeId = null;

		if ($params['orderId']!='' && strpos($params['orderId'], "donation") === false){
			$item->order = $this->getOrderFromService($params['orderId']);
			if(empty($item->order->DepositAmount)){
				$item->order->DepositAmount = $item->order->TotalAmount;
			}
			$currentbookingTypeId= BFCHelper::getOrderMerchantPaymentId($item->order);
			$currentMerchant = $item->order->MerchantId; 
		}
		
		$payments = $this->getPaymentsFromService($cultureCode,$currentMerchant);
		$item->merchantPayments = $payments;
		$item->merchantPayment = null;
		
				
		if ($item->merchantPayments!=null){
			if(!empty($currentbookingTypeId)){
				foreach ($item->merchantPayments as $merchantPayment) {
					if ($merchantPayment->BookingTypeId==$currentbookingTypeId  && $merchantPayment->IsGateway) {
						$item->merchantPayment = $merchantPayment;
					}
				}

			}
			/* se non ho un PaymentSystemId recuperato dall'ordine, tento di prendere il sistema di pagamento di default*/
			if (empty($item->merchantPayment)){
												
//				foreach ($item->merchantPayments as $merchantPayment) {
//					if ($merchantPayment->IsDefault && $merchantPayment->MerchantId==$currentMerchant  && $merchantPayment->IsGateway) {
//						$item->merchantPayment = $merchantPayment;
//					}
//				}
				if (!empty($item->merchantPayments)){
					foreach ($item->merchantPayments as $merchantPayment) {
						if ($merchantPayment->IsDefault && $merchantPayment->IsGateway) {
							$item->merchantPayment = $merchantPayment;
						}
					}
					if (empty($item->merchantPayment)){ // se non riesco a prendere il default prendo il primo gateway...
						if ($merchantPayment->IsGateway) {
							$item->merchantPayment = $merchantPayment;
						}
					}

				}
			}
			
		}	
				
	  return $item;
	}
	
	public function getOrderMerchantPayment($order) {
		$bookingTypeId = BFCHelper::getItem($order->NotesData, 'bookingTypeId');
		if ($bookingTypeId!=''){
			$bookingType = $this->getMerchantPaymentPaymentFromService($bookingTypeId);
			return $bookingType;
		}
		return null;
	}

	public function getMerchantPaymentData($bookingTypeId)
	{
		$data = array(
				'$filter' => 'Enabled eq true',
				'$select' => 'Data',
				'$format' => 'json'
		);
		$options = array(
				'path' => sprintf($this->urlGetMerchantPayment, $bookingTypeId),
				'data' => $data
		);
		$url = $this->helper->getQuery($options);
	
		$bookingData= null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$bookingData = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$bookingData = $res->d->results;
			}elseif(!empty($res->d)){
				$bookingData = $res->d;
			}
		}
	
	
		return $bookingData;
	}

	public function getMerchantPaymentPaymentFromService($bookingTypeId)
	{
		$data = array(
				'$expand' => 'MerchantPayment',
				'$format' => 'json'
		);
		$options = array(
				'path' => sprintf($this->urlGetMerchantBookingTypes, $bookingTypeId),
				'data' => $data
		);
		$url = $this->helper->getQuery($options);
	
		$bookingType= null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$bookingType = $res->d->results ?: $res->d;
		}
	
	
		return $bookingType;
	}
	

}
