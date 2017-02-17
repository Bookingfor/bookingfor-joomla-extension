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


/*================= SETEFI ==================================*/

class setefi{

	public $merchantNumber;
	public $separator;
	public $password;
	public $paymentUrl = 'https://www.monetaonline.it/monetaweb/hosted/init/http';
	public $currencycode='978';
	public $action='4';
	public $trackid;
	public $email;
	public $langid;
	public $amt;
	public $responseUrl;
	public $errorUrl;
	public $udf1;
	public $udf2;
	public $udf3;
	public $udf4;
	public $udf5;
	public $requestUrl;
	public $dataSend;
	public $orderId;
	public $suffixOrder;
	public $isdonation;
		//private $helper = null;
	
	public function __construct($config, $order, $language, $urlBack, $url, $suffixOrder, $overrideAmount=0, $debug = false, $donation = false)
	{
		//$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$paymentData = explode( '|', $config);  /*Separator|MerchantNumber|SecretString */
		$this->merchantNumber = $paymentData[1];
		$this->separator = $paymentData[0];
		$this->password = $paymentData[2];
		$this->trackid = sprintf('B%s%s%s%s', $this->separator, $order->ExternalId, $this->separator,$order->OrderId);
		$this->email = BFCHelper::getItem($order->CustomerData, 'email')."";
		$this->langid = $this->getLanguage($language);
		$this->amt = $order->DepositAmount;
		$this->udf1 = $order->OrderId;
		$this->orderId = $order->OrderId;
		if (isset($suffixOrder) && $suffixOrder!= "" && $overrideAmount >0 ){
			$this->trackid .= $this->separator . "R" . $suffixOrder;
			//$this->orderId .= $this->separator . "R" . $suffixOrder;
			$this->udf1 .= $this->separator . "R" . $suffixOrder;
			$this->amt = $overrideAmount;
			$this->udf4 =  $overrideAmount;
			$this->suffixOrder = $suffixOrder;
		}
		$this->isdonation = $donation;
		if($this->isdonation){
			$this->udf1 = sprintf('%s %s', $order->ExternalId, BFCHelper::getItem($order->CustomerData, 'nome'));
			$this->trackid = $order->ExternalId;
		}


		$this->responseUrl = $url;
		$this->errorUrl = $urlBack;

		if ($debug){
			$this->merchantNumber = '99999999';
			$this->password = '99999999';
			$this->paymentUrl = 'https://test.monetaonline.it/monetaweb/hosted/init/http';
		}

		$this->requestUrl = $this->getUrl();
	}

	public function getLanguage($language) {

		switch (strtolower(substr($language,0,2))) {
			case 'it':
				return 'ITA';
				break;
			case 'en':
				return 'USA';
				break;
			case 'de':
				return 'DEU';
				break;
			case 'es':
				return 'SPA';
				break;
			case 'fr':
				return 'FRA';
				break;
			default:
				return '';
				break;
		}
	}

	public function getUrl(){
		$bankUrl = '';
		$data = 'id=' . $this->merchantNumber . '&password=' . $this->password . 
		'&action=' . $this->action . '&amt=' . number_format($this->amt, 2, '.', '') . '&currencycode=' . $this->currencycode . 
		'&langid='. $this->langid . '&responseurl=' . $this->responseUrl .
		'&errorurl='.$this->errorUrl.'&trackid='.$this->trackid.'&udf1='.$this->udf1.'&udf4='.$this->udf4;
		
		$this->dataSend = $this->paymentUrl . "?" . $data;
		
		$buffer = wsQueryHelper::executeQuery($this->dataSend,"POST",false);
		$token=explode(":",$buffer,2);
		$tid=$token[0];
		$paymenturl=$token[1];
		
		
		$bankUrl = $paymenturl . "?PaymentID=" . $tid;
		/*salvo i dati inviati alla banca e cosa mi ritorna la banca come url di invio*/
				$this->donation = $donation;
		
		if(!$this->isdonation){
			if (isset($this->suffixOrder) && $this->suffixOrder!= ""  ){
				BFCHelper::setOrderStatus($this->orderId,null,false,false,$data . '&bankUrl=' . $bankUrl . '&paymentUrl=' .$this->paymentUrl);
			} else{
				BFCHelper::setOrderStatus($this->orderId,1,false,false,$data . '&bankUrl=' . $bankUrl . '&paymentUrl=' .$this->paymentUrl);
			}
		}
	
		return $bankUrl;
	}
}
class setefiServer extends setefi{

}
class setefiServerProcessor extends baseProcessor{
	public function __construct($order = false,$url = false, $debug = false)
	{
	
	}
	
	public function getBankId($param) {
		$bankId = BFCHelper::getVar('trackid','');
		return $bankId ;
		//return parent::getResult($param);;
	}
	public function getAmount($param) {
		//$amount = BFCHelper::getFloat('Amount','');
		//converto in euro l'importo pagato
		$amount =  number_format((BFCHelper::getFloat('udf4')), 2, '.', '');
		return $amount;
		//return parent::getResult($param);;
	}
	
	public function getResult($param = NULL) {
		$esito = BFCHelper::getVar('responsecode','');
		return (strtolower($esito)=='00' || strtolower($esito)=='000');
		//return parent::getResult($param);;
	}
	public function responseRedir($msg = '',$result='')
	{
		if (empty($msg)){
			$msg = "0";
		}

		$uri                    = JURI::getInstance();
		$urlBase = $uri->toString(array('scheme', 'host', 'port'));
		$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=setefi&payedOrderId=' . $msg . '&result=' . $result);
	
		echo 'redirect=' . $url;
		jexit();
	
	}
	
}
class setefiProcessor extends baseProcessor{
	public function __construct($order = false,$url = false, $debug = false)
	{

	}

	public function getBankId($param) {
		$bankId = BFCHelper::getVar('trackid','');
		return $bankId ;
		//return parent::getResult($param);;
	}
	public function getAmount($param) {
		//$amount = BFCHelper::getFloat('Amount','');
		//converto in euro l'importo pagato
		$amount =  number_format((BFCHelper::getFloat('udf4')), 2, '.', '');
		return $amount;
		//return parent::getResult($param);;
	}

	public function getResult($param = NULL) {
		$esito = BFCHelper::getVar('result','');
		return (strtolower($esito)=='1' );
		//return parent::getResult($param);;
	}
}
/*================= END SETEFI ==================================*/

