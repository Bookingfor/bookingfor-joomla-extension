<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pathbase = JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_bookingforconnector' . DIRECTORY_SEPARATOR;

class BFFavoritesHelper
{
	public static function getFavoritesFromSession() {
		$sessionkey = 'bffavorites';
		$session = JFactory::getSession();
		$pars = $session->get($sessionkey, '', 'mod_bookingforfavorites'); 
		return $pars;
	}

	public static function setFavoritesFromSession($pars) {
		$sessionkey = 'bffavorites';
		$session = JFactory::getSession();
		$session->set($sessionkey, $pars, 'mod_bookingforfavorites'); 
	}
	
	public static function getListFavorites() {
		$html = "";
		$favlist = self::getFavoritesFromSession();
		if(isset($favlist) && is_array($favlist) && count($favlist) ){
			$html .= '<table class="table table-hover table-condensed">';

			foreach ($favlist as $key => $value) {
				$html .= '<tr>';
				$html .= '<td class="span11"><a href="'.$key.'">'.$value.'</a></td><td class="span1"><a class="removefromfav" href="javascript:removefromfav(';
				$html .= "'".$key."'";
				$html .= ')">x</a></td>';
				$html .= '</tr>';
			}
			$html .= '</table>';
		}else{
			$html .= JTEXT::_('MOD_BOOKINGFORFAVORITES_NORESULT');
			}
		return $html;
	}

	public static function addFavorites($key, $value) {
		$favlist = self::getFavoritesFromSession();
		if(!isset($favlist) && !is_array($favlist)){ 
			$favlist = array();
		}
		$favlist[$key] = $value;
		self::setFavoritesFromSession($favlist);
	}
	public static function removeFavorites($key) {
		$favlist = self::getFavoritesFromSession();
		if(isset($favlist) && is_array($favlist)) {
			unset($favlist[$key]);
		}
		self::setFavoritesFromSession($favlist);
	}

}
?>
