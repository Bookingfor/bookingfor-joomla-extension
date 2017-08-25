<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class BookingForConnectorViewTag extends BFCView
{
	protected $state = null;
	protected $item = null;
	protected $params = null;
	protected $language = null;
	protected $actionmode = null;

		
	// Overwriting JView display method
	function display($tpl = NULL)
	{

		// Initialise variables
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$params 	= $state->params;
		$sortColumn 	= $state->get('list.ordering');
		$sortDirection 	= $state->get('list.direction');

		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		$app = JFactory::getApplication();
		$sitename = $app->get('sitename');
		
		$category = 0;
		$showGrouped = 0;

		if(!empty($item)) {
			$category = $item->SelectionCategory;
			$showGrouped  = BFCHelper::getVar('show_grouped',0);
			if ($category  == 1) {
				$items = $this->get('ItemsMerchants');
				$pagination	= $this->get('Pagination');
			}
			if ($category == 2) {
				$items = $this->get('ItemsOnSellUnit');
				$pagination	= $this->get('Pagination');
			}
			if ($category == 4) {
				$items = $this->get('ItemsResources');
				$pagination	= $this->get('Pagination');
				$showGrouped  = $params['show_grouped'];
			}

			$this->items = $items;
			$this->pagination = $pagination;
			
			$pagination->setAdditionalUrlParam("filter_order", $sortColumn);
			$pagination->setAdditionalUrlParam("filter_order_Dir", $sortDirection);
			$pagination->setAdditionalUrlParam("newsearch",0);
		}

		$this->state = $state;
		$this->language = $language;
		$this->item = $item;
		$this->category = $category;
		$this->showGrouped = $showGrouped ;
		
		// Display the view
		parent::display($tpl);
	}
		
}
