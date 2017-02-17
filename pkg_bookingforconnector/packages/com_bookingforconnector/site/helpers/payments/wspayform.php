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


/*================= WSPAYFORM ==================================*/
class wspayform{
	/*
	PAyment only in Kune
	
	*/
	public $shopID;
	public $separator;
	public $SecretKey;
	public $paymentUrl;
	public $numord;
	public $email;	
	public $languageId;
	public $importo;	
	public $importoForMac;	
	public $urlBack;
	public $url;
	public $suffixOrder;
	public $mac;
	public $redirectUrl;
		
	public function __construct($config, $order, $language, $urlBack, $url, $suffixOrder, $overrideAmount=0, $debug = FALSE)
	{
		$paymentData = explode( '|', $config);  /*ShopID|Separator|SecretKey|PaymentUrl */
		$this->shopID = $paymentData[0];
		$this->separator = $paymentData[1];
		$this->SecretKey = $paymentData[2];
		$this->paymentUrl = $paymentData[3];
		$this->numord = sprintf('B%s%s%s%s', rand(1, 9999) . $this->separator, $order->ExternalId, $this->separator,$order->OrderId);
		$this->importo =  number_format(($order->DepositAmount * COM_BOOKINGFORCONNECTOR_CONVERSIONCURRENCY), 2, ',', '');
		$this->importoForMac =  intval($order->DepositAmount * 100 * COM_BOOKINGFORCONNECTOR_CONVERSIONCURRENCY ) ;
		if (isset($suffixOrder) && $suffixOrder!= "" && $overrideAmount >0 ){
			$this->numord .= $this->separator . "R" . $suffixOrder;
			$this->importo =  number_format(($overrideAmount * COM_BOOKINGFORCONNECTOR_CONVERSIONCURRENCY), 2, ',', '');
			$this->importoForMac =  intval($overrideAmount * 100 * COM_BOOKINGFORCONNECTOR_CONVERSIONCURRENCY ) ;
		}
		$this->email = BFCHelper::getItem($order->CustomerData, 'email')."";
		$this->languageId = $this->getLanguage($language);
		$this->url = $url;
		$this->urlBack = $urlBack;
		
		if ($debug){
			$this->shopID = 'MYSHOP';
			$this->numord = 78;
			$this->importo =  '17,00';
			$this->importoForMac = '1700';
			$this->SecretKey = '3DfEO2B5Jjm4VC1Q3vEh';
		}
		
		//$this->mac = $this->getMac();
		$this->redirectUrl=getUrl();
	}
	
	public function getLanguage($language) {
		
		switch (strtolower(substr($language,0,2))) {
			case 'it':
				return 'IT';
				break;
			case 'en':
				return 'EN';
				break;
			case 'de':
				return 'DE';			
				break;
			case 'hr':
				return 'HR';
				break;
			default:
				return 'EN';
				break;
		}
	}

	public function getMac(){
		$strMac =  $this->shopID.$this->SecretKey.$this->numord.$this->SecretKey.$this->importoForMac.$this->SecretKey;
		
		$calculatedMac = md5($strMac);
		
		return $calculatedMac;
	}
}

class wspayformProcessor extends baseProcessor{
	public function __construct($order = false,$url = false, $debug = false)
	{
		
	}
	
	public function getResult($param = NULL) {
		$esito = BFCHelper::getVar('Success','');
		return (strtolower($esito)=='1');
		//return parent::getResult($param);;
	}
	public function getBankId($param) {
		$bankId = BFCHelper::getVar('ShoppingCartID','');
		return $bankId ;
		//return parent::getResult($param);;
	}
	public function getAmount($param) {
		//$amount = BFCHelper::getFloat('Amount','');
		//converto in euro l'importo pagato
		$amount =  number_format((BFCHelper::getFloat('Amount') / COM_BOOKINGFORCONNECTOR_CONVERSIONCURRENCY), 2, '.', '');
		return $amount;
		//return parent::getResult($param);;
	}
}


/*================= END WSPAYFORM ==================================*/
