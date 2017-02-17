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


/*================= bnlpositivity ==================================*/

class bnlpositivity{

	public $storename;
	public $ksig;
	public $order_id;
	public $importo;
	public $currentDateTime;
	public $separator;
	public $paymentUrl;
	public $requestUrl;
	public $divisa='EUR';
	public $email;
	public $language;
	public $responseSuccessURL;
	public $responseFailURL;
	public $suffixOrder;
	public $hash;
	public $info;
	public $FieldSeparator = " ";
	public $invoicenumber = " ";
	public $txntype = "PURCHASE";

	public function __construct($config, $order, $language, $urlBack, $url, $suffixOrder, $overrideAmount=0, $debug = FALSE)
	{
	
//	echo "<pre>order";
//	echo print_r($order);
//	echo "</pre>";
	
		//$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$paymentData = explode( '|', $config);  /* storename|ksig|Separator|PaymentUrl */
		$this->storename = $paymentData[0];
		$this->ksig = $paymentData[1];
		$this->separator = $paymentData[2];
		$this->paymentUrl = $paymentData[3];
		$this->order_id = sprintf('B%s%s%s%s', $this->separator, $order->ExternalId, $this->separator,$order->OrderId);
		$this->email = BFCHelper::getItem($order->CustomerData, 'email')."";
		$this->language = $this->getLanguage($language);
		$this->importo = number_format($order->DepositAmount,2,'.', '');
		$this->currentDateTime = date("Y:m:d-H:i:s");
		//$this->order_id = $order->OrderId;
		if (isset($suffixOrder) && $suffixOrder!= "" && $overrideAmount >0 ){
			$this->order_id .= $this->separator . "R" . $suffixOrder;
			//$this->orderId .= $this->separator . "R" . $suffixOrder;
			$this->importo = number_format($overrideAmount,2,'.', '');
			$this->suffixOrder = $suffixOrder;
		}
		$this->responseSuccessURL = $url;
		$this->responseFailURL = $urlBack;
		
		$paymentDescr = "payment";
		if(isset($order->donationNotes) && !empty($order->donationNotes)){
			$paymentDescr =  $order->donationNotes;
		}
		$paymentDescr = preg_replace('/[^A-Za-z0-9\- ]/', '', $paymentDescr); ;

		if (strlen($paymentDescr>120)){
			$paymentDescr = substr($paymentDescr,0,120);
		}
		
		if(empty($order->ExternalId)){
			$this->invoicenumber= $order->ExternalId;
		}

		if (strlen($this->invoicenumber>32)){
			$this->invoicenumber = substr($this->invoicenumber,0,32);
		}

		$this->info = $this->invoicenumber . $this->FieldSeparator . $paymentDescr . $this->FieldSeparator . "1" . $this->FieldSeparator . $this->importo . $this->FieldSeparator . $this->divisa;
		/*if ($debug){
			$this->merchant_id = 'NCHTEST';
			$this->abi = '03599';
			$this->SecretKey = 'CAE5859549B7F54834040AA110A533A1';
		}*/
		$this->hash = $this->getHash();
	}
	
	public function getHash(){
		$stringToHash = $this->storename .$this->currentDateTime . $this->importo . $this->divisa . $this->ksig;
		
//		echo "<pre>strHash";
//		echo print_r($strHash);
//		echo "</pre>";
		
		$ascii = bin2hex($stringToHash);
		return sha1($ascii);

	}

	public function getLanguage($language) {

		switch (strtolower(substr($language,0,2))) {
			case 'en':
				return 'EN';
				break;
			case 'it':
				return 'IT';
				break;
			case 'de':
				return 'DE';
				break;
			case 'fr':
				return 'EN';
				break;
			case 'es':
				return 'EN';
				break;
			default:
				return 'EN';
				break;
		}
	}
}

class bnlpositivityProcessor extends baseProcessor{

	public $data;
	public $order;
	
	public function __construct($order = false,$url = false, $debug = false)
	{

	}

	//public function getBankId($param) {
	public function getBankId() {
		
		$order_id = BFCHelper::getVar('oid', '');
		//	explode( '|', $order_id);
		return $order_id;
		//return parent::getResult($param);;
	}
	
	//public function getAmount($param) {
	public function getAmount() {
		//$amount = BFCHelper::getFloat('Amount','');
		//converto in euro l'importo pagato
		$amount = number_format((BFCHelper::getFloat('chargetotal')), 2, '.', '');
		return $amount;
		//return parent::getResult($param);;
	}

	//public function getResult($param) {
	public function getResult($param = null) {
		$esito = BFCHelper::getVar('status','');

		
		$this->storename = $this->data[0];
		$this->ksig = $this->data[1];
		$this->separator = $this->data[2];
		$this->paymentUrl = $this->data[3];


		$order_id = BFCHelper::getVar('oid','');
		$txndatetime = BFCHelper::getVar('addInfo4','');
		$importo = $this->getAmount();
		$divisa = BFCHelper::getVar('currency','');
		$approval_code = BFCHelper::getVar('approval_code','');
		$hashResponse = BFCHelper::getVar('response_hash','');
		$refnumber = BFCHelper::getVar('refnumber','');
		
		$calculatedhash = $this->ksig  . $approval_code . $importo . $divisa . $txndatetime. $this->storename;
		
		return (strtolower(sha1(bin2hex($calculatedhash))) == strtolower($hashResponse) && (strtolower($esito)=='approvato' || strtolower($esito)=='approved' || strtolower($esito)=='genehmigt'));
		//return parent::getResult($param);;
	}
}
