<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class BFCHelper {
	private static $basePath = 'http://ws.caorle.my/images';
	private static $paths = array(
		'merchant' => '/Merchants/'
	);
	
	private static $resizes = array(
		'merchant_list' => 'width=100'
	);
	
	public static function getItem($xml, $itemName) {
		$xdoc = new SimpleXmlElement($xml);
		$item = $xdoc->$itemName;
		return $item;
	}
	
	public static function getImageUrl($type, $path, $resize) {
		$finalPath = self::$basePath . self::$paths[$type] . $path;
		if (isset($resize)){
			if (isset(self::$resizes[$resize])) {
				$finalPath .= '?' . self::$resizes[$resize];
			}
		}
		
		return $finalPath;
	}
}
