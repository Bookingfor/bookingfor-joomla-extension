<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


/**
 * HTML View class for the HelloWorld Component
 */
class BookingForConnectorViewMerchantDetails extends BFCView
{
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
		
		$resource = null;
		$merchant = $this->item;
		$cartType = 1; //$merchant->CartType;
		$currencyclass = bfi_get_currentCurrency();
		$resourceId = 0;
		$condominiumId = 0;

		if(!empty(BFCHelper::getVar('refreshcalc',''))){
			bfi_setSessionFromSubmittedData();
		}
		$merchants = array();
		$merchants[] = $merchant->MerchantId;
		$criteoConfig = null;
		if(COM_BOOKINGFORCONNECTOR_CRITEOENABLED){
			$criteoConfig = BFCHelper::getCriteoConfiguration(2, $merchants);
		}
	
		
//		$_SESSION['search.params']['resourceId'] = $resourceId;
		
		$analyticsEnabled = $this->checkAnalytics("") && $config->get('eecenabled', 0) == 1;
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		$this->assignRef('criteoConfig', $criteoConfig);			
		
		$this->setLayout('calc');
		
		parent::display($tpl);
	}
}
