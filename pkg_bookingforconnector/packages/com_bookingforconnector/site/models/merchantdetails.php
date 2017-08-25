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
class BookingForConnectorModelMerchantDetails extends JModelList
{
	private $urlMerchant = null;
	private $urlMerchantResources = null;
	private $urlMerchantResourcesCount = null;
	private $helper = null;
	private $urlMerchantOnSellUnits = null;
	private $urlMerchantOnSellUnit = null;
	private $urlMerchantOnSellUnitsCount = null;
	private $urlMerchantRating = null;
	private $urlMerchantRatingCount = null;
	private $urlMerchantRatingAverage = null;
	private $urlMerchantMerchantGroups = null;
	private $urlMerchantCounter = null;
	private $urlMerchantOffers = null;
	private $urlMerchantOffer = null;
	private $urlMerchantOffersCount = null;
	private $urlMerchantPackages = null;
	private $urlMerchantPackagesCount = null;
	private $urlMerchantPackage = null;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlMerchant = '/GetMerchantsById';
		$this->urlMerchantResources = '/GetMerchantResources';
		$this->urlMerchantResourcesCount = '/GetMerchantResourcesCount';
		
//		$this->urlMerchantOffers = '/Packages';
//		$this->urlMerchantOffersCount = '/Packages/$count';
//		$this->urlMerchantOffer = '/Packages(%d)';

		$this->urlMerchantOnSellUnits = '/ResourceonsellView';
		$this->urlMerchantOnSellUnitsCount = '/ResourceonsellView/$count';
		$this->urlMerchantOnSellUnit = '/ResourceonsellView(%d)';
		$this->urlMerchantRatingAverage = '/GetMerchantAverage';
		$this->urlMerchantMerchantGroups = '/GetMerchantMerchantGroups';
		$this->urlMerchantCounter = '/MerchantCounter';

//		$this->urlMerchantRating = '/RatingsView';
//		$this->urlMerchantRatingCount = '/RatingsView/$count';
		$this->urlMerchantRating = '/GetReviews';
		$this->urlMerchantRatingCount = '/GetReviewsCount';

		$this->urlMerchantOffers = '/GetVariationPlans';
		$this->urlMerchantOffersCount = '/GetVariationPlansCount';
		$this->urlMerchantOffer = '/GetVariationPlanById';
		
		$this->urlMerchantPackages = '/GetPackages';
		$this->urlMerchantPackagesCount = '/GetPackagesCount';
		$this->urlMerchantPackage = '/GetPackageById';


	}

	


	protected function populateState($ordering = NULL, $direction = NULL) {
//		$session = JFactory::getSession();
		$searchseed = BFCHelper::getSession('searchseed', rand(), 'com_bookingforconnector');
//		if (!$session->has('searchseed','com_bookingforconnector')) {
		if ($searchseed==null) {
			BFCHelper::setSession('searchseed', $searchseed, 'com_bookingforconnector');
		}
		if (!isset($defaultDate)){
			$defaultDate = DateTime::createFromFormat('d/m/Y',BFCHelper::getStartDate());
		}
		$ci = clone BFCHelper::getStayParam('checkin', $defaultDate);
				
		$this->setState('params', array(
			'merchantId' => BFCHelper::getInt('merchantId'),
			'offerId' => BFCHelper::getInt('offerId'),
			'packageid' => BFCHelper::getInt('packageId'),
            'onSellUnitId' => BFCHelper::getInt('onsellunitid'),
			'searchseed' => $searchseed,
			'filters' => BFCHelper::getArray('filters'),
			'checkin' => BFCHelper::getStayParam('checkin', $defaultDate),
			'checkout' => BFCHelper::getStayParam('checkout', $ci->modify(BFCHelper::$defaultDaysSpan)),
			'duration' => BFCHelper::getStayParam('duration'),
			'paxages' => BFCHelper::getStayParam('paxages'),
			'paxes' => count(BFCHelper::getStayParam('paxes'))
		));


		return parent::populateState($ordering, $direction);
	}
	
	public function getMerchantFromServicebyId($merchantId) {
		
		return $this->getMerchantFromService($merchantId);
		
//		$merchanturlMerchantId = $this->urlMerchant;
//
//		if (isset($merchantId) && $merchantId!= ""){
//			$merchanturlMerchantId = sprintf($this->urlMerchant."(%d)", $merchantId);
//		}
//
//		$options = array(
//				'path' => $merchanturlMerchantId,
//				'data' => array(
//					'$filter' => 'Enabled eq true',
//					'$format' => 'json'
////					,'$expand' => 'MerchantType'
//				)
//		);
//		
//		$url = $this->helper->getQuery($options);
//		
//		$merchant = null;
//		
//		$r = $this->helper->executeQuery($url);
//		if (isset($r)) {
//			$res = json_decode($r);
////			$merchant = $res->d->results ?: $res->d;
//			if (!empty($res->d->results)){
//				$merchant = $res->d->results;
//			}elseif(!empty($res->d)){
//				$merchant = $res->d;
//			}
//		}
//		
//		/*switch ($layout) {
//			case 'resources':
//				$merchant->resources = $this->getItems();
//				break;
//			default:
//				break;
//		}*/
//
//		return $merchant;
	}
	
	public function getMerchantFromService($merchantId='') {
		
		if(empty($merchantId)){
			$params = $this->getState('params');
			$merchantId = $params['merchantId'];
		}

//		$layout = $params['layout'];
		
//		$merchanturlMerchantId = $this->urlMerchant;

//		if (isset($merchantId) && $merchantId!= ""){
//			$merchanturlMerchantId = sprintf($this->urlMerchant."(%d)", $merchantId);
//		}
//
		$cultureCode = JFactory::getLanguage()->getTag();

		$sessionkey = 'merchant.' . $merchantId . $cultureCode ;
		$merchant = null;
		$merchant = BFCHelper::getSession($sessionkey); //$_SESSION[$sessionkey];

		if ($merchant == null) {

			$options = array(
					'path' => $this->urlMerchant,
					'data' => array(
						'id' => $merchantId,
						'cultureCode' => BFCHelper::getQuotedString($cultureCode),
						'$format' => 'json'
					)
			);
			
			$url = $this->helper->getQuery($options);

			$r = $this->helper->executeQuery($url);
			if (isset($r)) {
				$res = json_decode($r);
				if (!empty($res->d->GetMerchantsById)){
					$merchant = $res->d->GetMerchantsById;
				}elseif(!empty($res->d)){
					$merchant = $res->d;
				}
			}
			BFCHelper::setSession($sessionkey, $merchant);
		}
		
		return $merchant;
	}
	
//Risorse in vendita
	public function getMerchantOnSellUnitsFromService($start, $limit) {
		$params = $this->getState('params');

		$merchantId = $params['merchantId'];

		$options = array(
				'path' => $this->urlMerchantOnSellUnits,
				'data' => array(
					'$filter' => 'MerchantId eq ' . $merchantId . ' and Enabled eq true',
					'$orderby' => 'Weight',
					/*'$skip' => $start,
					'$top' => $limit,*/
					'$format' => 'json'
				)
		);

		if (isset($start) && $start > 0) {
			$options['data']['$skip'] = $start;
		}

		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}

		$url = $this->helper->getQuery($options);

		$resources = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resources = $res->d->results;
			}elseif(!empty($res->d)){
				$resources = $res->d;
			}
		}

		return $resources;
	}

	public function getMerchantOnSellUnitFromService() {
		$params = $this->getState('params');

		$onSellUnitId = $params['onSellUnitId'];

		$options = array(
				'path' => sprintf($this->urlMerchantOnSellUnit, $onSellUnitId),
				'data' => array(
					'$format' => 'json'
				)
		);

		$url = $this->helper->getQuery($options);

		$onsellunit = null;

		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$onsellunit = $res->d->results ?: $res->d;
		}

		return $onsellunit;
	}

	public function getItemsOnSellUnit()
	{
		return $this->getItems('onsellunits');
	}

	public function getOnSellUnit()
	{
		return $this->getItems('onsellunit');
	}
// fine risorse in vendita <---

	public function getItem()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItem');

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$item = $this->getMerchantFromService();
		
		// Add the items to the internal cache.
		$this->cache[$store] = $item;

		return $this->cache[$store];
	}
		
	public function getMerchantResourcesFromService($start, $limit) {
		$params = $this->getState('params');
		
		$merchantId = $params['merchantId'];
//		$layout = $params['layout'];
		$seed = $params['searchseed'];
		$cultureCode = JFactory::getLanguage()->getTag();
		
		$options = array(
				'path' => $this->urlMerchantResources,
				'data' => array(
					'merchantId' => $merchantId,
					'cultureCode' => BFCHelper::getQuotedString($cultureCode),
					/*'skip' => $start,
					'top' => $limit,*/
					'seed' => $seed,
					'$format' => 'json'
				)
		);
		
		if (isset($start) && $start >= 0) {
			$options['data']['skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}	
				
		$url = $this->helper->getQuery($options);
		
		$resources = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$resources = $res->d->results;
			}elseif(!empty($res->d)){
				$resources = $res->d;
			}
		}

		return $resources;
	}

	public function getMerchantGroupsByMerchantIdFromService($merchantId = null) {
		$params = $this->getState('params');
		
		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}
		
		$options = array(
				'path' => $this->urlMerchantMerchantGroups,
				'data' => array(
					'merchantId' => $merchantId,
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$merchantGroups = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$merchantGroups = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$merchantGroups = $res->d->results;
			}elseif(!empty($res->d)){
				$merchantGroups = $res->d;
			}
		}

		return $merchantGroups;
	}	
	public function getPhoneByMerchantId($merchantId = null,$language='',$number='') {
		$params = $this->getState('params');
		
		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}
		
		$options = array(
				'path' => $this->urlMerchantCounter,
				'data' => array(
					'merchantId' => $merchantId,
					'what' => '\'phone'.$number.'\'',
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
				$res = $resReturn->MerchantCounter;
			}
		}

		return $res;
	}	

	public function GetFaxByMerchantId($merchantId = null,$language='') {
		$params = $this->getState('params');
		
		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}
		
		$options = array(
				'path' => $this->urlMerchantCounter,
				'data' => array(
					'merchantId' => $merchantId,
					'what' => '\'fax\'',
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
				$res = $resReturn->MerchantCounter;
			}
		}

		return $res;
	}	

	public function setCounterByMerchantId($merchantId = null, $what='', $language='') {
		$params = $this->getState('params');
		
		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}
		
		$options = array(
				'path' => $this->urlMerchantCounter,
				'data' => array(
					'merchantId' => $merchantId,
					'what' => '\''.$what.'\'',
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
				$res = $resReturn->MerchantCounter;
			}
		}

		return $res;
	}	

	public function getMerchantRatingAverageFromService($merchantId = null) {
		$params = $this->getState('params');
		
		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}
		
		$options = array(
				'path' => $this->urlMerchantRatingAverage,
				'data' => array(
					'merchantId' => $merchantId,
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$ratings = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			//$resRatings = $res->d->results ?: $res->d;
			$resRatings = null;
			if (!empty($res->d->results)){
				$resRatings = $res->d->results;
			}elseif(!empty($res->d)){
				$resRatings = $res->d;
			}
			if (!empty($resRatings)){
				$ratings = $resRatings->GetMerchantAverage;
			}
		}

		return $ratings;
	}	

	//CHANGED
	public function getMerchantRatingsFromService($start, $limit, $merchantId = null, $language='') {
		$params = $this->getState('params');
		
		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}
		if ($language==null) {
			$language = JFactory::getLanguage()->getTag();
		}
		
		$options = array(
				'path' => $this->urlMerchantRating,
				'data' => array(
//					'$filter' => 'MerchantId eq ' . $merchantId . ' and Enabled eq true',
					/*'$skip' => $start,
					'$top' => $limit,*/
//					'$orderby' => 'CreationDate desc',
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);
		
		if (isset($start) && $start > 0) {
			$options['data']['skip'] = $start;
		}else{
			$options['data']['skip'] = 0;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}		
		if (isset($merchantId) && $merchantId > 0) {
			$options['data']['MerchantId'] = $merchantId;
		}		
		
		$filters = $params['filters'];
		
		// typologyid filtering
		if ($filters != null && $filters['typologyid'] != null) {
			$_SESSION['ratings']['filters']['typologyid'] = $filters['typologyid'];
		}
		if ($filters != null && $filters['typologyid'] != null && $filters['typologyid']!= "0") {
			$_SESSION['ratings']['filters']['typologyid'] = $filters['typologyid'];

//			$options['data']['$filter'] .= ' and TypologyId eq ' .$filters['typologyid'];
			$options['data']['tipologyId'] = $filters['typologyid'];
		}

		$url = $this->helper->getQuery($options);
		
		$ratings = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {

			$res = json_decode($r);
//			$ratings = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$ratings = $res->d->results;
			}elseif(!empty($res->d)){
				$ratings = $res->d;
			}
		}
		return $ratings;
	}

	public function getMerchantRatingsFromService_OLD($start, $limit, $merchantId = null) {
		$params = $this->getState('params');
		
		if ($merchantId==null) {
			$merchantId = $params['merchantId'];
		}
		
		$options = array(
				'path' => $this->urlMerchantRating,
				'data' => array(
					'$filter' => 'MerchantId eq ' . $merchantId . ' and Enabled eq true',
					/*'$skip' => $start,
					'$top' => $limit,*/
					'$orderby' => 'CreationDate desc',
					'$format' => 'json'
				)
		);
		
		if (isset($start) && $start > 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}		
		
		$filters = $params['filters'];
		
		// typologyid filtering
		if ($filters != null && $filters['typologyid'] != null && $filters['typologyid']!= "0") {
			$options['data']['$filter'] .= ' and TypologyId eq ' .$filters['typologyid'];
		}

		$url = $this->helper->getQuery($options);
		
		$ratings = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$ratings = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$ratings = $res->d->results;
			}elseif(!empty($res->d)){
				$ratings = $res->d;
			}
		}
		
//		//raggruppo e conto tutte le recensioni
//		$resultGrouped = array();
//		foreach ($ratings as $val) {
//			if (isset($resultGrouped[$val->TypologyId])){
//				$resultGrouped[$val->TypologyId]++;
//			}else{
//				//$prev_value = array('value' => $val->TypologyId, 'amount' => 0);
//				$resultGrouped[$val->TypologyId] = 1;
//			}
//		}
//		$this->setState('rating.resultGrouped', $resultGrouped);

		//$ratings = $this->filterRatingResults($ratings);
		return $ratings;
	}	

	private function filterRatingResults($results) {
		$params = $this->getState('params');
		$filters = $params['filters'];
		if ($filters == null) return $results;
		
		// typologyid filtering
		if ($filters['typologyid'] != null) {
			$typologyid = (int)$filters['typologyid'];
			if ($typologyid > 0) {
				$results = array_filter($results, function($result) use ($typologyid) {
					return $result->TypologyId == $typologyid;
				});
			}
		}
		
		return $results;
	}


	public function getMerchantPackagesFromService($start, $limit, $language = null) {
		$params = $this->getState('params');
		
		$merchantId = $params['merchantId'];
		
		if ($language==null) {
			$language = JFactory::getLanguage()->getTag();
		}
		
		$options = array(
				'path' => $this->urlMerchantPackages,
				'data' => array(
					'merchantId' => $merchantId,
					'cultureCode' => BFCHelper::getQuotedString($language),
					/*'$filter' => 'MerchantId eq ' . $merchantId . ' and Enabled eq true',
					'$expand' => 'Photos',
					'$skip' => $start,
					'$top' => $limit,*/
					'$format' => 'json'
				)
		);
		/*
		if (isset($start) && $start > 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}		
		*/
		$url = $this->helper->getQuery($options);
		
		$ret = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$ret = $res->d->results ?: $res->d;
			if (!empty($res->d->results)){
				$ret = $res->d->results;
			}elseif(!empty($res->d)){
				$ret = $res->d;
			}
		}

		return $ret;
	}	
	
	public function getMerchantPackageFromService($language = null) {
		$params = $this->getState('params');
		
		$packageId = $params['packageid'];
		
		if ($language==null) {
			$language = JFactory::getLanguage()->getTag();
		}
		
		$options = array(
				'path' => $this->urlMerchantPackage,
				'data' => array(
					'id' => $packageId,
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$ret = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$ret = $res->d->results ?: $res->d;
			if (!empty($res->d->GetPackageById)){
				$ret = $res->d->GetPackageById;
			}elseif(!empty($res->d)){
				$ret = $res->d;
			}
		}

		return $ret;
	}	

	public function getMerchantOffersFromService($start, $limit, $language = null) {
		$params = $this->getState('params');
		
		$merchantId = $params['merchantId'];
		
		if ($language==null) {
			$language = JFactory::getLanguage()->getTag();
		}
		
		$options = array(
				'path' => $this->urlMerchantOffers,
				'data' => array(
					'merchantId' => $merchantId,
					'cultureCode' => BFCHelper::getQuotedString($language),
					/*'$filter' => 'MerchantId eq ' . $merchantId . ' and Enabled eq true',
					'$expand' => 'Photos',
					'$skip' => $start,
					'$top' => $limit,*/
					'$format' => 'json'
				)
		);
		/*
		if (isset($start) && $start > 0) {
			$options['data']['$skip'] = $start;
		}
		
		if (isset($limit) && $limit > 0) {
			$options['data']['$top'] = $limit;
		}		
		*/
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
	
	public function getMerchantOfferFromService($language = null) {
		$params = $this->getState('params');
		
		$offerId = $params['offerId'];
		
		if ($language==null) {
			$language = JFactory::getLanguage()->getTag();
		}
		
		$options = array(
				'path' => $this->urlMerchantOffer,
				'data' => array(
					'id' => $offerId,
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);
		
		$url = $this->helper->getQuery($options);
		
		$offer = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
//			$offer = $res->d->results ?: $res->d;
			if (!empty($res->d->GetVariationPlanById)){
				$offer = $res->d->GetVariationPlanById;
			}elseif(!empty($res->d)){
				$offer = $res->d;
			}
		}

		return $offer;
	}	
		
	public function getItemsRating() 
	{
		return $this->getItems('ratings');
	}
	
	public function getRating() 
	{
		return $this->getItems('ratings');
	}	

	public function getItemsOffer() 
	{
		return $this->getItems('offers');
	}
	
	public function getOffer() 
	{
		return $this->getItems('offer');
	}	
	
	public function getItemsResourcesAjax() 
	{
		return $this->getItems('resourcesajax');
	}

	public function getItemsPackages() 
	{
		return $this->getItems('packages');
	}
	
	public function getPackage() 
	{
		return $this->getItems('package');
	}	

	public function getItems($type = '') 
	{
		// Get a storage key.
		// Get a storage key.
		$store = $this->getStoreId('getItems'.$type);

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}
		$items = null;
		switch($type) {
			case 'packages':
				$items = $this->getMerchantPackagesFromService(
					$this->getStart($type),
					$this->getState('list.limit')
				);
				break;
			case 'package':
				$items = $this->getMerchantPackageFromService();
				break;
			case 'offers':
				$items = $this->getMerchantOffersFromService(
					$this->getStart($type),
					$this->getState('list.limit')
				);
				break;
			case 'offer':
				$items = $this->getMerchantOfferFromService();
				break;
			case 'onsellunits':
				$items = $this->getMerchantOnSellUnitsFromService(
					$this->getStart($type),
					$this->getState('list.limit')
				);
				break;
			case 'onsellunit':
				$items = $this->getMerchantOnSellUnitFromService();
				break;
			case 'ratings':
				$total = $this->getTotal($type);
				if($total>0){
					$items = $this->getMerchantRatingsFromService(
						$this->getStart($type,$total),
						$this->getState('list.limit')
					);
				}
				break;
			case 'resourcesajax':
				$items = $this->getMerchantResourcesFromService(
					0,
					COM_BOOKINGFORCONNECTOR_MAXRESOURCESAJAXMERCHANT
				);
				break;
			case '':
			default:
				$items = $this->getMerchantResourcesFromService(
					$this->getStart(),
					$this->getState('list.limit')
				);
				break;
		}
				
		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
	
	public function getTotal($type = '')
	{
		switch($type) {			
			case 'packages':
				return $this->getTotalPackages();
				break;
			case 'offers':
				return $this->getTotalOffers();
				break;
			case 'onsellunits':
				return $this->getTotalOnSellUnits();
				break;
			case 'ratings':
				return $this->getTotalRatings();
				break;
			case '':
			default:
				return $this->getTotalResources();
		}
	}	

	public function getTotalOnSellUnits()
	{
		$options = array(
				'path' => $this->urlMerchantOnSellUnitsCount,
				'data' => array(
					'$filter' => 'MerchantId eq ' . BFCHelper::getInt('merchantId'). ' and Enabled eq true'
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

	//CHANGED
	public function getTotalRatings()
	{
		$options = array(
				'path' => $this->urlMerchantRatingCount,
				'data' => array(
					'$format' => 'json',
					'MerchantId' =>  BFCHelper::getInt('merchantId') 
			)
		);
		
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$count = (int)$r;
			$res = json_decode($r);
			$count = (int)$res->d->GetReviewsCount;
		}
		
		return $count;
	}
	
	//old
	public function getTotalRatings_OLD()
	{
		$options = array(
				'path' => $this->urlMerchantRatingCount,
				'data' => array(
					'$filter' => 'MerchantId eq ' . BFCHelper::getInt('merchantId') . ' and Enabled eq true'
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
	
	public function getTotalOffers()
	{
		$options = array(
				'path' => $this->urlMerchantOffersCount,
				'data' => array(
					'merchantId' => BFCHelper::getInt('merchantId'),
					'$format' => 'json'
			)
		);
		
		$url = $this->helper->getQuery($options);
		
		$count = 0;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
//			$count = (int)$r;
			$res = json_decode($r);
			$count = (int)$res->d->GetVariationPlansCount;
		}
		
		return $count;
	}
	
	public function getTotalPackages()
	{
		$options = array(
				'path' => $this->urlMerchantPackagesCount,
				'data' => array(
					'merchantId' => BFCHelper::getInt('merchantId'),
					'$format' => 'json'
			)
		);
		
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$count = (int)$r;
			$res = json_decode($r);
			$count = (int)$res->d->GetPackagesCount;
		}
		
		return $count;
	}

	public function getTotalResources()
	{
		$options = array(
				'path' => $this->urlMerchantResourcesCount,
				'data' => array(
					'$format' => 'json',
					'merchantId' => BFCHelper::getInt('merchantId')
			)
		);
		
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = (int)$res->d->GetMerchantResourcesCount;
		}

		return $count;
	}	
	public function getTotalResourcesAjax()
	{
		$options = array(
				'path' => $this->urlMerchantResourcesCount,
				'data' => array(
					'$format' => 'json',
					'merchantId' => BFCHelper::getInt('merchantId')
			)
		);
		
		$url = $this->helper->getQuery($options);
		
		$count = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			$count = (int)$res->d->GetMerchantResourcesCount;
		}

		return $count;
	}
	public function getStart($type = '', $currTotal = -1)
	{
		$store = $this->getStoreId('getstart'.$type);

		// Try to load the data from internal storage.
		if (isset($this->cache[$store]))
		{
			return $this->cache[$store];
		}

		$start = $this->getState('list.start');
		$limit = $this->getState('list.limit');
		$total = $currTotal;
		if($total == -1){
			$total = $this->getTotal($type);
		}
		if ($start > $total - $limit)
		{
			$start = max(0, (int) (ceil($total / $limit) - 1) * $limit);
		}

		// Add the total to the internal cache.
		$this->cache[$store] = $start;

		return $this->cache[$store];
	}
	function getPaginationRatings()
	{	
		return $this->getPagination('ratings');
	}
	function getPaginationOnSellUnits()
	{	
		return $this->getPagination('onsellunits');
	}
	function getPaginationOffers()
	{	
		return $this->getPagination('offers');
	}
	function getPaginationPackages()
	{	
		return $this->getPagination('packages');
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
