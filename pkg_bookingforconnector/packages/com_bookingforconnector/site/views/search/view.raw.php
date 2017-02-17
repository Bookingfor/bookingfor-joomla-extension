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
class BookingForConnectorViewSearch extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $items = null;
	protected $pagination = null;
	protected $language = null;
	protected $typologies = null;

	// Overwriting JView display method
	function display($tpl = NULL, $preparecontent = false) 
	{
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$config = JComponentHelper::getParams('com_bookingforconnector');
		$app = JFactory::getApplication();

		// Initialise variables
		$state		= $this->get('State');
		$items		= $this->get('Items');
		$pagination	= $this->get('Pagination');
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');
		$params = $state->params;

		$typologies = $this->getModel()->getMasterTypologies(true,$language);

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			BFCHelper::raiseWarning(500, implode("\n", $errors));
			return false;
		}
		
		$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
		$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
		$pagination->setAdditionalUrlParam("searchid", $params['searchid']);
		
		$filters = BFCHelper::getFilterSearchParamsSession();
		if(!empty($filters)){
			foreach($filters as $key => $value) {
				$pagination->setAdditionalUrlParam("filters[".$key."]", $value);
			}
		}

		
		$hidesort = false;
		$this->assignRef('state', $state);
		$this->assignRef('params', $params);
		$this->assignRef('items', $items);
		$this->assignRef('pagination', $pagination);
		$this->assignRef('language', $language);
		$this->assignRef('typologies', $typologies);
		$this->assignRef('config', $config);
		$this->assignRef('hidesort', $hidesort);
		$maxItemsView = 3;
		$this->assignRef('maxItemsView', $maxItemsView);
		$analyticsEnabled = $this->checkAnalytics("");
		$this->assignRef('analyticsEnabled', $analyticsEnabled);
		
		// Display the view
		parent::display($tpl, true);
	}
}
