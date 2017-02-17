<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

 // Check for PHP4
if(defined('PHP_VERSION')) {
	$version = PHP_VERSION;
} elseif(function_exists('phpversion')) {
	$version = phpversion();
} else {
	// No version info. I'll lie and hope for the best.
	$version = '5.0.0';
}

// Old PHP version detected. EJECT! EJECT! EJECT!
if(!version_compare($version, '5.3.0', '>='))
{
	return JFactory::getApplication()->enqueueMessage('PHP versions 4.x, 5.0, 5.1 and 5.2 are no longer supported by BookingFor.<br/><br/>The version of PHP used on your site is obsolete and contains known security vulnerabilities. Moreover, it is missing features required by BookingForto work properly or at all. Please ask your host to upgrade your server to the latest PHP 5.3 release. Thank you!', 'warning');
}	

// import joomla controller library
//jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by HelloWorld
$controller = JControllerLegacy::getInstance('BookingForConnector');
 
// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));
 
// Redirect if set by the controller
$controller->redirect();
