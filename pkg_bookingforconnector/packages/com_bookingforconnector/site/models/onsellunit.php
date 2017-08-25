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
		$this->urlResource = '/GetResourceOnSellByIdSimple';
		$this->urlUnitServices = '/GetResourceOnSellServicesByResourceId';
		$this->urlUnit = '/GetResourceOnSellByIdSimple';
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

	public function getResourceFromServicebyId($resourceId, $language ="") {
		$resourceId = $resourceId;
		$resourceIdRef = $resourceId;
		if(empty( $language )){
			$language = JFactory::getLanguage()->getTag();
		}
		
		$options = array(
				'path' => $this->urlResource,
				'data' => array(
					'$format' => 'json',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'id' =>$resourceId
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$resource = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->GetResourceOnSellByIdSimple)){
				$resource = $res->d->GetResourceOnSellByIdSimple;
			}elseif(!empty($res->d)){
				$resource = $res->d;
			}
			$resource->Merchant=BFCHelper::getMerchantFromServicebyId($resource->MerchantId);
		}
		return $resource;
	}	

	public function getResourceFromService() {
		$params = $this->getState('params');
		$resourceId = $params['resourceId'];
		return $this->getResourceFromServicebyId($resourceId);
	}
	

	public function getResourceServicesFromService($resourceId = null) {
		$params = $this->getState('params');
		$language = $GLOBALS['bfi_lang'];
		if ($resourceId==null) {
			$resourceId = $params['resourceId'];
		}
				
		$options = array(
				'path' => $this->urlUnitServices,
				'data' => array(
					'$format' => 'json',
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'id' =>$resourceId
//					'orderby' => 'IsDefault asc'
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
