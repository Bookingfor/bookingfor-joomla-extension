<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

require_once $pathbase . 'defines.php';
require_once $pathbase . 'helpers/BFCHelper.php';

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$document		= JFactory::getDocument();
$language 	= JFactory::getLanguage()->getTag();
$mainframe = JFactory::getApplication();
$db   = JFactory::getDBO();

//Load language translation from component
$lang = JFactory::getLanguage();
$lang->load('com_bookingforconnector', $pathbase, 'en-EN', true);
$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);

$showcurrencyswitcher = $params->get('showcurrencyswitcher');
$showcart = $params->get('showcart');

$defaultCurrency = bfi_get_defaultCurrency();
$currentCurrency = bfi_get_currentCurrency();
$currencyExchanges = bfi_get_currencyExchanges();

if($showcurrencyswitcher && !$showcart && (empty($currencyExchanges) || count($currencyExchanges)<2)){
		return; //no more currency than default
}

bfi_load_scripts();

require JModuleHelper::getLayoutPath('mod_bookingforcurrencyswitcher', $params->get('layout', 'default'));
