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
class BookingForConnectorViewResource extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $params = null;
	protected $language = null;
	protected $gotCalculator = null;
	protected $stay = null;

	// Overwriting JView display method
	function display($tpl = null, $preparecontent = false) 
	{
		// Initialise variables
		$state		= $this->get('State');
		$item		= $this->get('Item');

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$params = $state->params;
		
		$stay = $this->get('Stay');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		$this->setBreadcrumb($item, 'resources', $language);
		
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('item', $item);
		$this->assignRef('stay', $stay);
		$this->assignRef('document', $document);
		$this->assignRef('language', $language);
		
		// Display the view
		//parent::display($tpl);
		parent::display($tpl, true);
	}
	
	function setBreadcrumb($resource, $layout = '') {
		$mainframe = JFactory::getApplication();
		$pathway   = $mainframe->getPathway();
		
		$pathway->addItem(
			$resource->Merchant->Name,
			JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $resource->Merchant->MerchantId . ':' . BFCHelper::getSlug($resource->Merchant->Name))
		);
		
		$pathway->addItem(
			JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_' . strtoupper($layout) ),
			JRoute::_('index.php?option=com_bookingforconnector&view=merchantdetails&layout=' . $layout . '&merchantId=' . $resource->Merchant->MerchantId . ':' . BFCHelper::getSlug($resource->Merchant->Name))
		);
				
//		$resourceName = BFCHelper::getLanguage($resource->Name, $this->Language);
		$resourceName = BFCHelper::getLanguage($resource->Name, $this->language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags')); 
		
		$pathway->addItem(
			$resourceName,
			JRoute::_('index.php?option=com_bookingforconnector&view=resource&resourceId=' . $resource->ResourceId . ':' . BFCHelper::getSlug($resourceName))
		);
	}
}
