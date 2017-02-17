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
require_once $pathbase . 'router.php';
require_once $pathbase . 'helpers/BFCHelper.php';

class plgSearchBookingforconnector extends JPlugin
{
	/**
	 * Constructor
	 *
	 * @access      protected
	 * @param       object  $subject The object to observe
	 * @param       array   $config  An array that holds the plugin configuration
	 * @since       1.5
	 */
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * @return array An array of search areas
	 */
	function onContentSearchAreas()
	{
		static $areas = array(
				'bookingforconnector' => 'PLG_SEARCH_BOOKINGFORCONNECTOR'
				);
				return $areas;
	}

	function onContentSearch($text, $phrase='', $ordering='', $areas=null)
	{

		//If the array is not correct, return it:
		if (is_array( $areas )) {
			if (!array_intersect( $areas, array_keys( $this->onContentSearchAreas() ) )) {
				return array();
			}
		}
		$section = JText::_( 'PLG_SEARCH_BOOKINGFORCONNECTOR' );

		//Then load the parameters of the plugin.
		$pluginParams = $this->params;

		//Now define the parameters like this:
		$limit = $pluginParams->def( 'search_limit', 50 );
		$direction = $pluginParams->def( 'direction', "asc" );

		//Use the function trim to delete spaces in front of or at the back of the searching terms
		$text = trim( $text );

		//Return Array when nothing was filled in.
		if ($text == '') {
			return array();
		}

		$wheres = array();
		switch ($phrase) {

			//search exact
			case 'exact':
				/*$text		= $db->Quote( '%'.$db->getEscaped( $text, true ).'%', false );
				$where 		= '(' . implode( ') OR (', $wheres2 ) . ')';*/
				break;

				//search all or any
			case 'all':
			case 'any':

				//set default
			default:
				/*
				 $words 	= explode( ' ', $text );
				 $wheres = array();
				 foreach ($words as $word)
				 {
				 $word		= $db->Quote( '%'.$db->getEscaped( $word, true ).'%', false );
				 $wheres2 	= array();
				 $wheres2[] 	= 'LOWER(a.name) LIKE '.$word;
				 $wheres[] 	= implode( ' OR ', $wheres2 );
				 }
				 $where = '(' . implode( ($phrase == 'all' ? ') AND (' : ') OR ('), $wheres ) . ')';
				 */
				break;
		}

		//ordering of the results
		switch ( $ordering ) {

			//alphabetic, ascending
			case 'alpha':
				$order = 'Name';
				break;

				//oldest first
			case 'oldest':

				//popular first
			case 'popular':

				//newest first
			case 'newest':

				//default setting: alphabetic, ascending
			default:
				$order = 'Name';
		}
		//replace nameofplugin

		$rows = array();

		$merchants = BFCHelper::getMerchantsSearch($text,0,$limit,$order,$direction);
		$document 	= JFactory::getDocument();
		$language 	= $document->getLanguage();
		
		/* we have to find the itemid for the target page */ 
		$db   = JFactory::getDBO();
		$lang = JFactory::getLanguage()->getTag();
		$uri  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
		
		$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uri .'%' ) .' AND language='. $db->Quote($lang) .' LIMIT 1' );
		
		$itemId = ($db->getErrorNum())? 0 : intval($db->loadResult());
		
		//The 'output' of the displayed link
		foreach($merchants as $merchant) {
			//$rows[$key]->href = 'index.php?option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);
			$rows[] = (object) array(
					'href'        => Jroute::_('index.php?Itemid='.$itemId.'&option=com_bookingforconnector&view=merchantdetails&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name)),
					'title'       => $merchant->Name,
					'created'     => null,
					'section'     => $section,
					'text'        => BFCHelper::getLanguage($merchant->Description,$language),
					'browsernav'  => '0'
					);
		}

		//Return the search results in an array
		return $rows;
	}
}
