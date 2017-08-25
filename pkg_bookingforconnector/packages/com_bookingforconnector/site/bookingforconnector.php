<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

include_once JPATH_COMPONENT . '/defines.php';
require_once JPATH_COMPONENT . '/views/BFCView.php';
require_once JPATH_COMPONENT . '/helpers/wsQueryHelper.php';
require_once JPATH_COMPONENT . '/helpers/BFCHelper.php';

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('BookingForConnector');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
