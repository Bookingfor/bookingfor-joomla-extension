<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');


/**
 * Build the route for the com_bookingforconnector component
 *
 * @param	array	An array of URL arguments
 * @return	array	The URL arguments to use to assemble the subsequent URL.
 * @since	1.5
 */
function BookingForConnectorBuildRoute(&$query)
{
	$segments = array();
	$view = '';
	
	if(isset($query['view']))
	{
		/* la corretta view da utilizzare nelle varie lingue e' settata nel file di traduzioni  */
		
		
		$v = JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_' . strtoupper($query['view']));
		
		if ($v!='' && $v!='COM_BOOKINGFORCONNECTOR_VIEWS_' . strtoupper($query['view'])) {
			$segments[] = strtolower($v);
		}else{ 
			$segments[] = strtolower($query['view']);
		}
		$view = strtoupper($query['view']);
		unset( $query['view'] );
	}

	if ($view=='CREW') {
		if(isset($query['crewId']))
		{
			$segments[] = $query['crewId'];
			unset( $query['crewId'] );
		};
	}
	
	if ($view=='PAYMENT') {
		if(isset($query['actionmode']))
		{
			$segments[] = $query['actionmode'];
			unset( $query['actionmode'] );
		};
		if(isset($query['payedOrderId']))
		{
			$segments[] = $query['payedOrderId'];
			unset( $query['payedOrderId'] );
		};
		if(isset($query['result']))
		{
			$segments[] = $query['result'];
			unset( $query['result'] );
		};
	}
	if ($view=='ORDERS') {
		if(isset($query['checkmode']))
		{
			$segments[] = $query['checkmode'];
			unset( $query['checkmode'] );
		};
	}
	
	if ($view=='CART') {
		if(isset($query['view']))
		{
			$segments[] = $query['view'];
			unset( $query['view'] );
		};
	}
	
	if ($view=='MERCHANTS') {
		if(isset($query['typeId']))
		{
			$segments[] = $query['typeId'];
			unset( $query['typeId'] );
		};
		if(isset($query['rating']))
		{
			$segments[] = $query['rating'];
			unset( $query['rating'] );
		};
	}
	
	if ($view=='MERCHANTDETAILS') {
		if(isset($query['merchantId']))
		{
			$segments[] = $query['merchantId'];
			unset( $query['merchantId'] );
		};
	}
	
	if ($view=='RESOURCE') {
		if(isset($query['resourceId']))
		{
			$segments[] = $query['resourceId'];
			unset( $query['resourceId'] );
		};
	}

	if ($view=='ONSELLUNIT') {
		if(isset($query['resourceId']))
		{
			$segments[] = $query['resourceId'];
			unset( $query['resourceId'] );
		};
	}
	
	if ($view=='CONDOMINIUM') {
		if(isset($query['resourceId']))
		{
			$segments[] = $query['resourceId'];
			unset( $query['resourceId'] );
		};
	}

	if(isset($query['layout']))
	{
		if ($view != '') {
			$l = JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_' . $view . '_LAYOUT_' . strtoupper($query['layout']));
			if ($l == 'COM_BOOKINGFORCONNECTOR_VIEWS_' . $view . '_LAYOUT_' . strtoupper($query['layout'])) {
				$l = $query['layout'];
			}
			$segments[] = strtolower($l);
			unset( $query['layout'] );
		}
	};

	if ($view=='MERCHANTDETAILS') {
		if(isset($query['packageId']))
		{
			$segments[] = $query['packageId'];
			unset( $query['packageId'] );
		};
		if(isset($query['offerId']))
		{
			$segments[] = $query['offerId'];
			unset( $query['offerId'] );
		};
		if(isset($query['onsellunitid']))
		{
			$segments[] = $query['onsellunitid'];
			unset( $query['onsellunitid'] );
		};
	}	
		
	if ($view=='CONDOMINIUM') {
		if(isset($query['search']))
		{
			$segments[] = $query['search'];
			unset( $query['search'] );
		};
	}
	if ($view=='USER') {
		if(isset($query['view']))
		{
			$segments[] = $query['view'];
			unset( $query['view'] );
		};
	}
	
	if ($view=='TAG') {
		if(isset($query['view']))
		{
			$segments[] = $query['view'];
			unset( $query['view'] );
		};
		if(isset($query['tagId']))
		{
			$segments[] = $query['tagId'];
			unset( $query['tagId'] );
		};
	}
	
	return $segments;
}



/**
 * Parse the segments of a URL.
 *
 * @param	array	The segments of the URL to parse.
 *
 * @return	array	The URL attributes to be used by the application.
 * @since	1.5
 */
function BookingForConnectorParseRoute($segments)
{
	$pathbase = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;
	$vars = array();
	$count = count( $segments );
	$app = JFactory::getApplication();
	$lang = JFactory::getLanguage();
	$lang->load('com_bookingforconnector', $pathbase, 'en-GB', true);
	$lang->load('com_bookingforconnector', $pathbase, $lang->getTag(), true);
					
	switch(strtolower($segments[0]))
	{
		case 'cart':	
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CARTDETAILS'):
			$vars['view'] = 'cart';
			break;
		case 'tag':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_TAG'):
			$vars['view'] = 'tag';
			break;
		case 'user':	
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_USER'):
			$vars['view'] = 'user';
			break;
		case 'searchonsell':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_SEARCHONSELL'):
			$vars['view'] = 'searchonsell';
			if ($count>1) {
				$layout = $segments[1];
				switch (strtolower($layout))
				{
					case 'thanks':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_SEARCHONSELL_LAYOUT_THANKS'):
						$vars['layout'] = 'thanks';
						break;
					default:
						break;
				}
			}
			break;
		case 'payment':			
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_PAYMENT'):
			$vars['view'] = 'payment';
			if ($count>1) {
				$actionmode = $segments[1];
				$vars['actionmode'] = $actionmode;
			}
			if ($count>2) {
				$orderId = $segments[2];
				$vars['orderId'] = $orderId;
			}
				if ($count>3) {
				$result = $segments[3];
				$vars['result'] = $result;
			}
			break;
		case 'crew':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CREW'):
			$vars['view'] = 'crew';
			if ($count>1) {
				$crewId = $segments[1];
				$vars['crewId'] = $crewId;
			}
			break;
		case 'orders':	
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ORDERS'):
			$vars['view'] = 'orders';
			if(!empty($segments[1])){
				$checkmode = $segments[1];
				$vars['checkmode'] = $checkmode;
			}
			break;
		case 'resources':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_RESOURCES'):
			$vars['view'] = 'resources';
			break;
		case 'search':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_SEARCH'):
			$vars['view'] = 'search';
			if ($count>1) {
				$layout = $segments[1];
				switch (strtolower($layout))
				{
					case 'thanks':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_SEARCH_LAYOUT_THANKS'):
						$vars['layout'] = 'thanks';
						break;	
					case 'filters':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_SEARCH_LAYOUT_FILTERS'):
						$vars['layout'] = 'filters';
						break;	
					default:
						break;
				}
			}
			break;
		case 'onsellunits':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNITS'):
			$vars['view'] = 'onsellunits';
			if ($count>1) {
				$vars['layout'] = $segments[1];
			}
			break;
		case 'onsellunit':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT'):
			$vars['view'] = 'onsellunit';
			if ($count>1) {
				$resourceid = explode( ':', $segments[1]);
				$vars['resourceId'] = (int) $resourceid[0];
			}
			if ($count>2) {
				$layout = $segments[2];
				switch (strtolower($layout))
				{
					case 'map':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_LAYOUT_MAP'):
						$vars['layout'] = 'map';
						break;						
					case 'form':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_LAYOUT_FORM'):
						$vars['layout'] = 'form';
						break;						
					case 'inforequestpopup':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_LAYOUT_INFOREQUESTPOPUP'):
						$vars['layout'] = 'inforequestpopup';
						break;	
					case 'rapidview':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_ONSELLUNIT_LAYOUT_RAPIDVIEW'):
						$vars['layout'] = 'rapidview';
						break;	
					default:
						break;
				}
			}			
			break;
		case 'resource':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_RESOURCE'):
			$vars['view'] = 'resource';
			if ($count>1) {
				$resourceid = explode( ':', $segments[1]);
				$vars['resourceId'] = (int) $resourceid[0];
			}
			if ($count>2) {
				$layout = $segments[2];
				switch (strtolower($layout))
				{
					case 'map':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_RESOURCE_LAYOUT_MAP'):
						$vars['layout'] = 'map';
						break;						
					case 'form':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_RESOURCE_LAYOUT_FORM'):
						$vars['layout'] = 'form';
						break;						
					case 'inforequestpopup':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_RESOURCE_LAYOUT_INFOREQUESTPOPUP'):
						$vars['layout'] = 'inforequestpopup';
						break;						
					case 'rapidview':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_RESOURCE_LAYOUT_RAPIDVIEW'):
						$vars['layout'] = 'rapidview';
						break;	
					case 'ratings':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_RESOURCE_LAYOUT_RATINGS'):
						$vars['layout'] = 'ratings';
						break;
					case 'rating':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_RESOURCE_LAYOUT_RATING'):
						$vars['layout'] = 'rating';
						break;
					default:
						break;
				}
			}			
			break;
		case 'condominium':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CONDOMINIUM'):
			$vars['view'] = 'condominium';
			if ($count>1) {
				$resourceid = explode( ':', $segments[1]);
				$vars['resourceId'] = (int) $resourceid[0];
			}
			if ($count>2) {
				$layout = $segments[2];
				switch (strtolower($layout))
				{
					case 'map':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CONDOMINIUM_LAYOUT_MAP'):
						$vars['layout'] = 'map';
						break;						
					case 'form':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CONDOMINIUM_LAYOUT_FORM'):
						$vars['layout'] = 'form';
						break;						
					case 'inforequestpopup':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CONDOMINIUM_LAYOUT_INFOREQUESTPOPUP'):
						$vars['layout'] = 'inforequestpopup';
						break;						
					case 'resourcesajax':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CONDOMINIUM_LAYOUT_RESOURCESAJAX'):
						$vars['layout'] = 'resourcesajax';
						break;
					case 'resources':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CONDOMINIUM_LAYOUT_RESOURCES'):
						$vars['layout'] = 'resources';
						break;
					case 'ratings':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CONDOMINIUM_LAYOUT_RATINGS'):
						$vars['layout'] = 'ratings';
						break;
					default:
						break;
				}
			}			
			break;
		case 'condominiums':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_CONDOMINIUMS'):
			$vars['view'] = 'condominiums';
//			$typeid = explode( ':', $segments[1]);
//			$vars['typeId'] = (int) $typeid[0];
//			$rating = explode( ':', $segments[2]);
//			$vars['rating'] = (int) $rating[0];
			break;
		case 'merchants':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTS'):
			$vars['view'] = 'merchants';
			if ($count>1) {
				$typeid = explode( ':', $segments[1]);
				$vars['typeId'] = (int) $typeid[0];
			}
			if ($count>2) {
				$rating = explode( ':', $segments[2]);
				$vars['rating'] = (int) $rating[0];
			}
			break;
		case 'merchantdetails':
		case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS'):
			$vars['view'] = 'merchantdetails';
			if ($count>1) {
			$merchantid = explode( ':', $segments[1]);
			$vars['merchantId'] = (int) $merchantid[0];
			}
			if ($count>2) {
				$layout = $segments[2];
				switch (strtolower($layout))
				{
					case 'redirect':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_REDIRECT'):
						$vars['layout'] = 'redirect';
						break;
					case 'onsellunits':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_ONSELLUNITS'):
						$vars['layout'] = 'onsellunits';
						break;
					case 'onsellunit':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_ONSELLUNIT'):
						$vars['layout'] = 'onsellunit';
						$onSellUnitId = explode( ':', $segments[3]);
						$vars['onsellunitid'] = (int) $onSellUnitId[0];
						break;
					case 'offers':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_OFFERS'):
						$vars['layout'] = 'offers';
						break;
					case 'offer':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_OFFER'):
						$vars['layout'] = 'offer';
						$offerId = explode( ':', $segments[3]);
						$vars['offerId'] = (int) $offerId[0];						
						break;
					case 'resources':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_RESOURCES'):
						$vars['layout'] = 'resources';
						break;
					case 'contactspopup':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_CONTACTSPOPUP'):
						$vars['layout'] = 'contactspopup';
						break;
					case 'contacts':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_CONTACTS'):
						$vars['layout'] = 'contacts';
						break;
					case 'ratings':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_RATINGS'):
						$vars['layout'] = 'ratings';
						break;
					case 'rating':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_RATING'):
						$vars['layout'] = 'rating';
						break;
					case 'map':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_MAP'):
						$vars['layout'] = 'map';
						break;						
					case 'rateplanslist':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_RATEPLANSLIST'):
						$vars['layout'] = 'rateplanslist';
						break;
					case 'resourcesajax':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_RESOURCESAJAX'):
						$vars['layout'] = 'resourcesajax';
						break;
					case 'thanks':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_THANKS'):
						$vars['layout'] = 'thanks';
						break;
					case 'errors':
					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_ERRORS'):
						$vars['layout'] = 'errors';
						break;
//					case 'packages':
//					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_PACKAGES'):
//						$vars['layout'] = 'packages';
//						break;
//					case 'package':
//					case JTEXT::_('COM_BOOKINGFORCONNECTOR_VIEWS_MERCHANTDETAILS_LAYOUT_PACKAGE'):
//						$vars['layout'] = 'package';
//						if ($count>3) {
//							$packageId = explode( ':', $segments[3]);
//							$vars['packageId'] = (int) $packageId[0];						
//						}
//						break;
						
					default:
						break;
				}
			}
			break;
	}
	return $vars;
}
