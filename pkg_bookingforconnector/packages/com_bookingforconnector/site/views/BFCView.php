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

JHtml::_('jquery.framework');
JHtml::_('jquery.ui');



/**
 * HTML View class for the HelloWorld Component
 */
class BFCView extends JViewLegacy 
{
	public function loadTemplate($tpl = null, $layout = null)
	{
		if ($layout == null)
			return parent::loadTemplate($tpl);
	
		$currentLayout = $this->getLayout();
		$this->setLayout($layout); // This is ugly
		$return = parent::loadTemplate($tpl);
		$this->setLayout($currentLayout);
		
		return $return;
	}

	public function checkAnalytics($listName) {
		return BFCHelper::checkAnalytics($listName);
//		$writeJs = true;
//		if (strtolower(filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH')) === 'xmlhttprequest') {
//		   $writeJs = false;
//		}
//		$document 	= JFactory::getDocument();
//		$config = JComponentHelper::getParams('com_bookingforconnector');
//		if($config->get('gaenabled', 0) == 1 && !empty($config->get('gaaccount', ''))) {
//			if($writeJs) {
//				$document->addScriptDeclaration('
//				var bookingfor_gacreated = true;
//				var bookingfor_eeccreated = false;
//				var bookingfor_gapageviewsent = 0;
//				if(!window.ga) {
//					(function(i,s,o,g,r,a,m){i["GoogleAnalyticsObject"]=r;i[r]=i[r]||function(){
//					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
//					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
//					})(window,document,"script","https://www.google-analytics.com/analytics.js","ga");
//					ga("create","' . $config->get('gaaccount') . '"' . (strpos(JURI::current(), 'localhost') !== false ? ', {
//					  "cookieDomain": "none"
//					}' : ', "auto"') . ');
//				}');
//			}
//			if($config->get('eecenabled', 0) == 1) {
//				if($writeJs) {
//					$document->addScriptDeclaration('
//					ga("require", "ec");
//					bookingfor_eeccreated = true;
//					
//					function initAnalyticsBFEvents() {
//						jQuery("body").on("click", "#grid-view, #list-view", function(e) {
//							if(e.originalEvent) {
//								//var listname = "' . $listName . ' - " + (jQuery(this).attr("id") == "grid-view" ? "Grid View" : "List View");
//								callAnalyticsEEc("", "", (jQuery(this).attr("id") == "grid-view" ? "GridView" : "ListView"), null, "changeView", "View&Sort");
//							}
//						});
//						jQuery("body").on("click", ".com_bookingforconnector-sort-item", function(e){
//							if(e.originalEvent) {
//								var listname = "OrderBy";
//								var sortType = "";
//								switch(jQuery(this).attr("rel").split("|")[0].toLowerCase()) {
//									case "reviewvalue":
//										sortType = "GuestRating";
//										break;
//									case "stay":
//									case "price":
//										sortType = "Price";
//										break;
//									case "offer":
//										sortType = "Offer";
//										break;
//									case "addedon":
//										sortType = "AddedDate";
//										break;
//									case "name":
//										sortType = "Name";
//										break;
//								}
//								if(!jQuery.trim(sortType).length) { return; }
//								listname += sortType;
//								callAnalyticsEEc("", "", listname, null, "changeSort", "View&Sort");
//							}
//						});
//						jQuery("body").on("mouseup", ".eectrack", function(e) {
//							if( e.which <= 2 ) {
//								callAnalyticsEEc("addProduct", [{
//									id: jQuery(this).attr("data-id") + " - " + jQuery(this).attr("data-type"),
//									name: jQuery(this).attr("data-itemname"), 
//									category: jQuery(this).attr("data-category"),
//									brand: jQuery(this).attr("data-brand"), 
//									//variant: jQuery(this).attr("data-type"),
//									position: parseInt(jQuery(this).attr("data-index")), 
//								}], "viewDetail", null, jQuery(this).attr("data-id"), jQuery(this).attr("data-type"));
//							}
//						});
//					}
//					
//					function callAnalyticsEEc(type, items, actiontype, list, actiondetail, itemtype) {
//						list = list && jQuery.trim(list).length ? list : "' . $listName . '";
//						switch(type) {
//							case "addProduct":
//								if(!items.length) { return; }
//								jQuery.each(items, function(i, itm) {
//									ga("ec:addProduct", itm);
//								});
//								break;
//							case "addImpression":
//								if(!items.length) { return; }
//								jQuery.each(items, function(i, itm) {
//									itm.list = list;
//									ga("ec:addImpression", itm);
//								});
//								break;
//						}
//						
//						switch(actiontype.toLowerCase()) {
//							case "click":
//								ga("ec:setAction", "click", {"list": list});
//								ga("send", "event", "Bookingfor", "click", list);
//								break;
//							case "item":
//								ga("ec:setAction", "detail");
//								ga("send","pageview");
//								bookingfor_gapageviewsent++;
//								break;
//							case "checkout":
//							case "checkout_option":
//								ga("ec:setAction", actiontype, actiondetail);
//								ga("send","pageview");
//								bookingfor_gapageviewsent++;
//								break;
//							case "addtocart":
//								ga("set", "&cu", "EUR");
//								ga("ec:setAction", "add", actiondetail);
//								ga("send", "event", "Bookingfor - " + itemtype, "click", "addToCart");
//								bookingfor_gapageviewsent++;
//								break;
//							case "purchase":
//								ga("set", "&cu", "EUR");
//								ga("ec:setAction", "purchase", actiondetail);
//								ga("send","pageview");
//								bookingfor_gapageviewsent++;
//							case "list":
//								ga("send","pageview");
//								bookingfor_gapageviewsent++;
//								break;
//							default:
//								ga("ec:setAction", "click", {"list": list});
//								ga("send", "event", "Bookingfor - " + itemtype, actiontype, actiondetail);
//								break;
//						}
//					}
//					
//					jQuery(function(){
//						initAnalyticsBFEvents();
//					});
//					
//					');
//				}
//			}
//			return true;
//		}
//		return false;
	}

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, 
	 *
	 * @see     fetch()
	 * @since   11.1
	 */
	public function display($tpl = null, $preparecontent = false)
	{
		$result = $this->loadTemplate($tpl);
		if ($result instanceof Exception)
		{
			return $result;
		}		
		if ($preparecontent) {
			$result = JHTML::_('content.prepare', $result );
		}

		echo $result;
				
	}
}