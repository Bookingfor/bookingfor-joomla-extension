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

/**
 * BookingForConnectorModelMerchants Model
 */
class BookingForConnectorModelResources extends JModelList
{
	private $urlResources = null;
	private $urlResourcesCount = null;
	private $resourcesCount = 0;
	private $urlMasterTypologies = null;
	private $urlCheckAvailabilityCalendar = null;
	private $urlGetResourcesByIds = null;
	private $helper = null;
	private $urlServices = null;
	private $TypeId = 1; // default value for product booking
	private $urlResourcesSearch = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlResources = '/GetResources'; //'/Resources';
		$this->urlResourcesCount = '/GetResourcesCount'; //'/Resources';
		$this->resourcesCount = 0;
		$this->urlMasterTypologies = '/GetMasterTypologies';
		$this->urlCheckAvailabilityCalendar = '/CheckAvailabilityCalendarByIdList';
		$this->urlGetResourcesByIds = '/GetResourcesByIds';
		$this->urlServices = '/GetServicesForSearch';
		$this->urlResourcesSearch = '/SearchAllLiteNew'; //'/Resources';
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

	public  function getServicesForSearch($language='') {
		$session = JFactory::getSession();
		$sessionkey= 'getServicesForSearch'.$language;
		$services = $session->get($sessionkey, null , 'com_bookingforconnector');
//		$services = null;
		if ($services==null) {		
			$options = array(
					'path' => $this->urlServices,
					'data' => array(
						'$format' => 'json',
						'cultureCode' => BFCHelper::getQuotedString($language),
						'typeId' => 1
					)
				);

			$url = $this->helper->getQuery($options);
		
			$services = null;
		
			$r = $this->helper->executeQuery($url,null,null,false);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->results)){
					$services = $res->d->results;
				}elseif(!empty($res->d)){
					$services = $res->d;
				}
			}
			$session->set($sessionkey, $services, 'com_bookingforconnector');
		}
	
		return $services;
	}

	public function getCheckAvailabilityCalendarFromService($resourcesId = null,$checkIn= null,$checkOut= null) {
		$resultCheck = '';
		if ($resourcesId==null || $checkIn ==null  || $checkOut ==null ) {
			return $resultCheck;
		}
		if ($checkIn==null) {
			$defaultDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDate(),new DateTimeZone('UTC'));
			$checkIn =  BFCHelper::getStayParam('checkin', $defaultDate);
		}
		if ($checkOut==null) {
			$checkOut =   BFCHelper::getStayParam('checkout', $checkIn->modify(BFCHelper::$defaultDaysSpan));
		}
		//calcolo le settimane necessarie

		//$ci = $params['checkin'];
		$options = array(
				'path' => $this->urlCheckAvailabilityCalendar,
				'data' => array(
					'resourcesId' => BFCHelper::getQuotedString($resourcesId) ,
					'checkin' => '\'' . $checkIn->format('Ymd') . '\'',
					'checkout' => '\'' . $checkOut->format('Ymd') . '\'',
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		

		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$checkDate = $res->d->results ?: $res->d;
			$resultCheck = $checkDate->CheckAvailabilityCalendarByIdList;
		}
		
		return $resultCheck;
	}

	public function getMasterTypologiesFromService($onlyEnabled = true) {
		$options = array(
				'path' => $this->urlMasterTypologies,
				'data' => array(
					/*'$filter' => 'IsEnabled eq true',*/
					'$format' => 'json'
				)
			);
			
		if ($onlyEnabled) {
			$options['data']['$filter'] = 'IsEnabled eq true';
		}
		
		$url = $this->helper->getQuery($options);
		
		$typologies = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$typologies = $res->d->results ?: $res->d;
		}
		
		return $typologies;
	}

	public function getMasterTypologies($onlyEnabled = true) {
		$session = JFactory::getSession();
		$typologies = BFCHelper::getSession('getMasterTypologies', null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($typologies==null) {
			$typologies = $this->getMasterTypologiesFromService($onlyEnabled);
			BFCHelper::setSession('getMasterTypologies', $typologies, 'com_bookingforconnector');
		}
		return $typologies;
	}

	public  function GetResourcesByIds($listsId,$language='') {
		$options = array(
				'path' => $this->urlGetResourcesByIds,
				'data' => array(
					'ids' => '\'' .$listsId. '\'',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);
		$url = $this->helper->getQuery($options);
	
		$resources = null;
	
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resources = json_encode($res->d->results);
			}elseif(!empty($res->d)){
				$resources = json_encode($res->d);
			}
		}
	
		return $resources;
	}


	public function getResourcesFromService($start, $limit, $ordering, $direction) {// with random order is not possible to order by another field

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
	
	public function getTotal()
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
	
	protected function populateState($ordering = NULL, $direction = NULL) {
		$filter_order = BFCHelper::getCmd('filter_order','Name');
		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');

		$session = JFactory::getSession();
		$searchseed = BFCHelper::getSession('searchseed', rand(), 'com_bookingforconnector');
		if ($searchseed ==null) {
			BFCHelper::setSession('searchseed', $searchseed, 'com_bookingforconnector');
		}
 
		$this->setState('params', array(
			'categories' => BFCHelper::getArray('categories'),
			'parentProductId' => BFCHelper::getInt('parentProductId'),
			'searchseed' => $searchseed
		));
		
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

		$items = $this->getResourcesFromService(
			$this->getStart(), 
			$this->getState('list.limit'), 
			$this->getState('list.ordering', 'Name'), 
			$this->getState('list.direction', 'asc')
		);

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	public function getResourcesForSearch($text, $start, $limit, $ordering, $direction) {
		//$typeId = $this->getTypeId();
		$cultureCode = JFactory::getLanguage()->getTag();
		$options = array(
				'path' => $this->urlResourcesSearch,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'topRresult' => 0,
					'lite' => 1,
					'$format' => 'json'
				)
			);

		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['topRresult'] = $limit;
		}

		$resourceName = $text.'';
		$options['data']['calculate'] = 0;
		$options['data']['checkAvailability'] = 0;
		$options['data']['itemTypeIds'] = '\'0,1,2,3\'';
		$options['data']['cultureCode'] = '\'' . $cultureCode. '\'';

		if (isset($resourceName) && $resourceName <> "" ) {
			$options['data']['resourceName'] = '\''. $resourceName.'\'';
		}
		
		//$this->applyDefaultFilter($options);
		
//		$filter = '';

		// get only enabled merchants because disabled are of no use
//		$this->helper->addFilter($filter, 'Enabled eq true', 'and');

//		if (isset($text)) {
//			$this->helper->addFilter(
//				$filter, 
//				'substringof(\'' . $text . '\',Name) eq true', 
//				'and'
//			);
//		}
				
//		if ($filter!='')
//			$options['data']['$filter'] = $filter;

		// adding other ordering to allow grouping
//		$options['data']['$orderby'] = 'Rating desc';
//		if (isset($ordering)) {
//			$options['data']['$orderby'] .= ", " . $ordering . ' ' . strtolower($direction);
//		}
		
		$url = $this->helper->getQuery($options);
		
			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->SearchAllLiteNew)){
					$results = $res->d->SearchAllLiteNew;
				}elseif(!empty($res->d)){
					$results = $res->d;
				}
			}

						
		$resources = null;

		if(isset($results->ItemsCount)){
			$resources = json_decode($results->ItemsString);
		}

		
		
//		$resources = null;
//		
//		$r = $this->helper->executeQuery($url);
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
		return $resources;
	}

	public function getMapResourcesFromService($jsonResult = false) {// with random order is not possible to order by another field

		$params = $this->getState('params');

		$options = array(
				'path' => $this->urlResources,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,
					'seed' => $seed,*/
					'$format' => 'json'
				)
			);
		
		$this->applyDefaultFilter($options);
				
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

		if($jsonResult)	{
			$arr = array();
			foreach($resources as $result) {
				if(isset($result->XPos) && !empty($result->XPos) && ($result->IsMapVisible)  && ($result->IsMapMarkerVisible) ){
					$val= new StdClass;
					$val->Id = $result->ResourceId ;
					$val->X = $result->XPos;
					$val->Y = $result->YPos;
					$arr[] = $val;
				}
			}
			return json_encode($arr);
				
		}

		return $resources;
	}
	
}
