<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$this->document->setTitle($this->item->Name);
$this->document->setDescription( BFCHelper::getLanguage($this->item->Description, $this->language));
$merchantSiteUrl = '';
if ($this->item->AddressData->SiteUrl != '') {
	$merchantSiteUrl =$this->item->AddressData->SiteUrl;
	$parsed = parse_url($merchantSiteUrl);
	if (empty($parsed['scheme'])) {
		$merchantSiteUrl = 'http://' . ltrim($merchantSiteUrl, '/');
	}
//	if (strpos('http://', $merchantSiteUrl) == false) {
//		$merchantSiteUrl = 'http://' . $merchantSiteUrl;
//	}
}
$metodForm = "";
if (strpos($merchantSiteUrl,'%3f')!==false || strpos($merchantSiteUrl,'?')!==false ){
	$metodForm = "post";
}
if (strpos($merchantSiteUrl,'?post')!==false ){
	$metodForm = "post";
	$merchantSiteUrl = str_replace("?post", "", $merchantSiteUrl);
}

?>
<style>
	body#bd{
		background-color: #ffffff;
		background-image:none;
	}
</style>

<div style="text-align:center;">

	<form method="<?php echo $metodForm?>" action="<?php echo $merchantSiteUrl?>" id="redirectfromsite" name="redirectfromsite">
		<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_REDIRECT_WAIT')?>
		<a href="<?php echo $merchantSiteUrl?>"><?php echo $merchantSiteUrl?></a> 
		<br /><br />
<script type="text/javascript">
<!--
if (typeof(ga) !== 'undefined') {
	ga('send', 'event', 'Bookingfor', 'Website', '<?php echo $merchantSiteUrl?>');
	ga(function(){
		function gred(id){return document.getElementById?document.getElementById(id  ):document.all(id);}
		gred("redirectfromsite").submit();
	});
}else{
		function gred(id){return document.getElementById?document.getElementById(id  ):document.all(id);}
		gred("redirectfromsite").submit();
}
//-->
</script>
	</form>

</div>
