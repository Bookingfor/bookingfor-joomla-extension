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
categoryId:
	1: Merchant
	2: Vendite
	4: Risorse
	8: Pacchetti
	16: Offerte 


 */
class BookingForConnectorModelTag extends JModelList
{
	private $urlTags = null;
	private $urlTagForSearch = null;
	private $urlTagsCount = null;
	private $urlTagsbyids = null;
	private $urlTagbyid = null;

	private $urlMerchant = null;
	private $urlMerchantCount = null;
	private $urlResources = null;
	private $urlResourcesCount = null;
	private $urlOffers = null;
	private $urlOffersCount = null;
	private $urlPackages = null;
	private $urlPackagesCount = null;
	private $count = null;
	private $availableCount = null;

		

	private $helper = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(null, null);
		$this->urlTags = '/GetTags';
		$this->urlTagForSearch = '/GetTagsForSearch';
		$this->urlTagsCount = '/Tags/$count/';
		$this->urlTagsbyids = '/GetTagsByIds';
		$this->urlTagbyid = '/GetTagById';
	
		$this->urlMerchants = '/GetMerchantsByTagIds';
		$this->urlMerchantsExt = '/GetMerchantsByTagIdsExt';
		$this->urlMerchantsCount = '/GetMerchantsByTagIds';
		$this->urlResources = '/SearchAllLiteNew';
		$this->urlResourcesCount = '/GetMerchantResourcesCount';
		$this->urlOffers = null;
		$this->urlOffersCount = null;
		$this->urlPackages = null;
		$this->urlPackagesCount = null;

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
	
//	public function getTags($language='', $categoryIds='', $start, $limit)  {
////		$session = JFactory::getSession();
//		$results = BFCHelper::getSession('getTags'.$language.$categoryIds, null , 'com_bookingforconnector');
////		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
//		if ($results==null) {
//			$results = $this->getTagsFromService($language, $categoryIds, $start, $limit);
//			BFCHelper::setSession('getTags'.$language.$categoryIds, $results, 'com_bookingforconnector');
//		}
//		return $results;
//	}
	public function getTags($language='', $categoryIds='', $start, $limit)  {
		$results = $this->getTagsFromService($language, $categoryIds, $start, $limit);
		return $results;
	}
	public function getTagsFromService($language='', $categoryIds='', $start, $limit) {
		if (empty($language)){
			$language = JFactory::getLanguage()->getTag();
		}
		$options = array(
				'path' => $this->urlTags,
				'data' => array(
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);

		if (!empty($categoryIds) ) {
			$options['data']['categoryIds'] = BFCHelper::getQuotedString($categoryIds);
		}

		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$ret = null;
		
		$r = $this->helper->executeQuery($url,null,null,false);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$ret = $res->d->results;
			}elseif(!empty($res->d)){
				$ret = $res->d;
			}
		}

		return $ret;
	}

//	public function getTagsForSearch($language='', $categoryIds='')  {
//		$session = JFactory::getSession();
//		$results = $session->get('getTagsForSearch'.$language.$categoryIds, null , 'com_bookingforconnector');
////		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
//		if ($results==null) {
//			$results = $this->getTagsForSearchFromService($language, $categoryIds);
//			$session->set('getTagsForSearch'.$language.$categoryIds, $results, 'com_bookingforconnector');
//		}
//		return $results;
//	}
	public function getTagsForSearch($language='', $categoryIds='')  {
		$results = $this->getTagsForSearchFromService($language, $categoryIds);
		return $results;
	}
	
	public function getTagsForSearchFromService($language='', $categoryIds='') {
		if (empty($language)){
			$language = JFactory::getLanguage()->getTag();
		}
		$options = array(
				'path' => $this->urlTagForSearch,
				'cultureCode' => BFCHelper::getQuotedString($language),
				'data' => array(
					'$format' => 'json'
				)
			);

		if (!empty($categoryIds) ) {
			$options['data']['categoryIds'] = BFCHelper::getQuotedString($categoryIds);
		}
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$ret = null;
		
		$r = $this->helper->executeQuery($url,null,null,false);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$ret = $res->d->results;
			}elseif(!empty($res->d)){
				$ret = $res->d;
			}
		}

		return $ret;
	}
	
	public function getTotal($type = '')
	{
		switch($type) {
			case 'merchants':
				return $this->getTotalMerchants();				
			case 'resources':
			default:
				return $this->getTotalResources();
		}
	}	

	

	public function getTotalResources()
	{
		
//		$params = $this->getState('params');
//		$tagId = $params['tagId'];
//				
//		$options = array(
//				'path' => $this->urlResourcesCount,
//				'data' => array(
//					'$format' => 'json',
//					'tagids' => BFCHelper::getQuotedString($tagId),
//				)
//			);
//		
//		$url = $this->helper->getQuery($options);
//		
//		$count = null;
//		$this->applyDefaultFilter($options);
//				
//		$url = $this->helper->getQuery($options);
//		
//		$count = 0;
//		
//		$r = $this->helper->executeQuery($url);
//		if (isset($r)) {
//			$res = json_decode($r);
//			if (isset($res->d->__count)){
//				$count = (int)$res->d->__count;
//			}elseif(isset($res->d)){
//				$count = (int)$res->d;
//			}
//		}
		return $this->count;
	}

	public function getTotalMerchants()
	{
		$params = $this->getState('params');
		$tagId = $params['tagId'];
				
		$options = array(
				'path' => $this->urlMerchants,
				'data' => array(
					'$format' => 'json',
					'tagids' => BFCHelper::getQuotedString($tagId),
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$count = null;
			$options['data']['$inlinecount'] = 'allpages';
			$options['data']['$top'] = 0;
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$count = 0;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (isset($res->d->__count)){
				$count = (int)$res->d->__count;
			}elseif(isset($res->d)){
				$count = (int)$res->d;
			}
		}
		$this->availableCount =  $count;
		$this->count =  $count;
		
		return $count;
	}

	public function getTagsByIds($listsId,$language='') {// with randor order is not possible to otrder by another field
		if (empty($language)){
			$language = JFactory::getLanguage()->getTag();
		}
		$options = array(
				'path' => $this->urlTagsbyids,
				'data' => array(
					'$format' => 'json',
					'ids' =>  '\'' .$listsId. '\'',
					'cultureCode' => BFCHelper::getQuotedString($language)
				)
			);
  
		$url = $this->helper->getQuery($options);
		
		$ret = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$ret = $res->d->results;
			}elseif(!empty($res->d)){
				$ret = $res->d;
			}
		}

		return $ret;
	}

	public function getTagById($id,$language='') {// with randor order is not possible to otrder by another field
		if (empty($language)){
			$language = JFactory::getLanguage()->getTag();
		}
		$options = array(
				'path' => $this->urlTagbyid,
				'data' => array(
					'$format' => 'json',
					'id' =>  $id,
					'cultureCode' => BFCHelper::getQuotedString($language)
				)
			);
  
		$url = $this->helper->getQuery($options);
		
		$ret = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->GetTagById)){
				$ret = $res->d->GetTagById;
			}elseif(!empty($res->d)){
				$ret = $res->d;
			}
		}

		return $ret;
	}



	protected function populateState($ordering = NULL, $direction = NULL) {
		$defaultRequest =  array(
			'tagId' => BFCHelper::getInt('tagId'),
			'category' => BFCHelper::getInt('category'),
			'show_grouped' => BFCHelper::getInt('show_grouped'),
			'newsearch' => BFCHelper::getInt('newsearch'),
			'state' => BFCHelper::getStayParam('state'),
		);
		
		$this->setState('params', $defaultRequest);

		$filter_order = BFCHelper::getCmd('filter_order','Order');
		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');		
		return parent::populateState($filter_order, $filter_order_Dir);
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

		$params = $this->getState('params');
		$tagId = $params['tagId'];
		
		$item = $this->getTagById($tagId);

		// Add the items to the internal cache.
		$this->cache[$store] = $item;

		return $this->cache[$store];
	}

	public function getItemsMerchants() 
	{
		return $this->getItems('merchants');
	}	
	public function getItemsOnSellUnit() 
	{
		return $this->getItems('onsell');
	}	
	public function getItemsResources() 
	{
		return $this->getItems('resources');
	}	


	public function getItems($type = '')
	{
		$params = $this->getState('params');
		$tagId = $params['tagId'];
		// Get a storage key.
		$store = $this->getStoreId('getItems' . $type.$tagId);

		$ignorePagination = false;
		$jsonResult = false;

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{

						echo "<!-- cache -->";			
			return $this->cache[$store];
		}
		switch($type) {
			case 'resources':
				$items = $this->getResourcesFromService(
					$this->getStart($type),
					$this->getState('list.limit'),
					$this->getState('list.ordering'), 
					$this->getState('list.direction'),
					$ignorePagination,
					$jsonResult
				);
				break;
			case 'merchants':
				$items = $this->getMerchantsFromService(
						$this->getStart($type),
						$this->getState('list.limit')
					);
				break;
			case 'onsell':
				$items = $this->getOnSellUnitFromService(
					$this->getStart($type),
					$this->getState('list.limit')
				);
				break;
			case '':
			default:				
				$items = $this->getTags(
					$this->getStart(), 
					$this->getState('list.limit'), 
					$this->getState('list.ordering', 'Order'), 
					$this->getState('list.direction', 'asc')
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
	
	public function getMerchantsFromService($start, $limit) {
		$params = $this->getState('params');
		
		$tagId = $params['tagId'];

//		$cultureCode = JFactory::getLanguage()->getTag();
		
		$options = array(
				'path' => $this->urlMerchants,
				'data' => array(
					'tagids' => BFCHelper::getQuotedString($tagId),
//					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					'$format' => 'json'
				)
		);
		
		if (isset($start) && $start >= 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}	
								
		$url = $this->helper->getQuery($options);
				
		$ret = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$ret = $res->d->results;
			}elseif(!empty($res->d)){
				$ret = $res->d;
			}
		}

		return $ret;
	}

	public function getMerchantsExt($tagids, $start = null, $limit = null) {
		$cultureCode = JFactory::getLanguage()->getTag();
		
		$options = array(
				'path' => $this->urlMerchantsExt,
				'data' => array(
					'tagids' => BFCHelper::getQuotedString(implode(',', $tagids)),
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
		$searchid = isset($params['searchid']) ? $params['searchid'] : '';
		if (isset($searchid) && $searchid !='') {
			$options['data']['searchid'] = '\'' . $searchid. '\'';
		}
								
		$url = $this->helper->getQuery($options);
				
		$ret = array();
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$ret = $res->d->results;
			}elseif(!empty($res->d)){
				$ret = $res->d;
			}
		}

		return $ret;
	}

	public function getTotalAvailable()
	{
		if ($this->availableCount !== null){
			return $this->availableCount;
		}
		else{
//			$this->retrieveItems();
			return $this->availableCount;
		}
	}

	public function getResourcesFromService($start, $limit, $ordering, $direction, $ignorePagination = false, $jsonResult = false) {

		$store = $this->getStoreId();
		
		$language = JFactory::getLanguage()->getTag();

		$this->currentOrdering = $ordering;
		$this->currentDirection = $direction;
		
		$params = $this->getState('params');
		$newsearch = $params['newsearch'];
		$tagId = $params['tagId'];
		$searchid = "resources".$tagId ;
		$params['groupresulttype'] = $params['show_grouped'];
		$merchantResults = $params['show_grouped'];
		
		$currParamInSession = BFCHelper::getSearchParamsSession();
				
		if(isset($currParamInSession) && $currParamInSession != null ){
			$currParamInSession['onlystay'] == false;
			$searchid = isset($currParamInSession['searchid']) ? $currParamInSession['searchid'] :  uniqid('', true);
		}else{
			$currParamInSession = null;
			$currParamInSession['onlystay'] == false;
			$searchid =  uniqid('', true);
		}
		$currParamInSession['searchid'] = $searchid;

		BFCHelper::setSearchParamsSession($currParamInSession);
		
//		$condominiumsResults = $params['condominiumsResults'];
		
		$sessionkey = 'tags.' . $searchid . '.results';

//		$session = JFactory::getSession();
		$options = array(
			'path' => $this->urlResources,
			'data' => array(
					'$format' => 'json',
					'topRresult' => 0,
					'calculate' => 0,
					'checkAvailability' => 0,
					'cultureCode' =>  BFCHelper::getQuotedString($language),
					'lite' => 1,
					'tagids' => BFCHelper::getQuotedString($tagId)
			)
		);
//			$this->applyDefaultFilter($options);
			if (!empty($merchantResults) ) {
				$options['data']['groupResultType'] = $merchantResults;
//				if ($groupresulttype==1 || $groupresulttype==2) { //onbly for merchants 
					$options['data']['getBestGroupResult'] = 1;
//				}
			}

		if (isset($searchid) && $searchid !='') {
			$options['data']['searchid'] = '\'' . $searchid. '\'';
		}

		$url = $this->helper->getQuery($options);

		$results = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->SearchAllLiteNew)){
				$results = $res->d->SearchAllLiteNew;
			}elseif(!empty($res->d)){
				$results = $res->d;
			}
		}

		if(!empty($results)){
			$params['show_grouped'] = ($results->GroupResultType==1);
			$params['groupresulttype'] = ($results->GroupResultType==1);
			if ($results->GroupResultType==1) {
			    $params['merchantTagIds'] = $tagId;
			}else{
			    $params['productTagIds'] = $tagId;
				}
		
		
//			$params['condominiumsResults'] = ($results->GroupResultType==2);
			$merchantResults = $params['show_grouped'];
//			$condominiumsResults = $params['condominiumsResults'];
		}
		$resultsItems = null;

		if(isset($results->ItemsCount)){
			$this->count = $results->ItemsCount;
			$this->availableCount = $results->AvailableItemsCount;
			$resultsItems = json_decode($results->ItemsString);
		}
		BFCHelper::setSearchParamsSession($params);

		if($jsonResult && !empty($resultsItems))	{
			$arr = array();

			foreach($resultsItems as $result) {
				$val= new StdClass;
				
				if ($merchantResults) {
					$val->MerchantId = $result->MerchantId; 
					$val->XGooglePos = $result->MrcLat;
					$val->YGooglePos = $result->MrcLng;
					$val->MerchantName = BFCHelper::getSlug($result->MrcName);
				}
//				elseif ($condominiumsResults){
//					$val->Resource = new StdClass;
//					$val->Resource->CondominiumId = $result->CondominiumId;
//					$val->Resource->ResourceId = $result->ResourceId;
//					$val->Resource->XGooglePos = $result->ResLat;
//					$val->Resource->YGooglePos = $result->ResLng;
//					$val->Resource->ResourceName = BFCHelper::getSlug($result->ResName);
//					$val->Resource->Price = $result->Price;
//				}
				else { 
					$val->Resource = new StdClass;
					$val->Resource->ResourceId = $result->ResourceId;
					$val->Resource->XGooglePos = $result->ResLat;
					$val->Resource->YGooglePos = $result->ResLng;
					$val->Resource->ResourceName = BFCHelper::getSlug($result->ResName);
					$val->Resource->Price = $result->Price;
				}
				$arr[] = $val;
			}

			return json_encode($arr);
				
		}
		return $resultsItems;
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
