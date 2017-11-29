<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

set_time_limit(300);
$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/wsQueryHelper.php';

/**
 * BookingForConnectorModelMerchants Model
 */
class BookingForConnectorModelSearch extends JModelList
{
	private $urlSearch = null;
	private $urlMasterTypologies = null;
	private $helper = null;
	private $currentOrdering = null;
	private $currentDirection = null;
	private $count = null;
	private $currentData = null;
	private $params = null;
	private $itemPerPage = null;
	private $direction = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(null, null);
		$this->urlMasterTypologies = '/GetMasterTypologies';
		$this->urlSearchResult = '/SearchResult';
		$this->urlSearch = '/SearchAllLiteNew';
	}
	
	public function applyDefaultFilter(&$options) {
		$params = BFCHelper::getSearchParamsSession();

		$searchid = !empty($params['searchid']) ? $params['searchid'] : uniqid('', true);
		$masterTypeId = $params['masterTypeId'];
		$checkin = $params['checkin'];
		$checkout = $params['checkout'];
		$duration = $params['duration'];
		$persons = $params['paxes'];
		$merchantCategoryId = $params['merchantCategoryId'];
		$paxages = $params['paxages'];
		$merchantId = $params['merchantId'];
		$tags = isset($params['tags'])?$params['tags']:"";
		$searchtypetab = $params['searchtypetab'];
		$stateIds = $params['stateIds'];
		$regionIds = $params['regionIds'];
		$cityIds = $params['cityIds'];
		$merchantIds = isset($params['merchantIds']) ? $params['merchantIds'] : '';
		$merchantTagIds = $params['merchantTagIds'];
		$productTagIds = $params['productTagIds'];
		
		$availabilitytype = $params['availabilitytype'];
		$itemtypes = $params['itemtypes'];
		$groupresulttype = $params['groupresulttype'];

		$cultureCode = $params['cultureCode'];
		
		$filters = $params['filters'];
//				$filtersselected = BFCHelper::getFilterSearchParamsSession();
		if(empty($filters)){
			$filters = BFCHelper::getFilterSearchParamsSession();		
		}
		$resourceName = $params['resourceName'].'';
		$refid = $params['refid'].'';
		if (!empty($refid) or !empty($resourceName))  {
			$options['data']['calculate'] = 0;
			$options['data']['checkAvailability'] = 0;
			
			if (isset($refid) && $refid <> "" ) {
				$options['data']['refId'] = '\''.$refid.'\'';
			}
			if (isset($resourceName) && $resourceName <> "" ) {
				$options['data']['resourceName'] = '\''. $resourceName.'\'';
			}
		}else{
		
			$onlystay = $params['onlystay'];
				
			$options['data']['calculate'] = $onlystay;
			$options['data']['checkAvailability'] = $onlystay;
			
			if (isset($params['locationzone']) ) {
				$locationzone = $params['locationzone'];
			}
			if (isset($masterTypeId) && $masterTypeId > 0) {
				$options['data']['masterTypeIds'] = '\'' .$masterTypeId.'\'';
			}

			if (!empty($merchantCategoryId) && $merchantCategoryId > 0) {
				$options['data']['merchantCategoryIds'] = '\'' .$merchantCategoryId.'\'';
			}
			
			if(empty($duration)){
				$duration = 0;
			}
			if ((isset($checkin))) {
				$options['data']['checkin'] = '\'' . $checkin->format('Ymd') . '\'';
				$options['data']['duration'] = $duration;
			}

			if (isset($availabilitytype) ) {
				$options['data']['availabilityTypes'] = '\'' .$availabilitytype .'\'';
			}
			if (isset($itemtypes) ) {
				$options['data']['itemTypeIds'] = '\'' .$itemtypes .'\'';
			}

			if (isset($groupresulttype) ) {
				$options['data']['groupResultType'] = $groupresulttype;
//				if ($groupresulttype==1 || $groupresulttype==2) { //onbly for merchants 
					$options['data']['getBestGroupResult'] = 1;
//				}
			}

			$points = isset($params['points']) ? $params['points'] : '' ;
			if (isset($points) && $points !='') {
				$options['data']['points'] = '\'' . $points. '\'';
			}

			if (isset($persons) && $persons > 0) {
				$options['data']['paxes'] = $persons;
				if (isset($paxages)) {
					$options['data']['paxages'] = '\'' . implode('|',$paxages) . '\'';
					// ciclo per aggiungere i dati
					$newpaxages = array();
					foreach ($paxages as $age) {
						if ($age >= BFCHelper::$defaultAdultsAge) {
							if ($age >= BFCHelper::$defaultSenioresAge) {
								array_push($newpaxages, $age.":".bfiAgeType::$Seniors);
							} else {
								array_push($newpaxages, $age.":".bfiAgeType::$Adult);
							}
						} else {
							array_push($newpaxages, $age.":".bfiAgeType::$Reduced);
						}
					}

					$options['data']['paxages'] = '\'' . implode('|',$newpaxages) . '\'';
				}else{
					$px = array_fill(0,$persons,BFCHelper::$defaultAdultsAge.":".bfiAgeType::$Adult);
					$options['data']['paxages'] = '\'' . implode('|',$px) . '\'';
				}
			}
			
//				$options['data']['pricetype'] = '\'' . 'rateplan' . '\'';

			if (isset($locationzone) && $locationzone !='' && $locationzone !='0') {
				$options['data']['zoneIds'] = '\''. $locationzone . '\'';
			}
			
			if (!empty($tags)) {
				$options['data']['tagids'] = '\'' . $tags . '\'';
			}				
		}


		if (isset($cultureCode) && $cultureCode !='') {
			$options['data']['cultureCode'] = '\'' . $cultureCode. '\'';
		}
		if (isset($searchid) && $searchid !='') {
			$options['data']['searchid'] = '\'' . $searchid. '\'';
		}
		
		if (isset($merchantId) && $merchantId > 0) {
			$options['data']['merchantid'] = $merchantId;
		}

		if (isset($stateIds) && $stateIds !='') {
			$options['data']['stateIds'] = '\'' . $stateIds. '\'';
		}

		if (isset($regionIds) && $regionIds !='') {
			$options['data']['regionIds'] = '\'' . $regionIds. '\'';
		}

		if (isset($cityIds) && $cityIds !='') {
			$options['data']['cityIds'] = '\'' . $cityIds. '\'';
		}

		if (isset($merchantIds) && $merchantIds !='') {
			$options['data']['merchantsList'] = '\'' . $merchantIds. '\'';
		}

		if (isset($merchantTagIds) && $merchantTagIds !='') {
			$options['data']['merchantTagsIds'] = '\'' . $merchantTagIds. '\'';
		}

		if (isset($productTagIds) && $productTagIds !='') {
			$options['data']['tagids'] = '\'' . $productTagIds. '\'';
		}



		if (!empty($this->currentOrdering )) {
			$options['data']['orderby'] = '\'' . $this->currentOrdering . '\'';
			$options['data']['ordertype'] = '\'' . $this->currentDirection . '\'';
		}

//filters[price]:200;
//filters[resourcescategories]:6
//filters[rating]:0
//filters[avg]:0
//filters[meals]:
//filters[merchantsservices]:
//filters[resourcesservices]:
//filters[zones]:
//filters[bookingtypes]:
//filters[offers]:
//filters[tags]:
//filters[rooms]:
//filters[paymodes]:			
		if(!empty( $filters )){
			if(!empty( $filters['price'] )){
				$options['data']['priceRange'] = BFCHelper::getQuotedString($filters['price']) ;
			}
			if(!empty( $filters['resourcescategories'] )){
				$options['data']['masterTypeIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['resourcescategories'])) ;
			}
			if(!empty( $filters['rating'] )){
				$options['data']['ratingIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['rating'])) ;
			}
			if(!empty( $filters['avg'] )){
				if (isset($groupresulttype) ) {
					if ($groupresulttype==1 ) { //onbly for merchants 
						$options['data']['mrcAvgs'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['avg'])) ;
					}else{
						$options['data']['resAvgs'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['avg'])) ;
					}
				}else{
					$options['data']['resAvgs'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['avg'])) ;
				}
			}
			if(!empty( $filters['meals'] )){
				$options['data']['includedMeals'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['meals'])) ;
			}
			if(!empty( $filters['merchantsservices'] )){
				$options['data']['merchantServiceIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['merchantsservices'])) ;
			}
			if(!empty( $filters['resourcesservices'] )){
				$options['data']['resourceServiceIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['resourcesservices'])) ;
			}
			if(!empty( $filters['zones'] )){
				$options['data']['zoneIds'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['zones'])) ;
			}
			if(!empty( $filters['bookingtypes'] )){
				$options['data']['requirePaymentsOnly'] = 1 ;
			}
			if(!empty( $filters['offers'] )){
				$options['data']['discountedPriceOnly'] = 1 ;
			}
			if(!empty( $filters['tags'] )){
				$options['data']['tagids'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['tags'])) ;
			}
			if(!empty( $filters['rooms'] )){
				$options['data']['bedRooms'] = BFCHelper::getQuotedString(str_replace("|",",",$filters['rooms'])) ;
			}
			if(!empty( $filters['paymodes'] )){
				if (strpos($filters['paymodes'],"freecancellation")!== FALSE) {
					$options['data']['freeCancellation'] = 1 ;
				}
				if (strpos($filters['paymodes'],"freepayment")!== FALSE) {
					$options['data']['payOnArrival'] = 1 ;
				}
				if (strpos($filters['paymodes'],"freecc")!== FALSE) {
					$options['data']['freeDeposit'] = 1 ;
				}
			}

		}

		
//		if ($filters!='')
//			$options['data']['$filter'] = $filter;
	}

	
	public function getSearchResults($start, $limit, $ordering, $direction, $ignorePagination = false, $jsonResult = false) {

		$this->currentOrdering = $ordering;
		$this->currentDirection = $direction;

		$params = BFCHelper::getSearchParamsSession();
								
		$searchid = !empty($params['searchid']) ? $params['searchid'] : uniqid('', true);
		$newsearch = isset($params['newsearch']) ? $params['newsearch'] : '0';
//		$pricerange = $params['pricerange'];
		$merchantResults = $params['merchantResults'];
		$condominiumsResults = $params['condominiumsResults'];
		$sessionkey = 'search.' . $searchid . '.results';			

		//$session = JFactory::getSession();
		$results = $this->currentData;

		if($newsearch == "1"){
			BFCHelper::setFilterSearchParamsSession(null);
		}else{
			$filtersselected = BFCHelper::getArray('filters', null);
			if ($filtersselected == null) { //provo a recuperarli dalla sessione...
				$filtersselected = BFCHelper::getFilterSearchParamsSession();
			}
			BFCHelper::setFilterSearchParamsSession($filtersselected);
		}
			

		if ($results == null) {
			$options = array(
				'path' => $this->urlSearch,
				'data' => array(
						'$format' => 'json',
						'topRresult' => 0,
						'lite' => 1
				)
			);
			
			if(!$ignorePagination){
				if (isset($start) && $start >= 0) {
					$options['data']['skip'] = $start;
				}
				
				if (isset($limit) && $limit > 0) {
					$options['data']['topRresult'] = $limit;
				}
			}

			$this->applyDefaultFilter($options);

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

						
			$filtersenabled = array();
			if(!empty($results)){
				$filtersenabled = json_decode($results->FiltersString);
				$params['merchantResults'] = ($results->GroupResultType==1);
				$params['condominiumsResults'] = ($results->GroupResultType==2);
				$merchantResults = $params['merchantResults'];
				$condominiumsResults = $params['condominiumsResults'];
			}
			BFCHelper::setSearchParamsSession($params);
			if($newsearch == "1"){
				BFCHelper::setFirstFilterSearchParamsSession($filtersenabled);
			}
			BFCHelper::setEnabledFilterSearchParamsSession($filtersenabled);
		}
		$resultsItems = null;

		if(isset($results->ItemsCount)){
			$this->count = $results->ItemsCount;
			$resultsItems = json_decode($results->ItemsString);
		}

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
				elseif ($condominiumsResults){
					$val->Resource = new StdClass;
					$val->Resource->CondominiumId = $result->CondominiumId;
					$val->Resource->ResourceId = $result->ResourceId;
					$val->Resource->XGooglePos = $result->ResLat;
					$val->Resource->YGooglePos = $result->ResLng;
					$val->Resource->ResourceName = BFCHelper::getSlug($result->ResName);
					$val->Resource->Price = $result->Price;
				}
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
	
	public function getTotal()
	{
		if ($this->count !== null){
			return $this->count;
		}
		else{
			$this->retrieveItems();
		}

	}
	
	public function getMasterTypologiesFromService($onlyEnabled = true, $language='') {
		$options = array(
				'path' => $this->urlMasterTypologies,
				'data' => array(
					/*'$filter' => 'IsEnabled eq true',*/
					'typeId' => '1',
					'cultureCode' =>  BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);
			
		if ($onlyEnabled) {
//			$options['data']['$filter'] = 'Enabled eq true';
			$options['data']['isEnable'] = 'true';
		}
		
		$url = $this->helper->getQuery($options);
		
		$typologies = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$typologies = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$typologies = $res->d->results;
			}elseif(!empty($res->d)){
				$typologies = $res->d;
			}
		}
		
		return $typologies;
	}

	public function getMasterTypologies($onlyEnabled = true, $language='') {
	  $typologies = $this->getMasterTypologiesFromService($onlyEnabled);
     return $typologies;
	}

		
	protected function populateState($ordering = NULL, $direction = NULL) {
//(in_array(BFCHelper::getInt('merchantCategoryId'), BFCHelper::getCategoryMerchantResults(BFCHelper::getVar('cultureCode')), true))
		$ci = BFCHelper::getStayParam('checkin', new DateTime());
//		$isMerchantResults = false;
//		if( (in_array(BFCHelper::getInt('masterTypeId'), BFCHelper::getTypologiesMerchantResults(), true) || (in_array(BFCHelper::getInt('merchantCategoryId'), BFCHelper::getCategoryMerchantResults(), true))){
//			$isMerchantResults = true;
//		}
//		if (BFCHelper::getInt('masterTypeId') > 0 || BFCHelper::getInt('merchantCategoryId') > 0) {
//		$condominiumsResults = BFCHelper::getVar('condominiumsResults');
//		$merchantResults = (in_array(BFCHelper::getInt('merchantCategoryId'), BFCHelper::getCategoryMerchantResults(BFCHelper::getVar('cultureCode')), false));
//		if ($condominiumsResults) {
//			$merchantResults = false;
//		}

		if (BFCHelper::getInt('newsearch') ==1) {						
			$condominiumsResults = BFCHelper::getVar('condominiumsResults');
			$merchantResults = (!empty(BFCHelper::getInt('merchantCategoryId')) && in_array(BFCHelper::getInt('merchantCategoryId'), BFCHelper::getCategoryMerchantResults(BFCHelper::getVar('cultureCode')), false));
			if ($condominiumsResults) {
				$merchantResults = false;
			}
			$filtersservices = BFCHelper::getVar('filtersservices');
			$bookableonly = BFCHelper::getVar('bookableonly');
//			$filters = BFCHelper::getVar('filters');
			$filters = BFCHelper::getArray('filters');
			
			if(empty($filters )){
				$filters =array();
			}
			if(!empty($bookableonly)){
				$filters['bookingtypes'] = $bookableonly;	
			}
			if(!empty($filtersservices)){
				if(empty($filters['services'])){
					$filters['services'] = $filtersservices;	
				}else{
					$filters['services'] .= ',' .$filtersservices;
				}
			}
	$availabilitytype =  isset($_REQUEST['availabilitytype']) ? $_REQUEST['availabilitytype'] : 1;
			$currParam = array(
				'searchid' => BFCHelper::getVar('searchid',uniqid('', true)),
				'checkin' => BFCHelper::getStayParam('checkin', new DateTime()),
				'checkout' => BFCHelper::getStayParam('checkout', $ci->modify(BFCHelper::$defaultDaysSpan)),
				'duration' => BFCHelper::getStayParam('duration'),
				'paxages' => BFCHelper::getStayParam('paxages'),
				/*'pricetype' => BFCHelper::getStayParam('pricetype'),*/
				'masterTypeId' => BFCHelper::getInt('masterTypeId'),
				'merchantResults' => $merchantResults,
				'merchantCategoryId' => BFCHelper::getVar('merchantCategoryId'),
				'merchantId' => BFCHelper::getInt('merchantId',0),
				'zoneId' => BFCHelper::getInt('locationzone',0),
				'groupresulttype' => BFCHelper::getInt('groupresulttype',0),
				'searchtypetab' => isset($_REQUEST['searchtypetab']) ? $_REQUEST['searchtypetab'] : -1,
				'availabilitytype' => $availabilitytype,
				'locationzone' => BFCHelper::getInt('locationzone',0),
				'cultureCode' => BFCHelper::getVar('cultureCode'),
				'paxes' => BFCHelper::getInt('persons'),
				'resourceName' => BFCHelper::getVar('resourceName',""),
				'refid' => BFCHelper::getVar('refid',""),
				'condominiumsResults' => $condominiumsResults,
				'pricerange' => BFCHelper::getVar('pricerange'),
				'onlystay' => BFCHelper::getVar('onlystay'),
				'tags' => BFCHelper::getVar('tags'),
				'filters' => $filters,
				'bookableonly' => BFCHelper::getVar('bookableonly'),
		'stateIds' => isset($_REQUEST['stateIds']) ? $_REQUEST['stateIds'] : '',
		'regionIds' => isset($_REQUEST['regionIds']) ? $_REQUEST['regionIds'] : '',
		'cityIds' => isset($_REQUEST['cityIds']) ? $_REQUEST['cityIds'] : '',
		'itemtypes' => isset($_REQUEST['itemtypes']) ? $_REQUEST['itemtypes'] : '',
				'newsearch' => 1

			);
			$this->setState('params', $currParam);	
								
		} else { // try to get params from session 
			$pars = BFCHelper::getSearchParamsSession();

			try {
				$tags = BFCHelper::getVar('tags');
				if (isset($pars['tags']) && empty($tags)){
					$tags =  $pars['tags'];
				}

				$filters = BFCHelper::getArray('filters');
								
				$currParam = array(
					'searchid' =>  BFCHelper::getVar('searchid',uniqid('', true)),
					'checkin' => $pars['checkin'],
					'checkout' => $pars['checkout'],
					'duration' => $pars['duration'],
					'masterTypeId' => $pars['masterTypeId'],
					'merchantResults' => $pars['merchantResults'],//$merchantResults,
					'merchantCategoryId' => $pars['merchantCategoryId'],
					'merchantId' => $pars['merchantId'],
					'paxes' => $pars['paxes'],
					'paxages' => $pars['paxages'],
					'locationzone' =>  $pars['zoneId'],
					'cultureCode' =>  $pars['cultureCode'],
					'resourceName' => $pars['resourceName'],
					'refid' => $pars['refid'],
					'condominiumsResults' => $pars['condominiumsResults'],
					'pricerange' => $pars['pricerange'],
					'onlystay' => BFCHelper::getVar('onlystay', $pars['onlystay']),
//					'filters' => BFCHelper::getVar('filters', $pars['filters']),
					'filters' => $filters,
					'bookableonly' => BFCHelper::getVar('bookableonly', $pars['bookableonly']),
					'tags' => $tags,
					'groupresulttype' => BFCHelper::getInt('groupresulttype',0),
					'searchtypetab' => isset($_REQUEST['searchtypetab']) ? $_REQUEST['searchtypetab'] : -1,
					'stateIds' => isset($_REQUEST['stateIds']) ? $_REQUEST['stateIds'] : '',
					'regionIds' => isset($_REQUEST['regionIds']) ? $_REQUEST['regionIds'] : '',
					'cityIds' => isset($_REQUEST['cityIds']) ? $_REQUEST['cityIds'] : '',
					'itemtypes' => isset($_REQUEST['itemtypes']) ? $_REQUEST['itemtypes'] : '',
					'availabilitytype' =>  isset($_REQUEST['availabilitytype']) ? $_REQUEST['availabilitytype'] : 1,

					//		'availabilitytype' => $availabilitytype,
					'newsearch' => BFCHelper::getVar('newsearch', "0")
				);
			} catch (Exception $e) {
				
				$condominiumsResults = BFCHelper::getVar('condominiumsResults');
				$merchantResults = (!empty(BFCHelper::getVar('merchantCategoryId')) && in_array(BFCHelper::getInt('merchantCategoryId'), BFCHelper::getCategoryMerchantResults(BFCHelper::getVar('cultureCode')), false));
				if ($condominiumsResults) {
					$merchantResults = false;
				}
				$filtersservices = BFCHelper::getVar('filtersservices');
				$bookableonly = BFCHelper::getVar('bookableonly');
//				$filters = BFCHelper::getVar('filters');
				$filters = BFCHelper::getArray('filters');
				if(empty($filters )){
					$filters =array();
				}
				if(!empty($bookableonly)){
					$filters['bookingtypes'] = $bookableonly;	
				}
				if(!empty($filtersservices)){
					if(empty($filters['services'])){
						$filters['services'] = $filtersservices;	
					}else{
						$filters['services'] .= ',' .$filtersservices;
					}
				}
				$currParam = array(
					'searchid' =>  BFCHelper::getVar('searchid',uniqid('', true)),
					'checkin' => BFCHelper::getStayParam('checkin', new DateTime()),
					'checkout' => BFCHelper::getStayParam('checkout', $ci->modify(BFCHelper::$defaultDaysSpan)),
					'duration' => BFCHelper::getStayParam('duration'),
					'paxages' => BFCHelper::getStayParam('paxages'),
					/*'pricetype' => BFCHelper::getStayParam('pricetype'),*/
					'masterTypeId' => BFCHelper::getInt('masterTypeId'),
					'merchantResults' => $merchantResults,
					'merchantCategoryId' => BFCHelper::getInt('merchantCategoryId'),
					'merchantId' => BFCHelper::getInt('merchantId',0),
					'zoneId' => BFCHelper::getInt('locationzone',0),
					'locationzone' => BFCHelper::getInt('locationzone',0),
					'cultureCode' => BFCHelper::getVar('cultureCode'),
					'paxes' => BFCHelper::getInt('persons'),
					'resourceName' => BFCHelper::getVar('resourceName',""),
					'refid' => BFCHelper::getVar('refid',""),
					'condominiumsResults' => $condominiumsResults,
					'pricerange' => BFCHelper::getVar('pricerange'),
					'onlystay' => BFCHelper::getVar('onlystay'),
					'tags' => BFCHelper::getVar('tags'),
					'filters' => $filters,
					'bookableonly' => BFCHelper::getVar('bookableonly'),
					'groupresulttype' => BFCHelper::getInt('groupresulttype',0),
					'searchtypetab' => isset($_REQUEST['searchtypetab']) ? $_REQUEST['searchtypetab'] : -1,
					'availabilitytype' => $availabilitytype,
					'stateIds' => isset($_REQUEST['stateIds']) ? $_REQUEST['stateIds'] : '',
					'regionIds' => isset($_REQUEST['regionIds']) ? $_REQUEST['regionIds'] : '',
					'cityIds' => isset($_REQUEST['cityIds']) ? $_REQUEST['cityIds'] : '',
					'itemtypes' => isset($_REQUEST['itemtypes']) ? $_REQUEST['itemtypes'] : '',
					'availabilitytype' =>  isset($_REQUEST['availabilitytype']) ? $_REQUEST['availabilitytype'] : 1,
					'newsearch' => 1

				);
			}



			$this->setState('params',$currParam);
		}
BFCHelper::setSearchParamsSession($currParam);
//		$filter_order = BFCHelper::getCmd('filter_order','stay');
//		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');
		$filter_order = BFCHelper::getCmd('filter_order');
		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir');
//		return parent::populateState($filter_order, $filter_order_Dir);
		parent::populateState($filter_order, $filter_order_Dir);
	}

	public function getItems($ignorePagination = false, $jsonResult = false, $start = 0, $count = 20) {
		if ($this->currentData !== null){
			return $this->currentData;
		}
		else{
			$start = $this->getState('list.start'); 
			$count = $this->getState('list.limit');
			$this->retrieveItems($ignorePagination, $jsonResult, $start, $count);
		}
		return $this->currentData;
	}

	public function retrieveItems($ignorePagination = false, $jsonResult = false, $start = 0, $count = 20) {
		
		if(!empty($_REQUEST['filter_order']) ){
			$items = $this->getSearchResults(
				$start,
				$count,
				$_REQUEST['filter_order'],
				$_REQUEST['filter_order_Dir'],
				$ignorePagination,
				$jsonResult
			);
		}
		else {
			$items = $this->getSearchResults(
				$start,
				$count,
				'',
				'',
				$ignorePagination,
				$jsonResult
			);
		}
		
		$this->currentData = $items;
	}

	public function SearchResult($term, $language, $limit, $onlyLocations=0) {
		if(empty( $onlyLocations)){
			$onlyLocations = 0;
		}
		
		$options = array(
			'path' => $this->urlSearchResult,
			'data' => array(
					'$format' => 'json',
					'term' => BFCHelper::getQuotedString($term),
					'onlyLocations' => $onlyLocations,
					'cultureCode' =>  BFCHelper::getQuotedString($language),
					'top' => 0
			)
		);
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}

		$url = $this->helper->getQuery($options);

		$results = array();

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->SearchResult)){
				$results = $res->d->SearchResult;
			}elseif(!empty($res->d)){
				$results = $res->d;
			}
		}
		
		return $results;
	}
}
