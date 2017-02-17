<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/wsQueryHelper.php';
require_once $pathbase . '/helpers/BFCHelper.php';


/*================= VirtualPay ==================================*/

class virtualpay{

	public $merchant_id;
	public $order_id;
	public $importo;
	public $separator;
	public $abi;
	public $paymentUrl;
	public $requestUrl;
	public $divisa='EUR';
	public $trackid;
	public $email;
	public $lingua;
	public $urlok;
	public $urlko;
	public $dataSend;
	public $suffixOrder;
	public $mac;
	public $items;
	public $SecretKey;
		//private $helper = null;
	public $FieldSeparator = "^";
	public $macSeparator = ";";
	
	public function __construct($config, $order, $language, $urlBack, $url, $suffixOrder='', $overrideAmount=0, $debug = FALSE)
	{
	
//	echo "<pre>order";
//	echo print_r($order);
//	echo "</pre>";
	
		//$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$paymentData = explode( '|', $config);  /* MerchantId|Abi|MacKey|Separator|PaymentUrl */
		$this->merchant_id = $paymentData[0];
		$this->abi = $paymentData[1];
		$this->SecretKey = $paymentData[2];
		$this->separator = $paymentData[3];
		$this->paymentUrl = $paymentData[4];
		$this->order_id = sprintf('B%s%s%s%s', $this->separator, $order->ExternalId, $this->separator,$order->OrderId);
		$this->email = BFCHelper::getItem($order->CustomerData, 'email')."";
		$this->lingua = $this->getLanguage($language);
		$this->importo = number_format($order->DepositAmount,2,',', '');
		//$this->order_id = $order->OrderId;
		if (isset($suffixOrder) && $suffixOrder!= "" && $overrideAmount >0 ){
			$this->order_id .= $this->separator . "R" . $suffixOrder;
			//$this->orderId .= $this->separator . "R" . $suffixOrder;
			$this->importo = $overrideAmount;
			$this->suffixOrder = $suffixOrder;
		}
		if(empty($order->ExternalId)){
			$order->ExternalId = $this->abi;
		}
		$this->urlok = $url;
		$this->urlko = $urlBack;
		$paymentDescr = "payment";
		if(isset($order->donationNotes) && !empty($order->donationNotes)){
			$paymentDescr =  $order->donationNotes;
		}
		$paymentDescr = preg_replace('/[^A-Za-z0-9\- ]/', '', $paymentDescr); ;

		if (strlen($paymentDescr>120)){
			$paymentDescr = substr($paymentDescr,0,120);
		}
		$ExternalId= $order->ExternalId;

		if (strlen($ExternalId>32)){
			$ExternalId = substr($ExternalId,0,32);
		}

		$this->items = $ExternalId. $this->FieldSeparator . $paymentDescr . $this->FieldSeparator . "1" . $this->FieldSeparator . $this->importo . $this->FieldSeparator . $this->divisa . $this->macSeparator;
		/*if ($debug){
			$this->merchant_id = 'NCHTEST';
			$this->abi = '03599';
			$this->SecretKey = 'CAE5859549B7F54834040AA110A533A1';
		}*/
		$this->mac = $this->getMac();
	}
	
	public function getMac(){
		$strMac = $this->merchant_id . $this->order_id . $this->importo . $this->divisa . $this->abi . $this->items . $this->SecretKey;
		
//		echo "<pre>strMac";
//		echo print_r($strMac);
//		echo "</pre>";
		
		$calculatedMac = md5($strMac);
		
		return strtoupper($calculatedMac);
	}

	public function getLanguage($language) {

		switch (strtolower(substr($language,0,2))) {
			case 'en':
				return 'ing';
				break;
			case 'it':
				return 'ita';
				break;
			case 'fr':
				return 'fra';
				break;
			case 'de':
				return 'ted';
				break;
			case 'es':
				return 'spa';
				break;
			default:
				return 'ing';
				break;
		}
	}
}

class virtualpayProcessor extends baseProcessor{

	public $data;
	
	public function __construct($order = false,$url = false, $debug = false)
	{

	}

	//public function getBankId($param) {
	public function getBankId() {
		
		$order_id = BFCHelper::getVar('ORDER_ID', '');
		//	explode( '|', $order_id);
		return $order_id;
		//return parent::getResult($param);;
	}
	
	//public function getAmount($param) {
	public function getAmount() {
		//$amount = BFCHelper::getFloat('Amount','');
		//converto in euro l'importo pagato
		$amount = number_format((BFCHelper::getFloat('IMPORTO')), 2, '.', '');
		return $amount;
		//return parent::getResult($param);;
	}

	//public function getResult($param) {
	public function getResult($param = null) {
		$macResponse = BFCHelper::getVar('MAC','');
		$transaction_id = BFCHelper::getVar('TRANSACTION_ID','');
		$merchant_id = BFCHelper::getVar('MERCHANT_ID','');
		$order_id = BFCHelper::getVar('ORDER_ID','');
		$cod_aut = BFCHelper::getVar('COD_AUT','');
		$importo = BFCHelper::getVar('IMPORTO','');
		$divisa = BFCHelper::getVar('DIVISA','');
		
		$calculatedMac = $transaction_id . $merchant_id . $order_id . $cod_aut . $importo . $divisa . $this->data[2];
		return (strtolower(md5($calculatedMac)) == strtolower($macResponse));
		//return parent::getResult($param);;
	}
}
