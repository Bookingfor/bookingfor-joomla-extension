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
require_once $pathbase . '/helpers/SimpleDOM.php';

/**
 * BookingForConnectorModelMerchants Model
 */
class BookingForConnectorModelOnSellUnit extends JModelItem
{
	private $urlResource = null;
	private $urlUnit = null;
	private $urlUnits = null;
	private $urlUnitServices = null;
	private $helper = null;
	private $urlResourceCounter = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlResource = '/ResourceonsellView(%d)'; //GetResourceOnSellById
		$this->urlUnitServices = '/ResourceonsellView(%d)/Unit/Services'; //GetResourceOnSellServicesByResourceId
		$this->urlUnit = '/ResourceonsellView(%d)'; //GetResourceOnSellById
		$this->urlUnits = '/ResourceonsellView'; //NON UTILIZZATO
		$this->urlResourceCounter = '/OnSellUnitCounter';
	}
	
	public function setCounterByResourceId($resourceId = null, $what='', $language='') {
		$params = $this->getState('params');
		
		if ($resourceId==null) {
			$resourceId = $params['resourceId'];
		}
		
		$options = array(
				'path' => $this->urlResourceCounter,
				'data' => array(
					'resourceId' => $resourceId,
					'what' =>  BFCHelper::getQuotedString($what), //'\''.$what.'\'',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$res = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resReturn = $res->d->results;
			}elseif(!empty($res->d)){
				$resReturn = $res->d;
			}
			if (!empty($resReturn)){
				$res = $resReturn->OnSellUnitCounter;
			}
		}

		return $res;
	}	

	public function getResourceFromService() {
		$params = $this->getState('params');
		$resourceId = $params['resourceId'];
		$resourceIdRef = $params['resourceId'];
		$options = array(
				'path' => sprintf($this->urlResource, $resourceId),
				'data' => array(
					'$format' => 'json'
					//,'$expand' => 'Merchant,Services'
					//,'$expand' => 'Merchant/MerchantType,OnSellUnit/Services'
				)
			);
		
		$url = $this->helper->getQuery($options);
		$cultureCode = JFactory::getLanguage()->getTag();
		
		$resource = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$resource = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$resource = $res->d->results;
			}elseif(!empty($res->d)){
				$resource = $res->d;
			}
			$resource->Merchant=BFCHelper::getMerchantFromServicebyId($resource->MerchantId);
//		$resource->Services = $resource->OnSellUnit->Services;
			if (!empty($resource->ServiceIdList)){
				$services=BFCHelper::GetServicesByIds($resource->ServiceIdList,$cultureCode);
				$resource->Services = $services;
				if (count($resource->Services) > 0){
					$tmpservices = array();
					foreach ($resource->Services as $service){
						$tmpservices[] = $service->Name;
					}
//					$services = implode(', ',$tmpservices);
				}
				$resource->Services = $services;
			}

		}
		return $resource;
	}	
	

	public function getResourceServicesFromService($resourceId = null) {
		$params = $this->getState('params');
		if ($resourceId==null) {
			$resourceId = $params['resourceId'];
		}
				
		$options = array(
				'path' => sprintf($this->urlUnitServices, $resourceId),
				'data' => array(
					'$filter' => 'Enabled eq true',
					'$format' => 'json',
					'orderby' => 'IsDefault asc'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$services = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$services = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$services = $res->d->results;
			}elseif(!empty($res->d)){
				$services = $res->d;
			}
		}
		
		return $services;
	}

		
	public function getUnitCategoriesFromService() {
		
		$options = array(
				'path' => $this->urlUnitCategories,
				'data' => array(
						'$filter' => 'Enabled eq true',
						'$format' => 'json'
				)
		);
		$url = $this->helper->getQuery($options);
	
		$categoriesFromService = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$categories = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$categoriesFromService = $res->d->results;
			}elseif(!empty($res->d)){
				$categoriesFromService = $res->d;
			}
		}
	
		return $categoriesFromService;
	}
	
	public function getUnitCategories() {
		$session = JFactory::getSession();
		$categories = $session->get('getUnitCategories', null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($categories==null) {
			$categories = $this->getUnitCategoriesFromService();
			$session->set('getUnitCategories', $categories, 'com_bookingforconnector');
		}
		return $categories;
	}

	protected function populateState() {
		$resourceId = BFCHelper::getInt('resourceId');
		$defaultRequest =  array(
			'resourceId' => BFCHelper::getInt('resourceId'),
			'state' => BFCHelper::getStayParam('state'),
		);
		
		$this->setState('params', $defaultRequest);

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

		$item = $this->getResourceFromService();
		
		// Add the items to the internal cache.
		$this->cache[$store] = $item;

		return $this->cache[$store];
	}
	
}
