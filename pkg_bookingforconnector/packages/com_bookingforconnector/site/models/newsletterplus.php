<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modellist');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/wsQueryHelper.php';
require_once $pathbase . '/helpers/BFCHelper.php';

/**
 * BookingForConnectorModelOrders Model
 */
class BookingForConnectorModelNewsLetterPlus extends JModelList
{
		
	public function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	public function sendRequest($email = NULL, $cultureCode = NULL, $firstname = NULL, $lastname = NULL, $IDcategoria = NULL, $phone = NULL, $address = NULL, $nation = NULL, $reqUrlReg = NULL,$denominazione= NULL, $referer = NULL) {
		
		$url = COM_BOOKINGFORCONNECTOR_NLP_URL;
		$body = array();
		$querysuffix = "";
		$sendpost = false;

		switch(COM_BOOKINGFORCONNECTOR_NLP_SYSTEM) {
			case "mailup":
				$sendpost = true;
				$body = array(
							'email' => $email,
							'campo1' => $firstname,
							'campo2' => $lastname
					);
				if ( defined('COM_BOOKINGFORCONNECTOR_NLP_OTHERPARAM_' . strtoupper(substr($cultureCode,0,2)))){
					try {
						$querysuffix = constant('COM_BOOKINGFORCONNECTOR_NLP_OTHERPARAM_' . strtoupper(substr($cultureCode,0,2)));			
					} catch (Exception $e) {
					}
				}elseif(defined('COM_BOOKINGFORCONNECTOR_NLP_OTHERPARAM')){
						$querysuffix = COM_BOOKINGFORCONNECTOR_NLP_OTHERPARAM;			
				}
				break;
			case "newsletterplus":
				$languareResponse = self::getLanguage($cultureCode);
				if ($IDcategoria == null && defined('COM_BOOKINGFORCONNECTOR_NLP_CAT_' . strtoupper(substr($cultureCode,0,2)))){
					try {
						$IDcategoria = constant('COM_BOOKINGFORCONNECTOR_NLP_CAT_' . strtoupper(substr($cultureCode,0,2)));			
					} catch (Exception $e) {
					}
				}
				$body = array(
							'IDcliente' => COM_BOOKINGFORCONNECTOR_NLP_ID,
							'IDcategoria' => $IDcategoria,
							'cognome' => $lastname,
							'nome' => $firstname,
							'codice' => $cultureCode,
							'denominazione' => $denominazione,
							'tel' => $phone,
							'indirizzo' => $address,
							'email' => $email,
							'IDstato' => $nation,
							'reqUrlReg' => $reqUrlReg,
							'lingua' => $languareResponse
					);
				//$url = $this->helper->getQuery($options);
				break;
		}



		$body = wsQueryHelper::sanitizeData($body);
		$query = http_build_query($body);
		
		// http_build_query has urlencoded the query and char '$' has been replaced by '%24'. This restores the '$' char
		$query = str_ireplace('%24', '$', $query);
		$query = str_ireplace('%27', '\'', $query);
		$query = str_ireplace('__27__', '\'\'', $query);
		$query = str_ireplace('__1013__', '%0D%0A', $query);

//		$query = http_build_query($body);
		$query .= $querysuffix;
//		$r = $query;
		$r = null;
		try {
		
			$ch = curl_init($url);
//			curl_setopt($ch, CURLOPT_PROXY, '127.0.0.1:8888'); /*-------fiddler------*/
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_HTTPGET, true);
			curl_setopt ($ch, CURLOPT_POST, $sendpost);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_REFERER, $referer);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			
			$r = curl_exec($ch);
			curl_close ($ch);
		} catch (Exception $e) {
			//echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		
		
		return $r;		

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
	
	protected function populateState($ordering = NULL, $direction = NULL) {
//		$filter_order = BFCHelper::getCmd('filter_order','Name');
//		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');
// 
//		$this->setState('params', array(
//			'typeId' => BFCHelper::getInt('typeId'),
//			'categoryId' => BFCHelper::getVar('categoryId')
//		));
		
		return parent::populateState($filter_order, $filter_order_Dir);
	}
	
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		return $this->cache[$store];
	}
}
