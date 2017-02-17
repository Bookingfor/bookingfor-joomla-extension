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
	private $urlUnits = null;
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
		$this->urlUnits = '/Resources';
		$this->urlUnitsCount = '/Resources';
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

	public function getResourceFromService() {
		$params = $this->getState('params');
		$resourceId = $params['resourceId'];
		$resourceIdRef = $params['resourceId'];
		$options = array(
				'path' => $this->urlResource,
				'data' => array(
					'Id' => $resourceId,
					'$format' => 'json',
//					'$expand' => 'Merchant,Services'
					//'$expand' => 'Merchant/MerchantType,OnSellUnit/Services'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$resource = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$resource = $res->d->results ?: $res->d;
			if (!empty($res->d->GetCondominiumById)){
				$resource = $res->d->GetCondominiumById;
			}elseif(!empty($res->d)){
				$resource = $res->d;
			}
		}
		return $resource;
	}	

		public function applyDefaultFilter(&$options) {

		$params = BFCHelper::getSearchParamsSession();
		if(!empty($params)){
			$masterTypeId = $params['masterTypeId'];
			$checkin = $params['checkin'];
			$checkout = $params['checkout'];
			$duration = $params['duration'];
			$persons = $params['paxes'];
			$merchantCategoryId = $params['merchantCategoryId'];
			$paxages = $params['paxages'];
			$merchantId = $params['merchantId'];

			$cultureCode = $params['cultureCode'];
			$resourceName = $params['resourceName'].'';
			$refid = $params['refid'].'';
			$onlystay = $params['onlystay'];
		}
		
		$filter = '';
		

		if (isset($onlystay) && $onlystay <> "false") {// solo se è calcolato allora faccio una ricerca con i parametri altrimenti non li passo

			if (isset($params['locationzone']) ) {
				$locationzone = $params['locationzone'];
			}
			if (isset($masterTypeId) && $masterTypeId > 0) {
				$options['data']['masterTypeId'] = $masterTypeId;
			}

			if (isset($merchantCategoryId) && $merchantCategoryId > 0) {
				$options['data']['merchantCategoryId'] = $merchantCategoryId;
			}
			
			if ((isset($checkin)) && (isset($duration) && $duration > 0)) {
				$options['data']['checkin'] = '\'' . $checkin->format('Ymd') . '\'';
				$options['data']['duration'] = $duration;
			}
			
			if (isset($persons) && $persons > 0) {
				$options['data']['paxes'] = $persons;
				if (isset($paxages)) {
					$options['data']['paxages'] = '\'' . implode('|',$paxages) . '\'';
				}else{
					$px = array_fill(0,$persons,BFCHelper::$defaultAdultsAge);
					$options['data']['paxages'] = '\'' . implode('|',$px) . '\'';
				}
			}
			
				$options['data']['pricetype'] = '\'' . 'rateplan' . '\'';

			if (isset($locationzone) && $locationzone > 0) {
				$options['data']['zoneId'] = $locationzone;
			}
		}else{
			if (isset($refid) && $refid <> "" ) {
				$options['data']['refId'] = '\''.$refid.'\'';
			}
			if (isset($resourceName) && $resourceName <> "" ) {
				$options['data']['resourceName'] = '\''. $resourceName.'\'';
			}
		}


		if (isset($cultureCode) && $cultureCode !='') {
			$options['data']['cultureCode'] = '\'' . $cultureCode. '\'';
		}
		
		if (isset($merchantId) && $merchantId > 0) {
			$options['data']['merchantid'] = $merchantId;
		}

		if ($filter!='')
			$options['data']['$filter'] = $filter;

		/*if (count($categoryIds) > 0)
			$options['data']['categoryIds'] = '\''.implode('|',$categoryIds).'\'';*/
	}

	public function getResourcesSearchFromService($start, $limit) {
		$params = $this->getState('params');
				
		$resourceId = $params['resourceId'];
		
		$options = array(
				'path' => $this->urlSearchAllCalculate,
				'data' => array(
						'$format' => 'json',
						'topRresult' => 0,
						'calculate' => 1,
						'lite' => 1,
						'condominiumId' => $resourceId
				)
		);
		
		$this->applyDefaultFilter($options);
		
//		if (isset($start) && $start >= 0) {
//			$options['data']['$skip'] = $start;
//		}
//		
//		if (isset($limit) && $limit > 0) {
//			$options['data']['$top'] = $limit;
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

	public function getResourcesFromService($start, $limit) {

				$params = $this->getState('params');
				
		$resourceId = $params['resourceId'];
		
		$options = array(
				'path' => $this->urlUnits,
				'data' => array(
					'$format' => 'json',
					'$filter' => 'ParentProductId eq ' . $resourceId,
				)
		);
		
		if (isset($start) && $start >= 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}	

		$options['data']['$orderby'] = 'Weight desc';
				
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
		$resourceId = BFCHelper::getInt('resourceId');
		$defaultRequest =  array(
			'resourceId' => BFCHelper::getInt('resourceId'),
			'state' => BFCHelper::getStayParam('state'),
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
		$params = $this->getState('params');
		$resourceId = $params['resourceId'];
				
		$options = array(
				'path' => $this->urlUnitsCount,
				'data' => array(
					'$format' => 'json',
					'$filter' => 'ParentProductId eq ' . $resourceId,
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$count = null;
			$options['data']['$inlinecount'] = 'allpages';
			$options['data']['$top'] = 0;
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = 0;
			if (isset($res->d->__count)){
				$count = (int)$res->d->__count;
			}elseif(isset($res->d)){
				$count = (int)$res->d;
			}
//			$count = (int)$r;
		}


		return $count;
	}	
	
}
