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
 * BookingForConnectorModelMerchants Model
 */
class BookingForConnectorModelServices extends JModelList
{
	private $urlServices = null;
	private $urlGetService = null;
	private $urlServicesCount = null;
	private $servicesCount = 0;
	private $urlServicesbyid = null;
		
	private $helper = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlGetService = '/Services(%d)';
		$this->urlServices = '/Services';
		$this->urlServicesCount = '/Services/$count/';
		$this->urlServicesbyid = '/GetServicesByIds';
	}
	
	public function applyDefaultFilter(&$options) {
//		$params = $this->getState('params');
//		
//		$typeId = $params['typeId'];
//		$categoryIds = $params['categoryId'];
//		
//		$filter = '';
//		// get only viewable services
//		//$this->helper->addFilter($filter, 'Viewable eq true', 'and');
//
//		if (isset($typeId) && $typeId > 0) {
//			$this->helper->addFilter(
//				$filter, 
//				'TypologyId eq ' . $typeId, 
//				'and'
//			);
//		}
//				
//		if ($filter!='')
//			$options['data']['$filter'] = $filter;
//
//		if (count($categoryIds) > 0)
//			$options['data']['categoryIds'] = '\''.implode('|',$categoryIds).'\'';
	}
	
	
//	public function getservicesFromService($start, $limit, $ordering, $direction) {
	public function getServicesFromService($start, $limit) {// with randor order is not possible to otrder by another field
		$options = array(
				'path' => $this->urlServices,
				'data' => array(
					'$filter' => 'Enabled eq true',
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
		

		// adding other ordering to allow grouping
//		$options['data']['$orderby'] = 'Rating desc';
//		if (isset($ordering)) {
//			$options['data']['$orderby'] .= ", " . $ordering . ' ' . strtolower($direction);
//		}
		
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
	
	public function getTotal()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlServicesCount,
				'data' => array(
					'$filter' => 'Enabled eq true',
					'$format' => 'json'
				)
			);
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = $res->d->GetServicesCount;
		}

		return $count;
	}
	
	public function getServicesByIds($listsId,$language='') {// with randor order is not possible to otrder by another field
		$options = array(
				'path' => $this->urlServicesbyid,
				'data' => array(
					'$format' => 'json',
					'ids' =>  '\'' .$listsId. '\'',
					'cultureCode' => BFCHelper::getQuotedString($language)
				)
			);
  
		$url = $this->helper->getQuery($options);
		
		$services = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$services = $res->d->results;
			}elseif(!empty($res->d)){
				$services = $res->d;
			}
		}

		return $services;
	}

	public function getServiceFromService($serviceid)
	{
		$params = $this->getState('params');
	
		$data = array(
				'$format' => 'json'
		);
		
		$options = array(
				'path' => sprintf($this->urlGetService, $serviceid),
				'data' => $data
		);
		
		$url = $this->helper->getQuery($options);
		
		$service= null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$service = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$service = $res->d->results;
			}elseif(!empty($res->d)){
				$service = $res->d;
			}

		}
		
		
		return $service;
	}
		
	protected function populateState($ordering = NULL, $direction = NULL) {
		$filter_order = BFCHelper::getCmd('filter_order','Order');
		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');		
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

		$items = $this->getServicesFromService(
			$this->getStart(), 
			$this->getState('list.limit'), 
			$this->getState('list.ordering', 'Order'), 
			$this->getState('list.direction', 'asc')
		);

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
}
