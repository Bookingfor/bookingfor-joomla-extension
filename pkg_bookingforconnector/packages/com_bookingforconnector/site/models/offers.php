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
class BookingForConnectorModelOffers extends JModelList
{
	private $urlOffers = null;
	private $urlOffersCount = null;
	private $offersCount = 0;
		
	private $helper = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlOffers = '/Packages';
		$this->urlOffersCount = '/Packages/$count';
	}
	
	public function applyDefaultFilter(&$options) {
		$params = $this->getState('params');
		
		$typeId = $params['typeId'];
		$categoryIds = $params['categoryId'];
		
		$filter = '';
		// get only viewable offers
		//$this->helper->addFilter($filter, 'Viewable eq true', 'and');

		if (isset($typeId) && $typeId > 0) {
			$this->helper->addFilter(
				$filter, 
				'TypologyId eq ' . $typeId, 
				'and'
			);
		}
				
		if ($filter!='')
			$options['data']['$filter'] = $filter;

		if (count($categoryIds) > 0)
			$options['data']['categoryIds'] = '\''.implode('|',$categoryIds).'\'';
	}
	
	
//	public function getOffersFromService($start, $limit, $ordering, $direction) {
	public function getOffersFromService($start, $limit) {// with randor order is not possible to otrder by another field
		//$typeId = $this->getTypeId();

		$params = $this->getState('params');
		$seed = $params['searchseed'];

		$options = array(
				'path' => $this->urlOffers,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'seed' => $seed,
					'expand' => '\'Merchant\'',
					'$expand' => 'Merchant',
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
		
		$offers = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$offers = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$offers = $res->d->results;
			}elseif(!empty($res->d)){
				$offers = $res->d;
			}
		}

		return $offers;
	}
	
	public function getTotal()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlOffersCount,
				'data' => array(
					'$format' => 'json'
				)
			);
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$count = (int)$r;

//			$res = json_decode($r);
//			$count = $res->d->GetOffersCount;
		}

		return $count;
	}
	
	protected function populateState($ordering = NULL, $direction = NULL) {
		$filter_order = BFCHelper::getCmd('filter_order','Name');
		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');

		$session = JFactory::getSession();
		$searchseed = $session->get('searchseed', rand(), 'com_bookingforconnector');
		if (!$session->has('searchseed','com_bookingforconnector')) {
			$session->set('searchseed', $searchseed, 'com_bookingforconnector');
		}
 
		$this->setState('params', array(
			'typeId' => BFCHelper::getInt('typeId'),
			'categoryId' => BFCHelper::getVar('categoryId'),
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

		$items = $this->getOffersFromService(
			$this->getStart(), 
			$this->getState('list.limit')
		);

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
}
