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


/*================= Bankart ==================================*/

class bankart{

	public $id;
	public $separator;
	public $password;
	public $passwordhash;
	public $paymentUrl;
	public $currencycode='978';
	public $action='4'; // 1: Purchase 4: Authorization
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
	public $servletName='PaymentInitHTTPServlet';
	public $webAddress;
	public $port;
	public $context;

	private $helper = null;
	
	public function __construct($config, $order, $language, $urlBack, $url, $suffixOrder, $overrideAmount=0, $debug = FALSE)
	{
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$paymentData = explode( '|', $config);  /*Terminal ID|password|passwordhash|webAddress|port|context|Separator (x)   id|password|separator|PaymentUrl */
		$this->id = $paymentData[0];
		$this->password = $paymentData[1];
		$this->passwordhash = $paymentData[2];
		$this->webAddress = $paymentData[3];
		$this->port = $paymentData[4];
		$this->context = $paymentData[5];
//		$this->paymentUrl = 'https://' . $this->webAddress . ":" . $this->port . "/" . $this->context . "/servlet/PaymentInitHTTPServlet";

		$urlBuf ="";

		if($this->port == "443" )
            $urlBuf = "https://";
        else
            $urlBuf = "http://";
        $urlBuf = $urlBuf . $this->webAddress;
        if(strlen($this->port) > 0)
            $urlBuf = $urlBuf . ":" . $this->port;
        if(strlen($this->context) > 0)
        {
            if ($this->context[0] != "/")
                $urlBuf = $urlBuf . "/";
            $urlBuf = $urlBuf . $this->context;
            if (!$this->context[strlen($this->context)-1] != "/")
                $urlBuf = $urlBuf . "/";
        }
        else
        {
                $urlBuf = $urlBuf . "/";
        }
        $urlBuf = $urlBuf . "servlet/" . $this->servletName;
		
		$this->paymentUrl = $urlBuf;
		
		$this->separator = $paymentData[6];
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
			$this->paymentUrl = 'https://Tebank.bankart.si:443/Testgw31/servlet/PaymentInitHTTPServlet';
		}

		$this->requestUrl = $this->getUrl();
	}

	public function getLanguage($language) {

		switch (strtolower(substr($language,0,2))) {
			case 'bs':
				return 'BS';
				break;
			case 'cz':
				return 'CZ';
				break;
			case 'de':
				return 'DE';
				break;
			case 'es':
				return 'ESP';
				break;
			case 'hr':
				return 'HR';
				break;
			case 'hu':
				return 'HU';
				break;
			case 'it':
				return 'IT';
				break;
			case 'ru':
				return 'RUS';
				break;
			case 'sl':
				return 'SI';
				break;
			case 'sk':
				return 'SVK';
				break;
			case 'sr':
				return 'SR';
				break;
			case 'en':
				return 'US';
				break;
//			case 'fr':
//				return 'FRA';
//				break;
			default:
				return 'US';
				break;
		}
	}

	public function getUrl(){
		$bankUrl = '';
//		$data = 'id=' . $this->id 
//			. '&password=' . $this->password 
//			.'&action=' . $this->action 
//			. '&amt=' . number_format($this->amt, 2, '.', '') 
//			. '&currencycode=' . $this->currencycode 
//			. '&langid='. $this->langid 
//			. '&responseUrl=' . $this->responseUrl 
//			. '&errorUrl='.$this->errorUrl
//			.'&trackid='.$this->trackid
//			.'&udf1='.$this->udf1
//			.'&udf2='.$this->udf2
//			.'&udf3='.$this->udf3
//			.'&udf4='.$this->udf4
//			.'&udf5='.$this->udf5;
        $buf = "";
		if(strlen($this->id) > 0)
            $buf = $buf . "id=" . $this->id . "&";
        if(strlen($this->password) > 0)
            $buf = $buf . "password=" . $this->password . "&";
        if(strlen($this->passwordhash) > 0)
            $buf = $buf . "passwordhash=" . $this->passwordhash . "&";
        if(strlen($this->amt) > 0)
            $buf = $buf . "amt=" . number_format($this->amt, 2, '.', '') . "&";
        if(strlen($this->currencycode) > 0)
            $buf = $buf . "currencycode=" . $this->currencycode . "&";
        if(strlen($this->action) > 0)
            $buf = $buf . "action=" . $this->action . "&";
        if(strlen($this->langid) > 0)
            $buf = $buf . "langid=" . $this->langid . "&";
        if(strlen($this->responseUrl) > 0)
            $buf = $buf . "responseURL=" . $this->responseUrl . "&";
        if(strlen($this->errorUrl) > 0)
            $buf = $buf . "errorURL=" . $this->errorUrl . "&";
        if(strlen($this->trackid) > 0)
            $buf = $buf . "trackid=" . $this->trackid . "&";
        if(strlen($this->udf1) > 0)
            $buf = $buf . "udf1=" . $this->udf1 . "&";
        if(strlen($this->udf2) > 0)
            $buf = $buf . "udf2=" . $this->udf2 . "&";
        if(strlen($this->udf3) > 0)
            $buf = $buf . "udf3=" . $this->udf3 . "&";
        if (strlen($this->udf4) > 0)
            $buf = $buf . "udf4=" . $this->udf4 . "&";
        if (strlen($this->udf5) > 0)
            $buf = $buf . "udf5=" . $this->udf5;
		if (strrpos($buf,"&") == strlen($buf)-1)
			$buf = substr($buf,0, strlen($buf)-1);
		$data = $buf;

		$this->dataSend = $this->paymentUrl . "?" . $data;
		

		
		$buffer = $this->helper->executeQuery($this->dataSend,"POST",false);
		$token=explode(":",$buffer,2);
		$tid=$token[0];
		$paymenturl=$token[1];
		
		
		$bankUrl = $paymenturl . "?PaymentID=" . $tid;
		/*salvo i dati inviati alla banca e cosa mi ritorna la banca come url di invio*/
		if (isset($this->suffixOrder) && $this->suffixOrder!= ""  ){
//			BFCHelper::setOrderStatus($this->orderId,null,false,false,$data . '&bankUrl=' . $bankUrl . '&paymentUrl=' .$this->paymentUrl);
		} else{
			BFCHelper::setOrderStatus($this->orderId,1,false,false,$data . '&bankUrl=' . $bankUrl . '&paymentUrl=' .$this->paymentUrl);
		}

		return $bankUrl;
	}
}
class bankartServer extends bankart{

}
class bankartServerProcessor extends baseProcessor{
	public function __construct($order = false,$url = false, $debug = false)
	{
	
	}
	
	public function getBankId($param = null) {
		$bankId = BFCHelper::getVar('trackid','');
		return $bankId ;
		//return parent::getResult($param);;
	}
	public function getAmount($param = null) {
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
		$uri = JURI::getInstance();
		$urlBase = $uri->toString(array('scheme', 'host', 'port'));
		$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=bankart&payedOrderId=' . $msg . '&result=' . $result);
		echo 'REDIRECT=' . $url;
		jexit();
	
	}
	
}
class bankartProcessor extends baseProcessor{
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
