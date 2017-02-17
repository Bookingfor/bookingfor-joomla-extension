<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */ 

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modelitem');

$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/wsQueryHelper.php';
require_once $pathbase . '/helpers/BFCHelper.php';
require_once $pathbase . '/helpers/FormHelper.php';
/**
 * BookingForConnectorModelMerchants Model
 */
class BookingForConnectorModelCrew extends JModelItem
{
	private $urlCrew = null;
	private $urlCreateCrew = null;	


		
	private $helper = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlCrew = '/GetCrews';
		$this->urlCreateCrew = '/CreateCrew';
	}
	
	public function setCrew($customerData = NULL, $merchantId = NULL, $orderId = NULL) {
		
		$params = $this->getState('params');
		if($customerData == null) $customerData = $params['customerData'];
		

		if(COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDER){
			
			$this->urlCreateCrew = '/CreateCrewFrom'.COM_BOOKINGFORCONNECTOR_USEEXTERNALUPDATEORDERSYSTEM;
		}
		
		$options = array(
				'path' => $this->urlCreateCrew,
				'data' => array(
						'crewData' => BFCHelper::getQuotedString(BFCHelper::getJsonEncodeString($customerData)),
						'merchantId' => $merchantId,
						'orderId' => $orderId,
						'$format' => 'json',
						'$expand' => 'ChildCrews'
				)
		);
		$url = $this->helper->getQuery($options);
	
		$crew = null;
	
		//$r = $this->helper->executeQuery($url);
		$r = $this->helper->executeQuery($url,"POST");
		if (isset($r)) {
			$res = json_decode($r);
			$crew = $res->d->results ?: $res->d;
		}
	
		return $crew[0];
	
	}
	
	public function getCrewFromService() {
		$params = $this->getState('params');
		$crewId = $params['crewId'];
		$orderId = $params['orderId'];
		$path= $this->urlCrew;
		$data = array(
				/*'$filter' => 'Enabled eq true',
				'$top' => 1,
				'$orderby' => 'IsDefault desc',*/
				'$expand' => 'ChildCrews',
				'$format' => 'json'
		);
		if ($crewId!=null){
//			$data['$filter'] = 'CrewId eq ' . $crewId ;
			//$path = sprintf($this->urlCrew . '(%d)', $crewId);
			$data['crewId'] =  $crewId ;
		}else{
			$data['orderId'] =  $orderId ;
//			$data['$filter'] = 'OrderId eq ' . $orderId . ' and ParentCrewId eq null ';
//			$data['$top'] = 1;
		}

		$options = array(
				'path' => $path,
				'data' => $data
			);
		
		$url = $this->helper->getQuery($options);
		
		$crew = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$crew = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$crew = $res->d->results;
			}elseif(!empty($res->d)){
				$crew = $res->d;
			}
		}

		
		return $crew[0];
	}
	
	protected function populateState() {
		$crewlistNumber = BFCHelper::getInt('crewslist',0);
		$birdDate = explode('/', BFCHelper::getVar('birthDate','//'));

		$nation ='';
		$culture ='';
		$documentId ='';
		$gender ='';
		$formData = BFCHelper::getArray('form');
		if(isset($formData)){
			$nation = FormHelper::getOptionsFromSelect($formData, 'nation');
			$culture = FormHelper::getOptionsFromSelect($formData, 'culture');
			$documentId = FormHelper::getOptionsFromSelect($formData, 'documentId');
			$gender = FormHelper::getOptionsFromSelect($formData, 'gender');
		}
		$customerData = array(
			'CrewId' => BFCHelper::getInt('crewId'),
			'OrderId' => BFCHelper::getInt('orderId'),
			'MerchantId' => BFCHelper::getInt('merchantId'),
			'Nation' => $nation,
			'Culture' => $culture,
			'DocumentId' => $documentId,
			'crewslist' => $crewlistNumber, //select
			'FirstName' => BFCHelper::getVar('firstName'),
			'LastName' => BFCHelper::getVar('lastName'),
			'Email' => BFCHelper::getVar('email'),
			'Gender' =>  $gender, 		
			'BirthDate' => $birdDate[2].'-' . $birdDate[1] . '-' . $birdDate[0],				
			'BirthLocation' => BFCHelper::getVar('birthLocation'),				
			'Address' => BFCHelper::getVar('address'),				
			'City' => BFCHelper::getVar('city'),				
			'Province' => BFCHelper::getVar('province'),				
			'PostalCode' => BFCHelper::getVar('postalCode'),				
			'Phone' => BFCHelper::getVar('phone'),				
			'DocumentNumber' => BFCHelper::getVar('documentNumber'),				
			'DocumentReleaseDate' => BFCHelper::getVar('documentRelease')
		);
		$customerDatas = array($customerData);
		for ($i = 0; $i < $crewlistNumber; $i++){
			$birdDate = explode('/', BFCHelper::getVar('birthDate'.$i,'//'));
			$nation ='';
			$gender ='';
			$formData = BFCHelper::getArray('form');
			if(isset($formData)){
				$nation = FormHelper::getOptionsFromSelect($formData, 'nation'.$i);
				$gender = FormHelper::getOptionsFromSelect($formData, 'gender'.$i);
			}
			$customerData1 = array(
			'CrewId' => BFCHelper::getInt('crewId'.$i),
			'ParentCrewId' => BFCHelper::getInt('parentCrewId'.$i),
			'OrderId' => BFCHelper::getInt('orderId'.$i),
			'MerchantId' => BFCHelper::getInt('merchantId'.$i),
			'Nation' => $nation, 
			'FirstName' => BFCHelper::getVar('firstName'.$i),
			'LastName' => BFCHelper::getVar('lastName'.$i),
			'Gender' => $gender,			
			'BirthDate' => $birdDate[2].'-' . $birdDate[1] . '-' . $birdDate[0],				
			'BirthLocation' => BFCHelper::getVar('birthLocation'.$i)
			);
			$customerDatas[] = $customerData1;
		}
		$vars['crewId'.$i]=BFCHelper::getInt('crewId'.$i);
		$this->setState('params',array(
							'orderId' => BFCHelper::getInt('orderId'),
							'merchantId' => BFCHelper::getInt('merchantId'),
							'crewId' => BFCHelper::getInt('crewId'),
							'customerData'=> $customerDatas
							)
		);
		
		return parent::populateState();
	}
	
	public function getItem()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItem');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$item = $this->getCrewFromService();
		
		// Add the items to the internal cache.
		$this->cache[$store] = $item;

		return $this->cache[$store];
	}
}
