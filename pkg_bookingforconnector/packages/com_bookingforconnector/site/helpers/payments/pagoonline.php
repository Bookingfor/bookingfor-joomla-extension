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


/*================= PAGOONLINE ==================================*/
class pagoonline{
	/*
	PAyment only in Kune
	
	*/
	public $merchantID;
	public $shopID;
	public $userID;
	public $password;
	public $separator;
	public $SecretKey;
	public $paymentUrl="http://pagamenti.unicredito.it/initInsert.do?";
	public $numeroOrdine;
	public $valuta="978";	
	public $email;	
	public $languageId;
	public $totaleOrdine;	
	public $urlOk;
	public $urlKo;
	public $suffixOrder;
	public $mac;
	public $causalePagamento;
	public $flagRiciclaOrdine="N";
	public $tipoRispostaApv="wait";
	public $flagDeposito="Y";

	public function __construct($config, $order, $language, $urlok, $urlko, $suffixOrder, $overrideAmount=0, $debug = FALSE)
	{
		$paymentData = explode( '|', $config);  /*MerchantID|ShopID|UserID|Password|Separator|SecretString */
		$this->merchantID = $paymentData[0];
		$this->shopID = $paymentData[1];
		$this->userID = $paymentData[2];
		$this->password = $paymentData[3];
		$this->separator = $paymentData[4];
		$this->SecretKey = $paymentData[5];
		//$this->paymentUrl = $paymentData[3];
		
		$this->numeroOrdine = sprintf('B%s%s%s%s', rand(1, 9999) . $this->separator, $order->ExternalId, $this->separator,$order->OrderId);
		$this->totaleOrdine =  intval($order->DepositAmount * 100 ) ;
		if (isset($suffixOrder) && $suffixOrder!= "" && $overrideAmount >0 ){
			$this->numord .= $this->separator . "R" . $suffixOrder;
			$this->totaleOrdine =  intval($overrideAmount * 100 ) ;
		}
		$this->email = BFCHelper::getItem($order->CustomerData, 'email')."";
		$this->languageId = $this->getLanguage($language);
		$this->urlOk = $urlok;
		$this->urlKo = $urlko;
		
		if ($debug){
			$this->merchantID = '9999888';
			$this->shopID = '99888';
			$this->userID = '9999888_IPERTRD';
			$this->numeroOrdine = 'VERXORDXPROD196';
			$this->totaleOrdine =  '1';
			$this->password = 'Ipertrade2013!';
			$this->SecretKey = 'b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1b1';
			$this->tipoRispostaApv = 'click';
			$this->urlOk ="http://www.dominio.it/ok.html";
			$this->urlKo ="http://www.dominio.it/ko.html";
		}
		
//		$this->paymentUrl .= $this->getUrl();
	}
	
	public function getLanguage($language) {
		
		switch (strtolower(substr($language,0,2))) {
			case 'it':
				return 'it';
				break;
			case 'en':
				return 'en';
				break;
			case 'de':
				return 'de';			
				break;
			case 'fr':
				return 'fr';
				break;
			case 'es':
				return 'es';
				break;
			default:
				return 'en';
				break;
		}
	}

	public function getUrl(){
		// Concatenazione input per il calcolo del MAC
		$inputMac  = "numeroCommerciante=".trim($this->merchantID );
		$inputMac .= "&userID=".trim($this->userID );
		$inputMac .= "&password=".trim($this->password );
		$inputMac .= "&numeroOrdine=".trim($this->numeroOrdine );
		$inputMac .= "&totaleOrdine=".trim($this->totaleOrdine );
		$inputMac .= "&valuta=".trim($this->valuta);
		$inputMac .= "&flagDeposito=".trim($this->flagDeposito);
		$inputMac .= "&urlOk=".trim($this->urlOk);
		$inputMac .= "&urlKo=".trim($this->urlKo);
		$inputMac .= "&tipoRispostaApv=".trim($this->tipoRispostaApv);
		$inputMac .= "&flagRiciclaOrdine=".trim($this->flagRiciclaOrdine);
		$inputMac .= "&stabilimento=".trim($this->shopID );

//		 qui potrei aggiungere gli eventuali parametri facoltativi :
//		 'tipoPagamento' e 'causalePagamento'

		if(!empty($this->email)){
			$inputMac .= "&emailCompratore=".trim($this->email);
		}
		if(!empty($this->causalePagamento)){
			$inputMac .= "&causalePagamento=".trim($this->causalePagamento);
		}
		$inputMac .= "&".trim($this->SecretKey);

		//Calcolo della firma digitale della stringa in input
		$MAC = md5($inputMac);
		$MACtemp = "";
		for($i=0;$i<strlen($MAC);$i=$i+2) {
			$MACtemp .= chr(hexdec(substr($MAC,$i,2)));
		}
		$MAC = $MACtemp;

		// Codifica del MAC con lo standard BASE64
		$MACcode = base64_encode($MAC);
		
		$inputUrl = "numeroCommerciante=".trim($this->merchantID );
		$inputUrl .= "&userID=".trim($this->userID );
		$inputUrl .= "&password=PASSWORD+FINTA";      //la password vera viene usata solo per il calcolo del MAC e non viene inviata al sito dei pagamenti (qui è sostituita con il valore fittizio "Password")
		$inputUrl .= "&numeroOrdine=".trim($this->numeroOrdine );
		$inputUrl .= "&totaleOrdine=".trim($this->totaleOrdine );
		$inputUrl .= "&valuta=".trim($this->valuta);
		$inputUrl .= "&flagDeposito=".trim($this->flagDeposito);
		$inputUrl .= "&urlOk=".urlencode($this->urlOk);
		$inputUrl .= "&urlKo=".urlencode($this->urlKo);
		$inputUrl .= "&tipoRispostaApv=".trim($this->tipoRispostaApv);
		$inputUrl .= "&flagRiciclaOrdine=".trim($this->flagRiciclaOrdine);
		$inputUrl .= "&stabilimento=".trim($this->shopID );
		if(!empty($this->email)){
			$inputUrl .= "&emailCompratore=".trim($this->email);
		}
		if(!empty($this->causalePagamento)){
			$inputUrl .= "&causalePagamento=".urlencode(trim($this->causalePagamento));
		}
		$inputUrl .= "&mac=".urlencode(trim($MACcode));
				
		return $this->paymentUrl . $inputUrl;
	}
}

class pagoonlineProcessor extends baseProcessor{
	public function __construct($order=null,$url=null, $debug = FALSE)
	{
		
	}
	
	public function getResult($param=null) {
		$esito = BFCHelper::getVar('esito','');
		return (strtolower($esito)=='ok');
		//return parent::getResult($param);;
	}
	public function getBankId($param=null) {
		$bankId = BFCHelper::getVar('numeroOrdine','');
		return $bankId ;
		//return parent::getResult($param);;
	}
	public function getAmount($param=null) {
		//$amount = BFCHelper::getFloat('Amount','');
		//converto in euro l'importo pagato
		//$amount =  number_format((BFCHelper::getFloat('Amount') / COM_BOOKINGFORCONNECTOR_CONVERSIONCURRENCY), 2, '.', '');
		$amount = 0;
		return $amount;
		//return parent::getResult($param);;
	}
}


/*================= END PAGOONLINE ==================================*/
