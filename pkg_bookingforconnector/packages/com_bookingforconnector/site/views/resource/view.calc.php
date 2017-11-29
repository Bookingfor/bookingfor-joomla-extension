<?php

/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * HTML View class for the Bookingforconnector Component
 */
class BookingForConnectorViewResource extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;
	protected $params = null;

	// Overwriting JView display method
	function display($tpl = null)
	{
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$params 	= $state->params;

		$this->state = $state;
		$this->params = $params;
		$this->item = $item;
		$this->config = $config;
		$this->language = $language;
		
		$resource = $this->item;
		$merchant = $resource->Merchant;
		$resourceId = $resource->ResourceId;
		
		$items=null;
		$pagination=null;




		if(!empty(BFCHelper::getVar('refreshcalc',''))){
			bfi_setSessionFromSubmittedData();
		}
		$merchants = array();
		$merchants[] = $resource->MerchantId;
		$criteoConfig = null;
		if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED){
			$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
		}
	
		
		$currParam = BFCHelper::getSearchParamsSession();
		$currParam['resourceId'] = $resourceId;
		BFCHelper::setSearchParamsSession($currParam);
		
		$analyticsEnabled = $this->checkAnalytics("") && COM_BOOKINGFORCONNECTOR_EECENABLED == 1;
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		$this->assignRef('criteoConfig', $criteoConfig);			
		
		$this->setLayout('calc');
		
		parent::display($tpl);
	}
}
