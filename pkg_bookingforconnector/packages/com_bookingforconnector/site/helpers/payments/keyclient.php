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


/*================= KEYCLIENT ==================================*/

class keyClient{
	
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
	public $mac;
		
	public function __construct($config, $order, $language, $urlBack, $url, $debug = FALSE)
	{
		$paymentData = explode( '|', $config);  /*ShopID|Separator|StartSecretKey|PaymentUrl */
		$this->alias = $paymentData[0];
		$this->separator = $paymentData[1];
		$this->startSecretKey = $paymentData[2];
		$this->paymentUrl = $paymentData[3];
		$this->divisa = 'EUR';
		$this->numord = sprintf('B%s%s%s%s', rand(1, 9999) . $this->separator, $order->ExternalId, $this->separator,$order->OrderId);
		$this->email = BFCHelper::getItem($order->CustomerData, 'email')."";
		$this->languageId = $this->getLanguage($language);
		$this->importo = intval($order->DepositAmount * 100);
		$this->url = $url;
		$this->urlBack = $urlBack;
		
		if ($debug){
			$this->numord = 'testCILME534';
			$this->importo = 1;
			$this->alias = 'payment_testm_urlmac';
			$this->startSecretKey = 'esempiodicalcolomac';
		}
		
		$this->mac = $this->getMac();
	}
	
	public function getLanguage($language) {
		
		switch (strtolower(substr($language,0,2))) {
			case 'it':
				return 'ITA';
				break;
			case 'en':
				return 'ENG';
				break;
			case 'de':
				return 'GER';			
				break;
			case 'es':
				return 'SPA';
				break;
			case 'fr':
				return 'FRA';
				break;
			case 'jp':
				return 'JPN';
				break;
			default:
				return '';
				break;
		}
	}

	public function getMac(){
		$strMac =  'codTrans='.$this->numord.'divisa='.$this->divisa.'importo='.$this->importo;
		
		$calculatedMac = urlencode(base64_encode(md5($strMac.$this->startSecretKey)));
		
		return $calculatedMac;
	}
}

class keyclientProcessor extends baseProcessor{
	public function __construct($order = false,$url = false, $debug = false)
	{
		
	}
	
	public function getResult($param = NULL) {
		$esito = BFCHelper::getVar('esito','');
		return (strtolower($esito)=='ok');
		//return parent::getResult($param);;
	}
}
