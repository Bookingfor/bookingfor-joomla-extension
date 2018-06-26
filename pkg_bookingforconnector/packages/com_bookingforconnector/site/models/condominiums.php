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
class BookingForConnectorModelCondominiums extends JModelList
{
	private $urlResources = null;
	private $urlResourcesCount = null;
	private $resourcesCount = 0;
	private $urlGetCondominiumsByIds = null;
	
	private $helper = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlResources = '/GetCondominiums';
		$this->urlResourcesCount = '/GetCondominiumsCount';
		$this->resourcesCount = 0;
		$this->urlGetCondominiumsByIds = '/GetCondominiumsByIds';
		$this->urlServices = '/Services';
	}
		
	public function applyDefaultFilter(&$options) {
		$params = $this->getState('params');
		
//		$categoryId = $params['categoryId'];
//		
//		$filter = '';
//		// get only enabled merchants because disabled are of no use
////		$this->helper->addFilter($filter, 'Enabled eq true', 'and');
//
//		if (isset($categoryId) && $categoryId > 0) {
//			$this->helper->addFilter($filter, 'categoryId eq ' . $categoryId, 'and');
//		}
		
//		if ($filter!='')
//			$options['data']['$filter'] = $filter;

	}


	public  function GetCondominiumsByIds($listsId,$language='') {
		$cultureCode = JFactory::getLanguage()->getTag();
		$options = array(
				'path' => $this->urlGetCondominiumsByIds,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'ids' => '\'' .$listsId. '\'',
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'$format' => 'json',
//					'$select' => 'CondominiumId,Name,Description,MerchantId,AddressData,DefaultImg,XGooglePos,YGooglePos'
					//'$select' => 'Area,Rooms,Description,MerchantId,MerchantTypeId,ResourceId,MerchantName,LocationName,AddressData,ImageUrl,Logo,XGooglePos,YGooglePos,LocationZone,IsNewBuilding,ForegroundExpiration,HighlightExpiration,ShowcaseExpiration,PriceVariation,IsReservedPrice,Created,IsAddressVisible,IsShowcase,IsForeground'

				)
			);
		$url = $this->helper->getQuery($options);
	
		$resources = null;
	
		$r = $this->helper->executeQuery($url,"POST");
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


	public function getResourcesFromService($start, $limit, $ordering, $direction, $jsonResult = false) {// with random order is not possible to order by another field
		$cultureCode = JFactory::getLanguage()->getTag();

		$params = $this->getState('params');

		$options = array(
				'path' => $this->urlResources,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
//					'seed' => $seed,
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'$format' => 'json'
				)
			);
		$categoryId = $params['categoryId'];
		if (isset($categoryId) && $categoryId > 0) {
			$options['data']['categoryId'] = $categoryId;
		}

		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}
		
		$this->applyDefaultFilter($options);
		

//		// adding other ordering to allow grouping
//		$options['data']['$orderby'] = "IsShowcase desc, IsForeground desc, Created  desc";
//		$options['data']['$orderby'] = 'Created desc';

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

		if($jsonResult && !empty($resources))	{
			$arr = array();
			foreach($resources as $resource) {
				$val= new StdClass;
				$val->Resource = new StdClass;
					$val->Resource->ResourceId = $resource->CondominiumId ;
					$val->Resource->XGooglePos = $resource->XGooglePos;
					$val->Resource->YGooglePos = $resource->YGooglePos;
				$arr[] = $val;
			}
			return json_encode($arr);
				
		}

		return $resources;
	}

	public function getResourcesFromFavorites() {// with random order is not possible to order by another field
		$resources = null;
		if ( BFCHelper::CountFavourites()>0){ // only if there are unit in favorites 
			$params = $this->getState('params');

			$options = array(
					'path' => $this->urlResources,
					'data' => array(
						/*'$skip' => $start,
						'$top' => $limit,*/
						//'seed' => $seed,
						'$format' => 'json'
					)
				);
			
			$this->applyDefaultFilter($options);


			$filterFav = '';
			$tmpFav = BFCHelper::GetFavourites();

			foreach( $tmpFav as $key => $value ) {
				if( !empty( $tmpFav[ $key ] ) ){
					$this->helper->addFilter($filterFav, '(OnSellUnitId eq '.$tmpFav[ $key ].')', 'or');
				}
			}
			if ($filterFav!='') {
				if($options['data']['$filter']!='') 
				{
					$options['data']['$filter'] .= " and (" . $filterFav . ")";
				}
			}
			// get only enabled merchants because disabled are of no use

	//		// adding other ordering to allow grouping
	//		$options['data']['$orderby'] = 'Created desc';
//			$options['data']['$orderby'] = "IsShowcase desc, IsForeground desc, Created  desc";

	//		if (isset($ordering)) {
	//			$options['data']['$orderby'] .= ", " . $ordering . ' ' . strtolower($direction);
	//		}
			
			$url = $this->helper->getQuery($options);
			
			
			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				$resources = $res->d->results ?: $res->d;
			}
		}
		return $resources;
	}

	public function getAllResources()
	{
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlResourcesCount,
				'data' => array(
						'$filter' => 'Enabled eq true'
				)
			);
						
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$count = (int)$r;
		}

		return $count;
	}

	public function getTotalResources()
	{
		$params = $this->getState('params');
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlResourcesCount,
				'data' => array(
						'$format' => 'json'
				)
			);
		$categoryId = $params['categoryId'];
		if (isset($categoryId) && $categoryId > 0) {
			$options['data']['categoryId'] = $categoryId;
		}
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = 0;
			if (isset($res->d->GetCondominiumsCount)){
				$count = (int)$res->d->GetCondominiumsCount;
			}elseif(isset($res->d)){
				$count = (int)$res->d;
			}
		}

		return $count;
	}


	protected function populateState($ordering = NULL, $direction = NULL) {
		$filter_order = BFCHelper::getCmd('filter_order','Weight');
		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');
		$session = JFactory::getSession();
		$searchseed = $session->get('searchseed', rand(), 'com_bookingforconnector');
		if (!$session->has('searchseed','com_bookingforconnector')) {
			$session->set('searchseed', $searchseed, 'com_bookingforconnector');
		}
 
		$params = array(
			'categoryId' => BFCHelper::getInt('categoryId')
		);
//		
		$input = JFactory::getApplication()->input;
//		$show_latest = $input->get( 'show_latest' );  // show_latest
//		if ($show_latest) {
//			$params['show_latest'] = $show_latest;
//		}
		$this->setState('params', $params);
		
		return parent::populateState($filter_order, $filter_order_Dir);
	}
	
	public function getItemsLatest()
	{
		return $this->getItems('latest');
	}

	public function getItemsFavorites()
	{
		return $this->getItems('favorites');
	}

	public function getItems($type = '', $jsonResult = false)
	{
		// Get a storage key.
		$store = $this->getStoreId();

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}
		switch($type) {
			case 'latest':
				$items = $this->getLatestResourcesFromService(
					$this->getStart($type),
					$this->getState('list.limit'),
					$this->getState('list.ordering', 'Created'), 
					$this->getState('list.direction', 'desc')
				);
				break;
			case 'favorites':
				$items = $this->getResourcesFromFavorites();
				break;
			default:
			if(!$jsonResult){	
					$items = $this->getResourcesFromService(
							$this->getStart(), 
							$this->getState('list.limit'), 
							$this->getState('list.ordering', 'Created'), 
							$this->getState('list.direction', 'asc'),
							$jsonResult
						);
				}else{
					$items = $this->getResourcesFromService(
							null, 
							null, 
							null, 
							null,
							$jsonResult
						);
				}
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

	public function getTotal($type = '')
	{
		switch($type) {
			case 'latest':
				return $this->getTotalLatest();
				break;
			case '':
			default:
				return $this->getTotalResources();
				break;
		}
	}	
	
	
	public function getResourcesForSearch($text, $start, $limit, $ordering, $direction) {
		$cultureCode = JFactory::getLanguage()->getTag();
		//$typeId = $this->getTypeId();
		$options = array(
				'path' => $this->urlResources,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'$format' => 'json'
				)
			);

		if (isset($start) && $start >= 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}
		
		//$this->applyDefaultFilter($options);
		
		$filter = '';

		// get only enabled merchants because disabled are of no use
		$this->helper->addFilter($filter, 'Enabled eq true', 'and');

		if (isset($text)) {
			$this->helper->addFilter(
				$filter, 
				'substringof(\'' . $text . '\',Name) eq true', 
				'and'
			);
		}
				
		if ($filter!='')
			$options['data']['$filter'] = $filter;

		// adding other ordering to allow grouping
		$options['data']['$orderby'] = 'Rating desc';
		if (isset($ordering)) {
			$options['data']['$orderby'] .= ", " . $ordering . ' ' . strtolower($direction);
		}
		
		$url = $this->helper->getQuery($options);
		
		$resources = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$resources = $res->d->results ?: $res->d;
		}

		return $resources;
	}

	function getPaginationLatest()
	{	
		return $this->getPagination('latest');
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

}
