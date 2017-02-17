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


/*================= PAYPALEXPRESS ==================================*/

class paypalExpress{
	
	public $alias;
	public $separator;
	public $startSecretKey;
	public $paymentUrl;
	public $divisa = 'EUR';
	public $numord;
	public $email;	
	public $languageId;
	public $importo;	
	public $urlBack;
	public $url;
	public $isdonation;
		
	public function __construct($config, $order, $language, $urlBack, $url, $debug = false, $donation = false)
	{
		$paymentData = explode( '|', $config);  /*Username|Password|Signature|Separator */
		$this->Username = $paymentData[0];
		$this->Password = $paymentData[1];
		$this->Signature = $paymentData[2];
		$this->separator = $paymentData[3];
		$this->divisa = 'EUR';
		$this->numord = sprintf('B%s%s%s%s', rand(1, 9999) . $this->separator, $order->ExternalId, $this->separator,$order->OrderId);
		$this->email = BFCHelper::getItem($order->CustomerData, 'email')."";
		$this->languageId = $this->getLanguage($language);
		$this->importo = (float)$order->DepositAmount;
		
		$this->paymentUrl = 'https://www.paypal.com/cgi-bin/webscr';
		$this->paymentUrlAPI = 'https://api-3t.paypal.com/nvp';
		$this->returnurl = $url;
		$this->urlBack = $urlBack;
		$this->method = "SetExpressCheckout";
		$this->version = "109.0";
		$this->paymentaction = "Sale";

		if ($debug){
			$this->paymentUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			$this->paymentUrlAPI = 'https://api-3t.sandbox.paypal.com/nvp';
		}
		//$this->requestUrl = $this->getUrl();
		
	}
	
	public function getLanguage($language) {
		
		switch (strtolower(substr($language,0,2))) {
			case 'it':
				return 'IT';
				break;
			case 'en':
				return 'US';
				break;
			case 'de':
				return 'DE';			
				break;
			case 'es':
				return 'ES';
				break;
			case 'fr':
				return 'FR';
				break;
			default:
				return 'US';
				break;
		}
	}

	public function getUrl(){

		$nvp = array(
			'PAYMENTREQUEST_0_AMT'				=> number_format($this->importo, 2, '.', ''),
			'PAYMENTREQUEST_0_ITEMAMT'			=> number_format($this->importo, 2, '.', ''),
			'PAYMENTREQUEST_0_CURRENCYCODE'		=> $this->divisa ,
			'PAYMENTREQUEST_0_PAYMENTREQUESTID'	=> $this->numord ,
			'PAYMENTREQUEST_0_PAYMENTACTION'	=> 'Sale',
			'L_PAYMENTREQUEST_0_NAME0'			=> $this->numord ,
			'L_PAYMENTREQUEST_0_QTY0'			=> 1,
			'L_PAYMENTREQUEST_0_AMT0'			=> number_format($this->importo, 2, '.', ''),
			'LOCALECODE'						=> $this->languageId ,
			'EMAIL'								=> $this->email ,
			'RETURNURL'							=> $this->returnurl,
			'CANCELURL'							=> $this->urlBack,
			'METHOD'							=> 'SetExpressCheckout',
			'VERSION'							=> $this->version ,
			'PWD'								=>  $this->Password,
			'USER'								=> $this->Username,
			'SIGNATURE'							=> $this->Signature
		);

//echo "<pre>";
//echo print($this->paymentUrlAPI );
//echo "</pre>";
//
//echo "<pre>";
//echo print_r($nvp);
//echo "</pre>";

		$curl = curl_init();

		curl_setopt( $curl , CURLOPT_URL , $this->paymentUrlAPI ); //Link para ambiente de teste: https://api-3t.sandbox.paypal.com/nvp
//		curl_setopt( $curl, CURLOPT_PROXY, '127.0.0.1:8888'); /*-------fiddler------*/
		curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
		curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
		curl_setopt( $curl , CURLOPT_POST , 1 );
		curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query( $nvp ) );

		$response = urldecode( curl_exec( $curl ) );

		curl_close( $curl );

		$responseNvp = array();

		if ( preg_match_all( '/(?<name>[^\=]+)\=(?<value>[^&]+)&?/' , $response , $matches ) ) {
			foreach ( $matches[ 'name' ] as $offset => $name ) {
				$responseNvp[ $name ] = $matches[ 'value' ][ $offset ];
			}
		}

		if ( isset( $responseNvp[ 'ACK' ] ) && $responseNvp[ 'ACK' ] == 'Success' ) {
			$paypalURL = $this->paymentUrl ;
			$query = array(
				'cmd'	=> '_express-checkout',
				'token'	=> $responseNvp[ 'TOKEN' ]
			);

//echo "<pre>paypalURL";
//echo $paypalURL;
//echo "</pre>";

//echo "<pre>query";
//echo print_r($query);
//echo "</pre>";

			header( 'Location: ' . $paypalURL . '?' . http_build_query( $query ) );
		} else {
			echo 'error';
			
			echo "<pre>";
			echo $response;
			echo "</pre>";
			
		}		
	}

}

class paypalExpressProcessor extends baseProcessor{
	
	public $data;
	public $order;

	public function __construct($order = false,$url = false, $debug = false)
	{
		
	}
	
	public function getBankId($param = NULL) {
		$bankId = BFCHelper::getVar('trackid','');
		return $bankId ;
		//return parent::getResult($param);;
	}
	
	public function getAmount($param = NULL) {
		//$amount = BFCHelper::getFloat('Amount','');
		//converto in euro l'importo pagato
//		$amount =  number_format((BFCHelper::getFloat('udf4')), 2, '.', '');
		return number_format($this->order->DepositAmount, 2, '.', '');
		//return parent::getResult($param);;
	}

	public function getResult($param = NULL,$debug = true) {
		/* $this->data  Username|Password|Signature|Separator */
		$this->paymentUrl = 'https://www.paypal.com/cgi-bin/webscr';
		$this->paymentUrlAPI = 'https://api-3t.paypal.com/nvp';
		$this->method = "DoExpressCheckoutPayment";
		$this->version = "109.0";
		$this->paymentaction = "Sale";
		$this->divisa = 'EUR';

		if ($debug){
			$this->paymentUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			$this->paymentUrlAPI = 'https://api-3t.sandbox.paypal.com/nvp';
		}
		$this->Username = $this->data[0];
		$this->Password = $this->data[1];
		$this->Signature = $this->data[2];
		$this->separator = $this->data[3];
//		if(isset(BFCHelper::getVar('token')) && isset(BFCHelper::getVar('PayerID'))){
		if(null !==(BFCHelper::getVar('token')) && null !==(BFCHelper::getVar('PayerID'))){
		$nvp = array(
			'PAYMENTREQUEST_0_AMT'				=> number_format($this->order->DepositAmount, 2, '.', ''),
			'PAYMENTREQUEST_0_ITEMAMT'				=> number_format($this->order->DepositAmount, 2, '.', ''),
			'PAYMENTREQUEST_0_CURRENCYCODE'		 => $this->divisa ,
			'PAYMENTREQUEST_0_PAYMENTREQUESTID'		 => $this->order->OrderId ,
			'PAYMENTREQUEST_0_PAYMENTACTION'	 => 'Sale',
			'TOKEN'							=> BFCHelper::getVar("token") ,
			'PAYERID'							=> BFCHelper::getVar("PayerID") ,
			'METHOD'							=> $this->method,
			'VERSION'							=> $this->version ,
			'PWD'								=>  $this->Password,
			'USER'								=> $this->Username,
			'SIGNATURE'							=> $this->Signature
		);

		$curl = curl_init();

		curl_setopt( $curl , CURLOPT_URL , $this->paymentUrlAPI ); //Link para ambiente de teste: https://api-3t.sandbox.paypal.com/nvp
//		curl_setopt( $curl, CURLOPT_PROXY, '127.0.0.1:8888'); /*-------fiddler------*/
		curl_setopt( $curl , CURLOPT_SSL_VERIFYPEER , false );
		curl_setopt( $curl , CURLOPT_RETURNTRANSFER , 1 );
		curl_setopt( $curl , CURLOPT_POST , 1 );
		curl_setopt( $curl , CURLOPT_POSTFIELDS , http_build_query( $nvp ) );

		$response = urldecode( curl_exec( $curl ) );

		curl_close( $curl );

		$responseNvp = array();

		if ( preg_match_all( '/(?<name>[^\=]+)\=(?<value>[^&]+)&?/' , $response , $matches ) ) {
			foreach ( $matches[ 'name' ] as $offset => $name ) {
				$responseNvp[ $name ] = $matches[ 'value' ][ $offset ];
			}
		}

		if ( isset( $responseNvp[ 'ACK' ] ) && $responseNvp[ 'ACK' ] == 'Success' ) {
			if ( isset( $responseNvp[ 'PAYMENTINFO_0_PAYMENTSTATUS' ] ) && ($responseNvp[ 'PAYMENTINFO_0_PAYMENTSTATUS' ] == 'Completed' ||$responseNvp[ 'PAYMENTINFO_0_PAYMENTSTATUS' ] == 'Pending' ) ) {
				return true;
			} else {
				return false;
			}		
		} else {
			return false;
		}		

		}

	}

	public function responseRedir($msg = '',$result='')
	{
		if (empty($msg)){
			$msg = "0";
		}

		$uri                    = JURI::getInstance();
		$urlBase = $uri->toString(array('scheme', 'host', 'port'));
		$url = $urlBase . JRoute::_('index.php?view=payment&actionmode=orderpaid&payedOrderId=' . $msg . '&result=' . $result);
		
//		echo "<pre>url";
//		echo $url;
//		echo "</pre>";
		
		header( 'Location: ' . $url  );
		jexit();
	
	}
	

}
