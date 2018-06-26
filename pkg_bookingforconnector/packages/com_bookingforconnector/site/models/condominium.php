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
require_once $pathbase . '/helpers/SimpleDOM.php';

/**
 * BookingForConnectorModelMerchants Model
 */
class BookingForConnectorModelCondominium extends JModelList
{
	private $urlResource = null;
	private $urlResources = null;
	private $urlResourcesCount = null;
	private $urlUnitServices = null;
	private $helper = null;
	private $urlResourceCounter = null;
	private $urlSearchAllCalculate = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlResource = '/GetCondominiumById';
//		$this->urlUnitServices = '/Condominiums(%d)/Unit/Services';
		$this->urlResources = '/GetResources';
		$this->urlResourcesCount = '/GetResourcesCount';
		$this->urlResourceCounter = '/CondominiumsCounter';
		$this->urlSearchAllCalculate = '/SearchAllLiteNew';
	}
	
	public function setCounterByResourceId($resourceId = null, $what='', $language='') {
		$params = $this->getState('params');
		
		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
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

	public function getResourceFromService($resourceId='') {
		$cultureCode = JFactory::getLanguage()->getTag();
		if(empty($resourceId)){
			$params = $this->getState('params');
			$resourceId = $params['resourceId'];
		}
		$options = array(
				'path' => $this->urlResource,
				'data' => array(
					'Id' => $resourceId,
					'$format' => 'json',
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
//					'$expand' => 'Merchant,Services'
					//'$expand' => 'Merchant/MerchantType,OnSellUnit/Services'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$condominium= null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$condominium = $res->d->results->GetCondominiumById;
			}elseif(!empty($res->d)){
				$condominium = $res->d->GetCondominiumById;
			}

		}
		if(!empty($condominium)){
			$condominium->Merchant=BFCHelper::getMerchantFromServicebyId($condominium->MerchantId);
		}
		
		
		return $condominium;
	}	

		public function applyDefaultFilter(&$options) {

		$params = $this->getState('params');
		
		$categories = $params['categories'];
		if (!empty($categories)) {
			$options['data']['productcategories'] =  BFCHelper::getQuotedString(implode(',',$categories));
		}
		
		$condominiumid = $params['parentProductId'];
		if (!empty($condominiumid)) {
			$options['data']['parentProductId'] =  $condominiumid;
		}
	}

//	public function getResourcesSearchFromService($start, $limit) {
//		$params = $this->getState('params');
//				
//		$resourceId = $params['resourceId'];
//		
//		$options = array(
//				'path' => $this->urlSearchAllCalculate,
//				'data' => array(
//						'$format' => 'json',
//						'topRresult' => 0,
//						'calculate' => 1,
//						'lite' => 1,
//						'condominiumId' => $resourceId
//				)
//		);
//		
//		$this->applyDefaultFilter($options);
//		
////		if (isset($start) && $start >= 0) {
////			$options['data']['$skip'] = $start;
////		}
////		
////		if (isset($limit) && $limit > 0) {
////			$options['data']['$top'] = $limit;
////		}	
//				
//		$url = $this->helper->getQuery($options);
//		
//		$resources = null;
//				
//		$r = $this->helper->executeQuery($url);
//		
//		if (isset($r)) {
//			$res = json_decode($r);
////			$resources = $res->d->results ?: $res->d;
//			if (!empty($res->d->results)){
//				$resources = $res->d->results;
//			}elseif(!empty($res->d)){
//				$resources = $res->d;
//			}
//		}
//
//		return $resources;
//	}

	public function getResourcesFromService($start, $limit) {// with random order is not possible to order by another field

		$params = $this->getState('params');
		$seed = $params['searchseed'];
		$cultureCode = JFactory::getLanguage()->getTag();

		$options = array(
				'path' => $this->urlResources,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'seed' => $seed,
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'$format' => 'json'
				)
			);

		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}
		
		$this->applyDefaultFilter($options);
		

//		// adding other ordering to allow grouping
//		$options['data']['$orderby'] = 'Weight desc';
//		if (isset($ordering)) {
//			$options['data']['$orderby'] .= ", " . $ordering . ' ' . strtolower($direction);
//		}
		
		$url = $this->helper->getQuery($options);
		
		$resources = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$resources = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$resources = $res->d->results;
			}elseif(!empty($res->d)){
				$resources = $res->d;
			}
		}

		return $resources;
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

		
	protected function populateState($ordering = NULL, $direction = NULL) {
		$session = JFactory::getSession();
		$searchseed = BFCHelper::getSession('searchseedcond', rand(), 'com_bookingforconnector');
		if ($searchseed ==null) {
			BFCHelper::setSession('searchseedcond', $searchseed, 'com_bookingforconnector');
		}
		$resourceId = BFCHelper::getInt('resourceId');
		$defaultRequest =  array(
			'categories' => BFCHelper::getArray('categories'),
			'parentProductId' => BFCHelper::getInt('resourceId'),
			'resourceId' => BFCHelper::getInt('resourceId'),
			'state' => BFCHelper::getStayParam('state'),
			'searchseed' => $searchseed
		);
		
		$this->setState('params', $defaultRequest);

		return parent::populateState($ordering, $direction);
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
	
	public function getItemsResourcesAjax() 
	{
		return $this->getItems('resourcesajax');
	}
	
	public function getItemsSearch() 
	{
		return $this->getItems('search');
	}

	public function getItems($type = '') 
	{
		// Get a storage key.
		$store = $this->getStoreId('getItems'.$type);

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		switch($type) {
			case 'resourcesajax':
				$items = $this->getResourcesFromService(
					0,
					COM_BOOKINGFORCONNECTOR_MAXRESOURCESAJAXMERCHANT
				);
				break;
			case 'search':
				$items = $this->getResourcesSearchFromService(
					$this->getStart(),
					$this->getState('list.limit')
				);
				break;
			case '':
			default:				
				$items = $this->getResourcesFromService(
					$this->getStart(),
					$this->getState('list.limit')
				);
				break;
		}
				
		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
	
	public function getStart($type = '')
	{
		$store = $this->getStoreId('getstart'.$type);

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$start = $this->getState('list.start');
		$limit = $this->getState('list.limit');
		$total = $this->getTotal($type);
		if ($start > $total - $limit)
		{
			$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $start;

		return $this->cache[$store];
	}

	function getPagination($type = '')
	{
		// Load the content if it doesn't already exist
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal($type), $this->getState('list.start'), $this->getState('list.limit') );
		}
		return $this->_pagination;
	}

	public function getTotal($type = '')
	{
		switch($type) {
			case '':
			default:
				return $this->getTotalResources();
		}
	}	

	public function getTotalResources()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlResourcesCount,
				'data' => array(
					'$format' => 'json'
			)
			);
//			$options['data']['$inlinecount'] = 'allpages';
//			$options['data']['$top'] = 0;
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = 0;
			$count = (int)$res->d->GetResourcesCount;
//			if (!empty($res->d->__count)){
//				$count = (int)$res->d->__count;
//			}elseif(!empty($res->d)){
//				$count = (int)$res->d;
//			}
//			$count = (int)$r;
		}
		return $count;
	}	
	
}
