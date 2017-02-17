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
 * BookingForConnectorModelOrders Model
 */
class BookingForConnectorModelUser extends JModelList
{
		
	private $helper = null;
	private $urlGetOrders = null;
	private $urlGetOrdersCount = null;

	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->helper = new wsQueryHelper(COM_BOOKINGFORCONNECTOR_WSURL, COM_BOOKINGFORCONNECTOR_APIKEY);
		$this->urlGetOrders = '/GetOrdersByExternalUser';
		$this->urlGetOrdersCount = '/GetOrdersByExternalUserCount';
	}

	public function getOrders($start, $limit) {		
		$userId = '';
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}else{
			return null;
		}
		$language = JFactory::getLanguage()->getTag();

		$options = array(
				'path' => $this->urlGetOrders,
				'data' => array(
					'userid' => BFCHelper::getQuotedString($userId),
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
		);
		if (isset($start) && $start > 0) {
			$options['data']['skip'] = $start;
		}

		if (isset($limit) && $limit > 0) {
			$options['data']['top'] = $limit;
		}

		
		$url = $this->helper->getQuery($options);
		
		$return = null;
		
		$r = $this->helper->executeQuery($url);
		if (isset($r)) {
			$res = json_decode($r);
			if (!empty($res->d->results)){
				$return = $res->d->results;
			}elseif(!empty($res->d)){

				$return = $res->d;
			}
		}
		return $return;
	}
	
	public function getTotal()
	{
		$userId = '';
		$user = JFactory::getUser();
		if ($user->id != 0) {
			$userId=$user->id."|". $user->username . "|" . $_SERVER["SERVER_NAME"];
		}else{
			return 0;
		}
		$language = JFactory::getLanguage()->getTag();
		$options = array(
				'path' => $this->urlGetOrdersCount,
				'data' => array(
					'userid' => BFCHelper::getQuotedString($userId),
					'cultureCode' => BFCHelper::getQuotedString($language),
					'$format' => 'json'
				)
			);
		
//		$this->applyDefaultFilter($options);
				
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
		
		return parent::populateState($ordering, $direction);
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

		$items = $this->getOrders(
			$this->getStart(), 
			$this->getState('list.limit')
//			, 
//			$this->getState('list.ordering', 'CreationDate'), 
//			$this->getState('list.direction', 'desc')
		);

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}
}
