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


/*================= Activa ==================================*/

class activa{

	public $id;
	public $separator;
	public $password;
	public $paymentUrl;
	public $currencycode='978';
	public $action='1';
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
		//private $helper = null;
	
	public function __construct($config, $order, $language, $urlBack, $url, $suffixOrder, $overrideAmount=0, $debug = FALSE)
	{
		//$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$paymentData = explode( '|', $config);  /*id|password|separator|PaymentUrl */
		$this->id = $paymentData[0];
		$this->separator = $paymentData[2];
		$this->password = $paymentData[1];
		$this->paymentUrl = $paymentData[3];
		$this->trackid = sprintf('B%s%s%s%s', $this->separator, $order->ExternalId, $this->separator,$order->OrderId);
		$this->email = BFCHelper::getItem($order->CustomerData, 'email')."";
		$this->langid = $this->getLanguage($language);
		$this->amt = $order->DepositAmount;
		$this->udf2 = $order->OrderId;
		$this->orderId = $order->OrderId;
		if (isset($suffixOrder) && $suffixOrder!= "" && $overrideAmount >0 ){
			$this->trackid .= $this->separator . "R" . $suffixOrder;
			//$this->orderId .= $this->separator . "R" . $suffixOrder;
			$this->udf2 .= $this->separator . "R" . $suffixOrder;
			$this->amt = $overrideAmount;
			$this->udf4 =  $overrideAmount;
			$this->suffixOrder = $suffixOrder;
		}

		$this->responseUrl = $url;
		$this->errorUrl = $urlBack;

		if ($debug){
			//$this->merchantNumber = '99999999';
			//$this->password = '99999999';
			$this->paymentUrl = 'https://test4.constriv.com/cg301/servlet/PaymentInitHTTPServlet';
		}

		$this->requestUrl = $this->getUrl();
	}

	public function getLanguage($language) {

		switch (strtolower(substr($language,0,2))) {
			case 'sl':
				return 'SLO';
				break;
			case 'en':
				return 'USA';
				break;
			case 'sr':
				return 'SRB';
				break;
			case 'it':
				return 'ITA';
				break;
			case 'fr':
				return 'FRA';
				break;
			case 'de':
				return 'DEU';
				break;
			case 'es':
				return 'ESP';
				break;
			default:
				return '';
				break;
		}
	}

	public function getUrl(){
		$bankUrl = '';
		$data = 'id=' . $this->id . '&password=' . $this->password . 
		'&action=' . $this->action . '&amt=' . number_format($this->amt, 2, '.', '') . '&currencycode=' . $this->currencycode . 
		'&langid='. $this->langid . '&responseUrl=' . $this->responseUrl .
		'&errorUrl='.$this->errorUrl.'&trackid='.$this->trackid.'&udf1='.$this->udf1.
		'&udf2='.$this->udf2.'&udf3='.$this->udf3.'&udf4='.$this->udf4.'&udf5='.$this->udf5;
		
		$this->dataSend = $this->paymentUrl . "?" . $data;
		
		$buffer = wsQueryHelper::executeQuery($this->dataSend,"POST",false);
		$token=explode(":",$buffer,2);
		$tid=$token[0];
		$paymenturl=$token[1];
		
		
		$bankUrl = $paymenturl . "?PaymentID=" . $tid;
		/*salvo i dati inviati alla banca e cosa mi ritorna la banca come url di invio*/
		if (isset($this->suffixOrder) && $this->suffixOrder!= ""  ){
			BFCHelper::setOrderStatus($this->orderId,null,false,false,$data . '&bankUrl=' . $bankUrl . '&paymentUrl=' .$this->paymentUrl);
		} else{
			BFCHelper::setOrderStatus($this->orderId,1,false,false,$data . '&bankUrl=' . $bankUrl . '&paymentUrl=' .$this->paymentUrl);
		}

		return $bankUrl;
	}
}
class activaServer extends activa{

}
class activaServerProcessor extends baseProcessor{
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
		/*$esito = BFCHelper::getVar('responsecode','');
		return (strtolower($esito)=='00' || strtolower($esito)=='000');*/
		$esito = BFCHelper::getVar('result','');
		return (strtolower($esito)=='approved' || strtolower($esito)=='captured');
		//return parent::getResult($param);;
	}
	public function responseRedir($msg = '',$result='')
	{
		$uri                    = &JURI::getInstance();
		$urlBase = $uri->toString(array('scheme', 'host', 'port'));
		$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=activa&payedOrderId=' . $msg . '&result=' . $result);
	
		echo 'redirect=' . $url;
		jexit();
	
	}
	
}
class activaProcessor extends baseProcessor{
	public function __construct($order = false,$url = false, $debug = false)
	{

	}

	//public function getBankId($param) {
	public function getBankId() {
		$bankId = BFCHelper::getVar('trackid','');
		return $bankId ;
		//return parent::getResult($param);;
	}
	
	//public function getAmount($param) {
	public function getAmount() {
		//$amount = BFCHelper::getFloat('Amount','');
		//converto in euro l'importo pagato
		$amount =  number_format((BFCHelper::getFloat('udf4')), 2, '.', '');
		return $amount;
		//return parent::getResult($param);;
	}

	//public function getResult($param) {
	public function getResult($param = NULL) {
		$esito = BFCHelper::getVar('result','');
		return (strtolower($esito)=='1' );
		//return parent::getResult($param);;
	}
}
