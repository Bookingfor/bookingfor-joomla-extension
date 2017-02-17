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

$currentComponent = BFCHelper::getCmd( 'option' );
$currentView = BFCHelper::getCmd( 'view' );
$currentlayout = BFCHelper::getString('layout','default');

if ($currentComponent != 'com_bookingforconnector') return;

if($currentView!=="search" && $currentView!=="searchonsell" && $currentView!=="condominiums"  && $currentView!=="onsellunits" &&  $currentView!=="resources" ) return ;


$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

$document		= JFactory::getDocument();
$language 	= $document->getLanguage();
$mainframe = JFactory::getApplication();
$db   = JFactory::getDBO();

$config = JComponentHelper::getParams('com_bookingforconnector');
$XGooglePosDef = htmlspecialchars($config->get('posx', 0));;
$YGooglePosDef = htmlspecialchars($config->get('posy', 0));
$startzoom = $config->get('startzoom',14);
$googlemapsapykey = $config->get('googlemapskey','');


//Load language translation from component
$lang = JFactory::getLanguage();
$lang->load('com_bookingforconnector', $pathbase, 'en-EN', true);
$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);

//-----------------------------------------------------------------------------------------
//	implementazione css 
//	per uno style personalizzato usare il seguente file css nella cartella css del template
//	"mod_bookingforsearchbymerchanttype.css"
//-----------------------------------------------------------------------------------------

JHTML::stylesheet('modules/'.$module->module.'/assets/style.css');

if (is_file(JPATH_SITE.'/templates/'.$mainframe->getTemplate().'/css/'.$module->module.".css")){
	JHTML::stylesheet('templates/'.$mainframe->getTemplate().'/css/'.$module->module.".css");
}

require JModuleHelper::getLayoutPath('mod_bookingformaps', $params->get('layout', 'default'));
