<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelitem library
jimport('joomla.application.component.modellist');
set_time_limit(300);
$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . '/helpers/wsQueryHelper.php';
require_once $pathbase . '/helpers/BFCHelper.php';
require_once $pathbase . '/helpers/FormHelper.php';

/**
 * BookingForConnectorModelMerchants Model
 */
class BookingForConnectorModelSearch extends JModelList
{
	private $urlSearch = null;
	private $urlSearchAll = null;
	private $urlSearchAllCalculate = null;
	private $urlSearchAllCount = null;
	private $urlSearchAllCountMerchant = null;
	private $urlSearchCount = null;
	private $urlSearchByMerchant = null;
	private $urlSearchMerchants = null;
	private $urlSearchMerchantsCount = null;
	private $urlMasterTypologies = null;
	private $urlStay = null;
	private $urlAllRatePlansStay = null;
	private $helper = null;
	private $currentOrdering = null;
	private $currentDirection = null;
	private $count = null;
	private $urlGetCalculateResourcesByIds = null;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlSearch = '/Search';
		$this->urlSearchCount = '/Search/$count';
		$this->urlSearchMerchants = '/SearchMerchants';
		$this->urlSearchMerchantsCount = '/SearchMerchants/$count';
		$this->urlMasterTypologies = '/GetMasterTypologies';
		$this->urlSearchByMerchant = '/SearchByMerchant';
		$this->urlStay = '/GetStay';
		$this->urlAllRatePlansStay = '/GetAllRatePlansStay';
		$this->urlSearchAll = '/SearchAllPlus';
		$this->urlSearchAllCalculate = '/SearchAllLiteNew';
//		$this->urlSearchAllCalculate = '/SearchAllGeo';
		//$this->urlSearchAllCalculate = '/SearchAllPlusCalculable';
		$this->urlSearchAllCount = '/SearchAllCountPlus';
		$this->urlSearchAllCountMerchant = '/SearchAllCountMerchant';
		$this->urlGetCalculateResourcesByIds = '/GetResourcesCalculateByIds';
	}
	
	public function applyDefaultFilter(&$options) {
		$params = $this->getState('params');
				
		$masterTypeId = $params['masterTypeId'];
		$checkin = $params['checkin'];
		$checkout = $params['checkout'];
		$duration = $params['duration'];
		$persons = $params['paxes'];
		$merchantCategoryId = $params['merchantCategoryId'];
		$paxages = $params['paxages'];
		$merchantId = $params['merchantId'];
		$tags = $params['tags'];

		$cultureCode = $params['cultureCode'];
	
		$filter = '';
		
		$resourceName = $params['resourceName'].'';
		$refid = $params['refid'].'';
		if (!empty($refid) or !empty($resourceName))  {
			$options['data']['calculate'] = 0;
			
			if (isset($refid) && $refid <> "" ) {
				$options['data']['refId'] = '\''.$refid.'\'';
			}
			if (isset($resourceName) && $resourceName <> "" ) {
				$options['data']['resourceName'] = '\''. $resourceName.'\'';
			}
		}else{
		
			$onlystay = $params['onlystay'];
				
			$options['data']['calculate'] = $onlystay;
			
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
			
			if (!empty($tags)) {
				$options['data']['tagids'] = '\''. $tags.'\'';
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
	
	private function purgeSessionValues($session, $searchid) {
		$filtersKey = $this->getFiltersKey();	
		$keys = array(
			'search.' . $searchid . '.count', 
			'search.' . $searchid . '.results',
			'search.' . $searchid . '.' . $filtersKey . '.results',
			'search.filterparams'
		);
		foreach ($keys as $key) {
			$session->set($key, null, 'com_bookingforconnector');
		}
	}
	
	public function getSearchResults($start, $limit, $ordering, $direction, $ignorePagination = false, $jsonResult = false) {

		$this->currentOrdering = $ordering;
		$this->currentDirection = $direction;
		
		$params = $this->getState('params');
		$searchid = $params['searchid'];
		$newsearch = $params['newsearch'];

		$pricerange = $params['pricerange'];
		$merchantResults = $params['merchantResults'];
		$condominiumsResults = $params['condominiumsResults'];
		
		$sessionkey = 'search.' . $searchid . '.results';

		$session = JFactory::getSession();
		$results = null;
								
		if($newsearch == "0"){

			
			$cachedresults = $session->get($sessionkey); 
			try {
				if (isset($cachedresults) && !empty($cachedresults) )
				$results = (array)json_decode(gzuncompress(base64_decode($cachedresults)));
		//			echo 'sessionkey: ',  $sessionkey, "<br />";
				//$results = $cachedresults;
			} catch (Exception $e) {
	//			echo 'Exception: ',   $e->getMessage(), "<br />";
				//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}else{
			
			BFCHelper::setFilterSearchParamsSession(null);
		}
		
		if ($results == null) {
//			echo 'No result: <br />';
			$options = array(
				'path' => $this->urlSearchAllCalculate,
				'data' => array(
						'$format' => 'json',
						'topRresult' => 0,
//						'calculate' => 1, // spostato nell'applicazione dei filtri altrimenti mi calcola i prezzi anche se non voglio
						'lite' => 1
				)
			);
			$this->applyDefaultFilter($options);

			$url = $this->helper->getQuery($options);

			$results = null;

			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
//				$results = $res->d->results ?: $res->d;
				if (!empty($res->d->results)){
					$results = $res->d->results;
				}elseif(!empty($res->d)){
					$results = $res->d;
				}
				try {				
					if (!empty($results)) {
//						shuffle($results);
						$resultsCat = array();
						$resultsBook = array();
						$resultsCat = array_filter($results, function($result) {
							return $result->IsCatalog ;
						});
						if (!empty($resultsCat)){
							shuffle($resultsCat);
							$resultsBook = array_filter($results, function($result) {
								return !$result->IsCatalog ;
							});
							if (!empty($resultsBook)){
								shuffle($resultsBook);
								$results = array_merge($resultsBook,$resultsCat);
							}else{
								$results = $resultsCat;
							}
						}else{
							shuffle($results);
						}

					}
				} catch (Exception $e) {
					//echo 'Caught exception: ',  $e->getMessage(), "\n";
				}
			}
			// purge last searchid. clears unusable session data
			
//			$lastsearchid = $session->get('search.last', '', 'com_bookingforconnector');			
//			
//			if ($lastsearchid != '') {
//				$this->purgeSessionValues($session, "booking");
//				$session->set('search.last','', 'com_bookingforconnector');
//				// purge static searchresult
//				//BFCHelper::setSearchResult($lastsearchid, null);
//			}

			// saves parameters into session
			BFCHelper::setSearchParamsSession($params);
//			if(!empty($results)){
//				if($pricerange !=='0' && strpos($pricerange,'|') !== false ){ // se ho un valore per pricerange diverso da 0 allora splitto per ; 
//					$priceranges = explode("|", $pricerange);
//					$pricemin = $priceranges[0];
//					$pricemax = $priceranges[1];
//
//
//					// price min filtering
//					if ($pricemin > 0) {
//						$results = array_filter($results, function($result) use ($pricemin) {
//							return $result->Price >= $pricemin;
//						});
//					}
//					// price max filtering
//					if ($pricemax > 0) {
//						$results = array_filter($results, function($result) use ($pricemax) {
//							return $result->Price <= $pricemax;
//						});
//					}
//				}
//			}
			
		$onlystay = $params['onlystay'];
		if(!empty($results) && $onlystay =='1'){
				$results = array_filter($results, function($result) {
//					return $result->Price >0 ;
					return $result->IsCatalog || (!$result->IsCatalog && $result->Price > 0);
				});
		}

		try {							
			// save current search in session to disable all further calculations upon reordering and filtering
			$compr = base64_encode(gzcompress(json_encode($results),true));
		} catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}




			$session->set($sessionkey, $compr); 
			
			//ciclo per filtrare i possibili filtri
			$filtersenabled = array();
			
			$filtersenabled['count'] = 0;
			
			if(!empty($results)){
			
				$filtersenabled['count'] = count($results);
			
				//per condominiumsResults non \E8 necessario filtrare i condomini per caratteristiche, si basano sulle risorse
				if ($merchantResults) {
//					$tmpstars = array_unique(array_map(function ($i) { return $i->MrcRating; }, $results));
//					$filtersenabled['stars'] = implode(',',$tmpstars);
					$filtersenabled['stars'] = array_count_values(array_filter(array_map(function ($i) { return $i->MrcRating; }, $results)));
//					$tmplocationzones = array_unique(array_map(function ($i) { return $i->MrcZoneId; }, $results));	
//					$filtersenabled['locationzones'] = implode(',',$tmplocationzones);
					$filtersenabled['locationzones'] = array_count_values(array_map(function ($i) { return $i->MrcZoneId; }, $results));	
				}else{
//					$tmpstars = array_unique(array_map(function ($i) { return $i->ResRating; }, $results));
//					$filtersenabled['stars'] = implode(',',$tmpstars);
					$filtersenabled['stars'] = array_count_values(array_filter(array_map(function ($i) { return $i->ResRating; }, $results)));
//					$tmplocationzones = array_unique(array_map(function ($i) { return $i->ResZoneId; }, $results));	
//					$filtersenabled['locationzones'] = implode(',',$tmplocationzones);
					$filtersenabled['locationzones'] = array_count_values(array_map(function ($i) { return $i->ResZoneId; }, $results));	

					$tmpRooms = array_count_values(array_map(function ($i) { return $i->Rooms; }, $results));	
//					asort($tmpRooms);
					ksort($tmpRooms);

//					$filtersenabled['rooms'] = implode(',',$tmpRooms);
					$filtersenabled['rooms'] =  $tmpRooms;

				}

				
//				$tmpRateplanName = array_unique(array_map(function ($i) { return $i->RateplanName; }, $results));	
//				$filtersenabled['rateplanname'] = implode(',',$tmpRateplanName);
				$filtersenabled['rateplanname'] = array_count_values(array_filter(array_map(function ($i) { return $i->RateplanName; }, $results)));	

//				$tmpmastertypologies = array_unique(array_map(function ($i) { return $i->MasterTypologyId; }, $results));	
//				$filtersenabled['mastertypologies'] = implode(',',$tmpmastertypologies);
				$filtersenabled['mastertypologies'] = array_count_values(array_map(function ($i) { return $i->MasterTypologyId; }, $results));	

				
				// elenco merchantGroup presenti nella ricerca
//				$tmpmerchantgroups = array_unique(explode(",",array_reduce($results, 
//						function($returnedList, $item){
//							$val =  preg_replace('/\s+/', '', $item->MrcTagsIdList);
//							if (!empty($val)) {
//								$returnedList .= "," .$val;
//							}
//							return $returnedList;
//						}
//						)));
//				foreach( $tmpmerchantgroups as $key => $value ) {
//					if( empty( $tmpmerchantgroups[ $key ] ) )
//						unset( $tmpmerchantgroups[ $key ] );
//				}		
//				$filtersenabled['merchantgroups'] = implode(',',$tmpmerchantgroups);
				
				$tmpmerchantgroups = array_count_values(explode(",",array_reduce($results, 
						function($returnedList, $item){
							$val =  preg_replace('/\s+/', '', $item->MrcTagsIdList);
							if (!empty($val)) {
								$returnedList .= "," .$val;
							}
							$resval =  preg_replace('/\s+/', '', $item->TagsIdList);
							if (!empty($resval)) {
								$returnedList .= "," .$resval;
							}
							return $returnedList;
						}
						)));
//				foreach( $tmpmerchantgroups as $key => $value ) {
//					if( empty( $tmpmerchantgroups[ $key ] ) )
//						unset( $tmpmerchantgroups[ $key ] );
//				}		
				$filtersenabled['merchantgroups'] =  $tmpmerchantgroups;

				// elenco Servizi presenti nella ricerca
//				$tmpservices = array_unique(explode(",",array_reduce($results,
//						function($returnedList, $item){
//							$val =  preg_replace('/\s+/', '', $item->MrcServiceIdList);
//							if (!empty($val)) {
//								$returnedList .= "," .$val;
//							}								
//							$val =  preg_replace('/\s+/', '', $item->ResServiceIdList);
//							if (!empty($val)) {
//								$returnedList .= "," .$val;
//							}
//							
//							return $returnedList;
//						}
//				)));
//				foreach( $tmpservices as $key => $value ) {
//					if( empty( $tmpservices[ $key ] ) )
//						unset( $tmpservices[ $key ] );
//				}
//				$filtersenabled['services'] = implode(',',$tmpservices);
				$tmpservices = array_count_values(explode(",",array_reduce($results,
						function($returnedList, $item){
							$val =  preg_replace('/\s+/', '', $item->ResServiceIdList);
							if (!empty($val)) {
								$returnedList .= "," .$val;
							}
							return $returnedList;
						}
				)));
//				foreach( $tmpservices as $key => $value ) {
//					if( empty( $tmpservices[ $key ] ) )
//						unset( $tmpservices[ $key ] );
//				}
				$filtersenabled['services'] = $tmpservices;

				$tmpservicesmerchants = array_count_values(explode(",",array_reduce($results,
						function($returnedList, $item){
							$val =  preg_replace('/\s+/', '', $item->MrcServiceIdList);
							if (!empty($val)) {
								$returnedList .= "," .$val;
							}								
							return $returnedList;
						}
				)));
				$filtersenabled['servicesmerchants'] = $tmpservicesmerchants;
				
				// elenco BookingType presenti nella ricerca
//				bookableonly
//				$tmpbookingtype = array_unique(array_map(function ($i) { return $i->BookingType; }, $results));
//				$tmpbookingtype = array_unique(array_map(function ($i) { return $i->IsBookable; }, $results));
//				foreach( $tmpbookingtype as $key => $value ) {
//					if( empty( $tmpbookingtype[ $key ] ) )
//						unset( $tmpbookingtype[ $key ] );
//				}
//				$filtersenabled['bookingtypes'] = implode(',',$tmpbookingtype);
				$filtersenabled['bookingtypes'] = array_count_values(array_filter(array_map(function ($i) { return intval($i->IsBookable); }, $results)));
				
//				$tmpoffers = array_unique(array_map(function ($i) { return $i->TotalPrice>$i->Price; }, $results));	
//				$tmpoffers = array_unique(array_map(function ($i) { return !empty($i->DiscountId); }, $results));	
//				$tmpoffers = array_unique(array_map(function ($i) { return $i->IsOffer; }, $results));
//				foreach( $tmpoffers as $key => $value ) {
//					if( empty( $tmpoffers[ $key ] ) )
//						unset( $tmpoffers[ $key ] );
//				}
//				$filtersenabled['offers'] = implode(',',$tmpoffers);
				$tmpoffers = array_count_values(array_filter(array_map(function ($i) { return intval($i->IsOffer && $i->TotalPrice>$i->Price); }, $results)));
				$filtersenabled['offers'] = $tmpoffers;



				$prices = array_map(function ($i) { return $i->Price; }, array_filter($results, function($rs) { return !$rs->IsCatalog; })) ;
//				$prices = array_map(function ($i) { return $i->Price; }, $results) ;

				if(!empty($prices )) {
					$filtersenabled['pricemin'] = round(min($prices)-1, 0, PHP_ROUND_HALF_DOWN);
					$filtersenabled['pricemax'] =  round(max($prices)+1, 0, PHP_ROUND_HALF_UP);
				}


			}
						
			BFCHelper::setEnabledFilterSearchParamsSession($filtersenabled);
		}

		$results = $this->filterResults($results);

		// ordering is taking place here only for simple results, merchants are ordered by the grouping function
		if(!empty($results)) {
			if (isset($ordering) && !$merchantResults) {
				$catValues = array();
				foreach($results as $key => $row) {
					$catValues[$key]  = $row->IsCatalog;
				}

				switch (strtolower($ordering)) {
					case 'stay':
						$pricesValues = array();
						foreach($results as $key => $row) {
							$pricesValues[$key]  = $row->TotalPrice;
						}
						array_multisort($catValues, SORT_ASC, $pricesValues, ($direction == 'desc' ? SORT_DESC : SORT_ASC), $results);
	//					usort($results, function($a,$b) use ( $ordering, $direction) {
	//						return BFCHelper::orderBy($a, $b, 'TotalPrice', $direction);
	//					});
						break;
					case 'rooms':
						$RoomsValues = array();
						foreach($results as $key => $row) {
							$RoomsValues[$key]  = $row->Rooms;
						}
						array_multisort($catValues, SORT_ASC, $RoomsValues, ($direction == 'desc' ? SORT_DESC : SORT_ASC), $results);
	//					usort($results, function($a,$b) use ( $ordering, $direction) {
	//						return BFCHelper::orderBy($a, $b, 'Rooms', $direction);
	//					});
						break;
					case 'offer':
						$discountValues = array();
						foreach($results as $key => $row) {
							$discountValues[$key]  = $row->PercentVariation;
						}
						array_multisort($catValues, SORT_ASC, $discountValues, ($direction == 'desc' ? SORT_DESC : SORT_ASC), $results);
	//					usort($results, function($a,$b) use ( $ordering, $direction) {
	////						return BFCHelper::orderBySingleDiscount($a, $b, $direction);
	//						return BFCHelper::orderBy($a, $b, 'PercentVariation', $direction);
	//					});
						break;
					default:
						$randomRes = array();
						foreach($results as $key => $row) {
							$randomRes[$key]  = $key;
						}
						array_multisort($catValues, SORT_ASC, $randomRes, SORT_ASC, $results);
						break;
				}
	//			} else {
	//			usort($results, function($a,$b) use ( $ordering, $direction) {
	//				return $a->IsCatalog - $b->IsCatalog;
	//			});
			}
		}					
		if ($condominiumsResults && !empty($results)) {
			// grouping and ordering
						$results = $this->groupResultsByCondominium($results);
		}

		if ($merchantResults && !empty($results)) {
			// grouping and ordering
			$results = $this->groupResultsByMerchant($results, $ordering, $direction);
		}
		


				
		$this->count =  count($results);
	
		if (! $ignorePagination && isset($start) && (isset($limit) && $limit > 0 ) && !empty($results)) {
			$results = array_slice($results, $start, $limit);
			$params = $this->getState('params');
			$checkin = $params['checkin'];
			$duration = $params['duration'];
			$persons = $params['paxes'];
			$paxages = $params['paxages'];
		}
		if($jsonResult && !empty($results))	{
			$arr = array();

			foreach($results as $result) {
				$val= new StdClass;
				if ($merchantResults) {

					$val->MerchantId = $result->MerchantId;
					$val->XGooglePos = $result->XGooglePos;
					$val->YGooglePos = $result->YGooglePos;
				}
				elseif ($condominiumsResults){
					$val->Resource = new StdClass;
					$val->Resource->ResourceId = $result->CondominiumId;
					$val->Resource->XGooglePos = $result->XGooglePos;
					$val->Resource->YGooglePos = $result->YGooglePos;
				}
				else { 
					$val->Resource = new StdClass;
					$val->Resource->ResourceId = $result->ResourceId;
					$val->Resource->XGooglePos = $result->ResLat;
					$val->Resource->YGooglePos = $result->ResLng;

				}
				$arr[] = $val;
			}
			
			
			return json_encode($arr);
				
		}
		return $results;

		//return $jsonResult ? json_encode($results) : $results;
	}
	
	private function filterResults($results) {
		$params = $this->getState('params');
		$filters = null;
		if (!empty($params['filters'])){
			$filters = $params['filters'];
		}
		if ($filters == null) { //provo a recuperarli dalla sessione...
			$filters = BFCHelper::getFilterSearchParamsSession();
		}


		if ($filters == null) return $results;
		BFCHelper::setFilterSearchParamsSession($filters);
		// zone filtering
		if (!empty($filters['locationzones']) && is_array($results)) {
			$locationzones = $filters['locationzones'];
			$locationzones = explode(",", $locationzones);
			if (is_array($locationzones) || $locationzones != "0" ){
				$results = array_filter($results, function($result) use ($locationzones) {
					return ((is_array($locationzones) && (in_array( $result->MrcZoneId, $locationzones) || in_array( $result->ResZoneId, $locationzones) )) || ($result->MrcZoneId == $locationzones ||$result->ResZoneId == $locationzones  ));
				});
			}
		}


		// merchantgroups filtering
		if (!empty($filters['merchantgroups']) && is_array($results)) {
			$merchantgroups = $filters['merchantgroups'];
			$merchantgroups = explode(",", $merchantgroups);
			if (is_array($merchantgroups)){
			//if ($stars > 0) {
				$results = array_filter($results, function($result) use ($merchantgroups) {
					$hasTags = false;
					$val =  preg_replace('/\s+/', '', $result->MrcTagsIdList);
					$merchantGroupIdList = explode(",",$val);
					if (is_array($merchantGroupIdList)) {
						$arrayresult = array_intersect($merchantgroups, $merchantGroupIdList); 
						$hasTags = (count($arrayresult)>0);

					} else {
						$hasTags = ((is_array($merchantgroups) && in_array( $merchantGroupIdList, $merchantgroups )) || $merchantGroupIdList == $merchantgroups  );
					} 
					
					$val = preg_replace('/\s+/', '', $result->TagsIdList);
					$merchantGroupIdList = explode(",",$val);
					if (is_array($merchantGroupIdList)) {
						$arrayresult = array_intersect($merchantgroups, $merchantGroupIdList); 
						$hasTags = $hasTags || (count($arrayresult)>0);

					} else {
						$hasTags = $hasTags || ((is_array($merchantgroups) && in_array( $merchantGroupIdList, $merchantgroups )) || $merchantGroupIdList == $merchantgroups  );
					}
					return $hasTags;
				});
			}
		}
		
//		// services filtering
//		if (!empty($filters['services']) && is_array($results)) {
//			$services = $filters['services'];
//			$services = explode(",", $services);
//			if (is_array($services)){
//				$results = array_filter($results, function($result) use ($services) {
//					$merchantServiceIdList = explode(",",$result->MrcServiceIdList);
//					$resourceServiceIdList = explode(",",$result->ResServiceIdList);
//					$serviceIdList = array_merge($merchantServiceIdList,$resourceServiceIdList);
//					if (is_array($serviceIdList)) {
//						$arrayresult = array_intersect($services, $serviceIdList);
//						return (count($arrayresult)==count($services));
//	
//					}else{
//						return ((is_array($services) && in_array( $serviceIdList, $services )) || $serviceIdList == $services  );
//					}
//				});
//			}
//		}
		// services filtering resource
		if (!empty($filters['services']) && is_array($results)) {
			$services = $filters['services'];
			$services = explode(",", $services);
			if (is_array($services)){
				$results = array_filter($results, function($result) use ($services) {
					$serviceIdList = explode(",",$result->ResServiceIdList);
					if (is_array($serviceIdList)) {
						$arrayresult = array_intersect($services, $serviceIdList);
						return (count($arrayresult)==count($services));
	
					}else{
						return ((is_array($services) && in_array( $serviceIdList, $services )) || $serviceIdList == $services  );
					}
				});
			}
		}
		// services filtering merchants
		if (!empty($filters['servicesmerchants']) && is_array($results)) {
			$services = $filters['servicesmerchants'];
			$services = explode(",", $services);
			if (is_array($services)){
				$results = array_filter($results, function($result) use ($services) {
					$serviceIdList =  explode(",",$result->MrcServiceIdList);;
					if (is_array($serviceIdList)) {
						$arrayresult = array_intersect($services, $serviceIdList);
						return (count($arrayresult)==count($services));
	
					}else{
						return ((is_array($services) && in_array( $serviceIdList, $services )) || $serviceIdList == $services  );
					}
				});
			}
		}
		
		// mastertypologies filtering
		if (!empty($filters['mastertypologies']) && is_array($results)) {
			$mastertypologies = $filters['mastertypologies'];
			$mastertypologies = explode(",", $mastertypologies);
			if (is_array($mastertypologies)){
			//if ($stars > 0) {
				$results = array_filter($results, function($result) use ($mastertypologies) {
					return ((is_array($mastertypologies) && in_array( $result->MasterTypologyId, $mastertypologies )) || $result->MasterTypologyId == $mastertypologies  );
				});
			}
		}

// rooms filtering checkbox
//		if (!empty($filters['rooms']) && is_array($results)) {
//			$rooms = $filters['rooms'];
//			$rooms = explode(",", $rooms);
//			if (is_array($rooms)){
//			//if ($stars > 0) {
//				$results = array_filter($results, function($result) use ($rooms) {
//					return ((is_array($rooms) && in_array( $result->Rooms, $rooms )) || $result->Rooms == $rooms  );
//				});
//			}
//		}

// rooms filtering radiobutton
		if (!empty($filters['rooms']) && is_array($results)) {
			$rooms = $filters['rooms'];
			if ($rooms > 0) {
				$results = array_filter($results, function($result) use ($rooms) {
					return $result->Rooms >= $rooms;
				});
			}
		}


		// RateplanName filtering
		if (!empty($filters['rateplanname']) && is_array($results)) {
			$rateplanname = $filters['rateplanname'];
			$rateplanname = explode(",", $rateplanname);
			if (is_array($rateplanname)){
			//if ($stars > 0) {
				$results = array_filter($results, function($result) use ($rateplanname) {
					return ((is_array($rateplanname) && in_array( $result->RateplanName, $rateplanname )) || $result->RateplanName == $rateplanname  );
				});
			}
		}
		// stars filtering
		if (!empty($filters['stars']) && is_array($results)) {
			$stars = $filters['stars'];
			$stars = explode(",", $stars);
			if (is_array($stars) || $stars != "0" ){
				$results = array_filter($results, function($result) use ($stars) {
					return ((is_array($stars) && (in_array( $result->MrcRating, $stars) || $result->MrcRating == $stars || in_array( $result->ResRating, $stars)) || $result->ResRating == $stars));
				});
			}
		}
		// bookingTypes filtering
		if (!empty($filters['bookingtypes']) && is_array($results)) {
			$bookingTypes = $filters['bookingtypes'];
			$bookingTypes = explode(",", $bookingTypes);
			if (is_array($bookingTypes) || $bookingTypes != "0" ){
				$results = array_filter($results, function($result) use ($bookingTypes) {
//					return ((is_array($bookingTypes) && (in_array( $result->BookingType, $bookingTypes)) || $result->BookingType == $bookingTypes));
					return ((is_array($bookingTypes) && (in_array( $result->IsBookable, $bookingTypes)) || $result->IsBookable == $bookingTypes));
				});
			}
		}

		// offers filtering
		if (!empty($filters['offers']) && is_array($results)) {
			$offers = $filters['offers'];
			$offers = explode(",", $offers);
			if (is_array($offers) || $offers != "0" ){
				$results = array_filter($results, function($result) use ($offers) {
//					return ((is_array($offers) && (in_array( $result->TotalPrice>$result->Price, $offers)) || $result->TotalPrice>$result->Price == $offers));
					//return ((is_array($offers) && (in_array( !empty($result->DiscountId), $offers)) || !empty($result->DiscountId) == $offers));
					return ((is_array($offers) && (in_array( $result->IsOffer, $offers)) || $result->IsOffer == $offers) && $result->TotalPrice>$result->Price);

				});
			}
		}

		// price min filtering
		if (!empty($filters['pricemin']) && is_array($results)) {
			$pricemin = $filters['pricemin'];
			if ($pricemin > 0) {
				$results = array_filter($results, function($result) use ($pricemin) {
					return $result->Price >= $pricemin;
				});
			}
		}
		// price min filtering
		if (!empty($filters['pricemax']) && is_array($results)) {
			$pricemax = $filters['pricemax'];
			if ($pricemax > 0) {
				$results = array_filter($results, function($result) use ($pricemax) {
					return $result->Price <= $pricemax;
				});
			}
		}

		return $results;
	}
	
	private function groupResultsByMerchant($results, $ordering, $direction) {

		$catValues = array();
		foreach($results as $key => $row) {
			$catValues[$key]  = $row->IsCatalog;
		}

		if (isset($ordering) && is_array($results)) {
			// 'stay' ordering should take place before grouping
			if (strtolower($ordering) == 'stay') {
					$pricesValues = array();
					foreach($results as $key => $row) {
						$pricesValues[$key]  = $row->TotalPrice;
					}
					array_multisort($catValues, SORT_ASC, $pricesValues, ($direction == 'desc' ? SORT_DESC : SORT_ASC), $results);
//				usort($results, function($a,$b) use ( $ordering, $direction) {
//					return BFCHelper::orderBy($a, $b, 'TotalPrice', $direction);
//				});
			}
			if (strtolower($ordering) == 'offer') {
					$pricesValues = array();
					foreach($results as $key => $row) {
						$pricesValues[$key]  = $row->PercentVariation;
					}
					array_multisort($catValues, SORT_ASC, $pricesValues, ($direction == 'desc' ? SORT_DESC : SORT_ASC), $results);
//				usort($results, function($a,$b) use ( $ordering, $direction) {
//					return BFCHelper::orderBy($a, $b, 'PercentVariation', $direction);
////					return BFCHelper::orderBySingleDiscount($a, $b, $direction);
//				});
			}
		}
		
		$arr = array();
		foreach($results as $result) {
			if (!array_key_exists($result->MerchantId, $arr)) {
				$merchant = new stdClass();
				$merchant->MerchantId = $result->MerchantId;
				$merchant->MrcCategoryName = $result->DefaultLangMrcCategoryName;
				$merchant->Name = $result->MrcName;
				$merchant->XGooglePos = $result->MrcLat;
				$merchant->YGooglePos = $result->MrcLng;
				$merchant->MerchantTypeId = $result->MerchantTypeId;
				$merchant->Rating = $result->MrcRating;
				$merchant->RatingsContext = $result->RatingsContext;
				$merchant->RatingsType = $result->RatingsType;
				$merchant->PaymentType = $result->PaymentType;
				$merchant->reviewValue = $result->MrcAVG;
				$merchant->reviewCount = $result->MrcAVGCount;
				$merchant->LogoUrl = $result->LogoUrl;
				$merchant->Weight = $result->MrcWeight;
				$merchant->MrcTagsIdList = $result->MrcTagsIdList;
				$merchant->ImageUrl = $result->MrcImageUrl;
				$merchant->Resources = array();
				$merchant->Resources[] = $result;
				$arr[$merchant->MerchantId] = $merchant;
			}
			else {
				$merchant = $arr[$result->MerchantId];
					$merchant->Resources[] = $result;
			}
		}

		if (!empty($ordering)) {
			switch (strtolower($ordering)) {
				case 'stay':
					usort($arr, function($a,$b) use ( $ordering, $direction) {
						return BFCHelper::orderByStay($a, $b, $direction);
					});
					break;
				case 'rating':
					usort($arr, function($a,$b) use ( $ordering, $direction) {
						return BFCHelper::orderBy($a, $b, 'Rating', $direction);
					});
					break;
				case 'reviewvalue':
					usort($arr, function($a,$b) use ( $ordering, $direction) {
						return BFCHelper::orderBy($a, $b, 'reviewValue', $direction);
					});
					break;
			   case 'offer':
					usort($arr, function($a,$b) use ( $ordering, $direction) {
						return BFCHelper::orderBy($a->Resources[0], $b->Resources[0], 'PercentVariation', $direction);
//						return BFCHelper::orderByDiscount($a, $b, $direction);
					});
					break;
				default:
					$mrcCatalog = array();
					$mrcIndexes = array();
					
					foreach($arr as $key => $row) {
						/*
						usort($row->Resources, function($a,$b) use ( $ordering, $direction) {
							return BFCHelper::orderBy($a, $b, 'ResWeight', 'asc');
						});
						*/
						$totalResources = count($row->Resources);
						$catRes = count(array_filter($row->Resources, function($rs) {
							return $rs->IsCatalog;
						}));
						$mrcCatalog[$key] = ($catRes * 100) / $totalResources;
						$mrcIndexes[$key] = $key;
					}
					
					array_multisort($mrcCatalog, SORT_ASC, $mrcIndexes, SORT_ASC, $arr);
//				usort($arr, function($a,$b) use ( $ordering, $direction) {
//					return BFCHelper::orderBy($a, $b, 'PaymentType', 'desc');
//				});
			}
		}else{
			$mrcCatalog = array();
			$mrcWeights = array();
			
			foreach($arr as $key => $row) {
				$mrcWeights[$key]  = $row->Weight;
				$catValues = array();
				$resWeights = array();
				foreach($row->Resources as $k => $rs) {
					$catValues[$k]  = $rs->IsCatalog;
					$resWeights[$k]  = $rs->ResWeight;
				}
				array_multisort($catValues, SORT_ASC, $resWeights, SORT_ASC, $row->Resources);
				/*
				usort($row->Resources, function($a,$b) use ( $ordering, $direction) {
					return BFCHelper::orderBy($a, $b, 'ResWeight', 'asc');
				});
				*/
				$totalResources = count($row->Resources);
				$catRes = count(array_filter($row->Resources, function($rs) {
					return $rs->IsCatalog;
				}));
				$mrcCatalog[$key] = ($catRes * 100) / $totalResources;
			}
			
//			array_multisort($mrcCatalog, SORT_ASC, $arr);
//			usort($arr, function($a,$b) use ( $ordering, $direction) {
//				return BFCHelper::orderBy($a, $b, 'Weight', 'asc');
//			});
//			usort($arr->Resources[], function($a,$b) use ( $ordering, $direction) {
//				return BFCHelper::orderBy($a, $b, 'ResWeight', 'asc');
//			});
		}
		
		return $arr;
	}

		private function groupResultsByCondominium($results) {
		
		$arr = array();
		foreach($results as $result) {
			if(!empty($result->CondominiumId)){
				if (!array_key_exists($result->CondominiumId, $arr)) {
					$condominium = new stdClass();
					$condominium->CondominiumId = $result->CondominiumId;
					$condominium->MrcCategoryName = $result->DefaultLangMrcCategoryName;
					$condominium->XGooglePos = $result->ResLat;
					$condominium->YGooglePos = $result->ResLng;
					$condominium->Name  = $result->ResName;
					$condominium->MerchantId = $result->MerchantId;
					$condominium->MerchantName = $result->MrcName;
					$condominium->Resources = array();
					$condominium->Resources[] = $result;
					$arr[$result->CondominiumId] = $condominium;
				}
				else {
					$condominium = $arr[$result->CondominiumId];
					$condominium->Resources[] = $result;
				}
			}
		}

		return $arr;
	}

	public function getSearchResultsByMerchant($merchantId) {
		$params = $this->getState('params');
		
		$options = array(
				'path' => $this->urlSearchByMerchant,
				'data' => array(
					'merchantId' => $merchantId,
					'$select' => 'ResourceId,Name,Rating',
					'$format' => 'json'
				)
		);

		/*if (isset($start) && $start > 0) {
			$options['data']['$skip'] = $start;
		}

		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}*/

		$this->applyDefaultFilter($options);

		$url = $this->helper->getQuery($options);

		$results = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$results = $res->d->results ?: $res->d;
		}

		return $results;
	}
	
	public function getStay($resourceId) {
		$params = $this->getState('params');

		$ci = $params['checkin'];
		$du = $params['duration'];
		//$px = array_fill(0,(int)$params['paxes'],BFCHelper::$defaultAdultsAge);
		//$ex = $params['extras'];
		$pt = $params['pricetype'];
		$paxages = $params['paxages'];
		
		if ($ci == null || $du == null || $paxages == null) { 
			return null;
		}
				
		$options = array(
				'path' => $this->urlStay,
				'data' => array(
					'resourceId' => $resourceId,
					'checkin' => '\'' . $ci->format('Ymd') . '\'',
					'duration' => $du,
					//'paxages' => '\'' . implode('|',$px) . '\'',
					'paxages' => '\'' . implode('|',$paxages) . '\'',
						//'extras' => '\'' . $ex . '\'',
					'priceType' => '\'' . $pt . '\'',
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$stay = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$stay = $res->d->GetStay;
		}

		return $stay;
	}
	
	public function GetAllRatePlansStay($resourceId) {
		$params = $this->getState('params');

		$ci = $params['checkin'];
		$du = $params['duration'];
		//$px = array_fill(0,(int)$params['paxes'],BFCHelper::$defaultAdultsAge);
		//$ex = $params['extras'];
		$paxages = $params['paxages'];
		
		if ($ci == null || $du == null || $paxages == null) { 
			return null;
		}
				
		$options = array(
				'path' => $this->urlAllRatePlansStay,
				'data' => array(
					'resourceId' => $resourceId,
					'checkin' => '\'' . $ci->format('Ymd') . '\'',
					'duration' => $du,
					//'paxages' => '\'' . implode('|',$px) . '\'',
					'paxages' => '\'' . implode('|',$paxages) . '\'',
					//'extras' => '\'' . $ex . '\'',
					'$format' => 'json'
				)
			);
		
		$url = $this->helper->getQuery($options);
		
		$ratePlansstay = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$ratePlansstay = $res->d;
		}

		return $ratePlansstay;
	}

	public function getFiltersKey() {
		$filters ="";
		$params = $this->getState('params');
		if (isset($params) && !empty($params['filters'])){
			$filters = $params['filters'];
		}
		if (empty($filters) || (!is_array($filters) && count($filters) == 0)) return '';
		$filtersKey = '';
		foreach($filters as $key=>$filter) {
			$filtersKey .= $key . ':' . $filter;
		}
		return $filtersKey;
	}
	
	public function getTotal()
	{
		if ($this->count !== null)
			return $this->count;
			
		$params = $this->getState('params');
		$merchantResults = $params['merchantResults'];
		
		$searchid = $params['searchid'];
		
		$sessionkey = 'search.' . $searchid . '.count';
		$session = JFactory::getSession();
		$cachedresults = $session->get($sessionkey, '');
		
		if (isset($cachedresults) && $cachedresults != null) {
			/*
			// post filtering results
			$filtersKey = $this->getFiltersKey();
			$results = $this->filterResults($results);
			
			$sessionkey = 'search.' . $searchid . '.' . $filtersKey . '.results';
			
			$filteredCachedresults = $session->get($sessionkey, '', 'com_bookingforconnector');
			
			if (isset($filteredCachedresults) && is_array($filteredCachedresults)) {
				return count($filteredCachedresults);
			}
			*/
			return $cachedresults;
		}
		//$merchantResults = $params['merchantResults'];

		$options = array(
				'path' => $merchantResults == true ? $this->urlSearchAllCountMerchant : $this->urlSearchAllCount,
				'data' => array(
					'$format' => 'json'
				)
			);
		
		$this->applyDefaultFilter($options);
				
		$url = $this->helper->getQuery($options);
		
		$c = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$c = $merchantResults == true ? (int)$res->d->SearchAllCountMerchant : (int)$res->d->SearchAllCount;
		}

		$session->set($sessionkey, $c);
		return $c;
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
		$session = JFactory::getSession();
		if(empty($language)){
				$language = JFactory::getLanguage()->getTag();
		}
		$typologies = $session->get('getMasterTypologies'.$language, null , 'com_bookingforconnector');
//		if (!$session->has('getMerchantCategories','com_bookingforconnector')) {
		if ($typologies==null) {
			$typologies = $this->getMasterTypologiesFromService($onlyEnabled, $language);
			$session->set('getMasterTypologies'.$language, $typologies, 'com_bookingforconnector');
		}
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
				'searchid' =>  "booking", //BFCHelper::getVar('searchid'),
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
					'searchid' =>  "booking", //BFCHelper::getVar('searchid'),
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
					'newsearch' => BFCHelper::getVar('newsearch', "0")
				);
			} catch (Exception $e) {
				
				$condominiumsResults = BFCHelper::getVar('condominiumsResults');
				$merchantResults = (!empty(BFCHelper::getInt('merchantCategoryId')) && in_array(BFCHelper::getInt('merchantCategoryId'), BFCHelper::getCategoryMerchantResults(BFCHelper::getVar('cultureCode')), false));
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
					'searchid' =>  "booking", //BFCHelper::getVar('searchid'),
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
					'newsearch' => 1

				);
			}


			$this->setState('params',$currParam);
		}

//		$filter_order = BFCHelper::getCmd('filter_order','stay');
//		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir','asc');
		$filter_order = BFCHelper::getCmd('filter_order');
		$filter_order_Dir = BFCHelper::getCmd('filter_order_Dir');
//		return parent::populateState($filter_order, $filter_order_Dir);
		parent::populateState($filter_order, $filter_order_Dir);
	}
	
	public function getItems($ignorePagination = false, $jsonResult = false)
	{
		// Get a storage key.
		//$store = $this->getStoreId();

		// Try to load the data from internal storage.
		/*if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}*/

//		$items = $this->getSearchResults(
//			$this->getState('list.start'), 
//			$this->getState('list.limit'), 
//			$this->getState('list.ordering', 'stay'), 
//			$this->getState('list.direction', 'asc'),
//			$ignorePagination,
//			$jsonResult
//		);
		$items = $this->getSearchResults(
			$this->getState('list.start'), 
			$this->getState('list.limit'), 
			$this->getState('list.ordering'), 
			$this->getState('list.direction'),
			$ignorePagination,
			$jsonResult
		);

		// Add the items to the internal cache.
		//$this->cache[$store] = $items;

		//return $this->cache[$store];
		return $items;
	}
	function maxValueInArray($array, $keyToSearch)
	{
		$currentMax = NULL;
		foreach($array as $arr)
		{
			foreach($arr as $key => $value)
			{
				if ($key == $keyToSearch && ($value >= $currentMax))
				{
					$currentMax = $value;
				}
			}
		}

		return $currentMax;
	}
	
	public  function GetResourcesCalculateByIds($listsId,$language='') {

		$params = $this->getState('params');

		$ci = $params['checkin'];
		$du = $params['duration'];
		//$px = array_fill(0,(int)$params['paxes'],BFCHelper::$defaultAdultsAge);
		//$ex = $params['extras'];
		$paxages = $params['paxages'];
		
		if ($ci == null || $du == null || $paxages == null) { 
			return null;
		}
				
		$options = array(
				'path' => $this->urlGetCalculateResourcesByIds,
				'data' => array(
					/*'$skip' => $start,
					'$top' => $limit,*/
					'ids' => '\'' .$listsId. '\'',
					'checkin' => '\'' . $ci->format('Ymd') . '\'',
					'duration' => $du,
					//'paxages' => '\'' . implode('|',$px) . '\'',
					'paxages' => '\'' . implode('|',$paxages) . '\'',
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

}
