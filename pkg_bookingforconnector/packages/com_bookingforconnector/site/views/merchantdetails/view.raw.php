<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * HTML View class for the HelloWorld Component
 */
class BookingForConnectorViewMerchantDetails extends BFCView
{
//	protected $state = null;
//	protected $item = null;
//	protected $items = null;
//	protected $pagination = null;
//	protected $document = null;
//	protected $language = null;
			
	// Overwriting JView display method
	function display($tpl = NULL, $preparecontent = false) 
	{
		// Initialise variables
		$state		= $this->get('State');
		$item		= $this->get('Item');
		
		if ($item->HasResources) {
			if (BFCHelper::getString('layout') == 'resourcesajax') {
				$items = $this->get('ItemsResourcesAjax');
			}
		}
		if ($item->HasResources) {
			if (BFCHelper::getString('layout') == 'resources') {
				$items = $this->get('Items');
				$pagination	= $this->get('Pagination');
			}
		}
		
		if (BFCHelper::getString('layout') == 'offers') {
			$items = $this->get('ItemsOffer');
			$pagination	= $this->get('Pagination');
		}
		
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();		
		$params 	= $state->params;
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		$this->setBreadcrumb($item, BFCHelper::getString('layout'));
		
		$this->assignRef('params', $params);
		$this->assignRef('item', $item);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('document', $document);
		$this->assignRef('language', $language);
		
		// Display the view
		parent::display($tpl, true);
	}
	
	function setBreadcrumb($merchant, $layout = '') {
		$mainframe = JFactory::getApplication();
		$pathway   = $mainframe->getPathway();
		
		$pathway->addItem(
			$merchant->Name,
			JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name))
		);
		
		if ($layout != '') {
			$pathway->addItem(
				JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_' . strtoupper($layout) ),
				JRoute::_('index.php?layout=' . $layout . '&option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name))
			);
		}
	}
}
