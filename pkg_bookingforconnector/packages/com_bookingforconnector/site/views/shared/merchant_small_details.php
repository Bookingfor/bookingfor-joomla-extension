<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */


// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$merchantSiteUrl = '';
$mrcindirizzo = "";
$mrccap = "";
$mrccomune = "";
$mrcstate = "";

if (empty($merchant->AddressData)){
	$mrcindirizzo = isset($merchant->Address)?$merchant->Address:""; 
	$mrccap = isset($merchant->ZipCode)?$merchant->ZipCode:""; 
	$mrccomune = isset($merchant->CityName)?$merchant->CityName:""; 
	$mrcstate = isset($merchant->StateName)?$merchant->StateName:""; 
	$merchantSiteUrl = isset($merchant->SiteUrl)?$merchant->SiteUrl:""; 
}else{
	$addressData = isset($merchant->AddressData)?$merchant->AddressData:"";
	$mrcindirizzo = isset($addressData->Address)?$addressData->Address:""; 
	$mrccap = isset($addressData->ZipCode)?$addressData->ZipCode:""; 
	$mrccomune = isset($addressData->CityName)?$addressData->CityName:""; 
	$mrcstate = isset($addressData->StateName)?$addressData->StateName:"";
	$merchantSiteUrl = isset($addressData->SiteUrl)?$addressData->SiteUrl:""; 
}
//	if (!empty($merchantSiteUrl)) {
//		$parsed = parse_url($merchantSiteUrl);
//		if (empty($parsed['scheme'])) {
//			$merchantSiteUrl = 'http://' . ltrim($merchantSiteUrl, '/');
//		}
//	}

//TODO: da controllare perchè con risorse a catalogo non ricava l'url correttamente
//if (!isset($uriMerchant)) {
	$db   = JFactory::getDBO();
	$itemIdMerchant=0;
	$uriMerchant  = 'index.php?option=com_bookingforconnector&view=merchantdetails';
	$db->setQuery('SELECT id FROM #__menu WHERE link LIKE '. $db->Quote( $uriMerchant .'%' ) .' AND (language='. $db->Quote($language) .' OR language='.$db->Quote('*').') AND published = 1 LIMIT 1' );
	$itemIdMerchant = intval($db->loadResult());
	
	$uriMerchant.='&merchantId=' . $merchant->MerchantId . ':' . BFCHelper::getSlug($merchant->Name);
	if ($itemIdMerchant<>0){
		$uriMerchant.='&Itemid='.$itemIdMerchant;
	}
//}
//$uriMerchant = $routeMerchant;

//$route = $routeMerchant;

//$uriMerchantResources = $uriMerchant .'/'._x( 'resources', 'Page slug', 'bfi' ).'?limitstart=0';
//$uriMerchantOffers = $uriMerchant .'/'._x('offers', 'Page slug', 'bfi' ).'?limitstart=0';
//$uriMerchantOnsellunits = $uriMerchant .'/'._x( 'onsellunits', 'Page slug', 'bfi' ).'?limitstart=0';
//$uriMerchantRatings = $uriMerchant .'/'._x('reviews', 'Page slug', 'bfi' );
//$uriMerchantRedirect = $uriMerchant .'/'._x('redirect', 'Page slug', 'bfi' );
//$uriMerchantInfoRequest = $uriMerchant .'/'._x('contactspopup', 'Page slug', 'bfi' );

$uriMerchantResources = JRoute::_($uriMerchant .'&layout=resources&limitstart=0');
$uriMerchantOffers = JRoute::_($uriMerchant .'&layout=offers&limitstart=0');
$uriMerchantOnsellunits = JRoute::_($uriMerchant .'&layout=onsellunits&limitstart=0');
$uriMerchantRatings = JRoute::_($uriMerchant .'&layout=ratings');
$uriMerchantRedirect = JRoute::_($uriMerchant .'&layout=redirect&tmpl=component');
$uriMerchantInfoRequest = JRoute::_($uriMerchant . '&layout=contactspopup&tmpl=component&format=raw');

$merchantLogo = JURI::root() . "components/com_bookingforconnector/assets/images/defaults/default-s3.jpeg";
if (!empty($merchant->LogoUrl)){
	$merchantLogo = BFCHelper::getImageUrlResized('merchant',$merchant->LogoUrl, 'logobig');
}

if(BFCHelper::getVar( 'view')=="resource" && !empty($resource_id)){
	$uriMerchantInfoRequest .= '&resourceid='.$resource_id.'&resourceType=resource' ;
}
if(BFCHelper::getVar( 'view')=="onsellunit" && !empty($resource_id)){
	$uriMerchantInfoRequest .= '&resourceid='.$resource_id.'&resourceType=onsellunit';
}
?>
<div class=" bfi-hideonextra">
	<br />
	<div class=" bfi-border">
		<div class="bfi-row bfi-merchant-simple bfi-hideonextra">
			<div class="bfi-col-md-4">
					<div class="bfi-vcard-name">
						<a href="<?php echo ($isportal)?$routeMerchant :"/";?>"><?php echo  $merchant->Name?></a>
						<span class="bfi-item-rating">
						<?php for($i = 0; $i < $merchant->Rating ; $i++) { ?>
						  <i class="fa fa-star"></i>
						<?php } ?>
						</span>
					</div>
					<div class="bfi-row ">
						<div class="bfi-col-md-5 bfi-vcard-logo-box">
							<div class="bfi-vcard-logo"><a href="<?php echo ($isportal)?$routeMerchant :"/";?>"><img src="<?php echo $merchantLogo?>" /></a></div>	
						</div>
						<div class="bfi-col-md-7 bfi-pad0-10 bfi-street-address-block">
							
							<span class="bfi-street-address"><?php echo $mrcindirizzo ?></span>, <span class="postal-code "><?php echo $mrccap ?></span> <span class="locality"><?php echo $mrccomune ?></span> <span class="state">, <?php echo $mrcstate ?></span><br />
						</div>
						<?php if($isportal) { ?>
							<div class="bfi-row bfi-text-center bfi-marchant-ref">
								<div class="bfi-text-center">
									<span class="tel "><a  href="javascript:void(0);" onclick="bookingfor.getData(bfi_variable.bfi_urlCheck,'merchantid=<?php echo $merchant->MerchantId?>&task=GetPhoneByMerchantId&language=' + bfi_variable.bfi_cultureCode,this,'<?php echo  addslashes($merchant->Name) ?>','PhoneView')"  id="phone<?php echo $merchant->MerchantId?>" class="bfi-btn bfi-alternative2"><?php echo  JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_SHOWPHONE') ?></a></span>
									<?php if ($merchantSiteUrl != ''):?><span class="website"><a target="_blank" href="<?php echo $uriMerchantRedirect; ?>" class="bfi-btn bfi-alternative2"><?php echo JTEXT::_('MOD_BOOKINGFORCONNECTOR_MERCHANT_SITEGO') ?></a></span>
									<?php endif;?>
								</div>
							</div>
						<?php } ?>
					</div>			
					<div class="bfi-height10"></div>
					<div class="bfi-text-center">
							<a class="boxedpopup bfi-btn bfi-alternative" href="<?php echo $uriMerchantInfoRequest?>" style="width: 100%;"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_EMAIL') ?></a>
					</div>
			</div>	
			<div class="bfi-col-md-8 bfi-pad10">
				<ul class="bfi-menu-small">
				<?php if($isportal) { ?>
					<?php if ($merchant->HasResources):?>
						<li><a href="<?php echo $uriMerchantResources; ?>" class="bfi-btn bfi-alternative3"><?php echo JTEXT::_('MOD_BOOKINGFORCONNECTOR_RESOURCES') ?></a></li>
					<?php endif ?>
					<?php if ($merchant->HasOnSellUnits):?>
						<li><a href="<?php echo $uriMerchantOnsellunits; ?>" class="bfi-btn bfi-alternative3"><?php echo JTEXT::_('MOD_BOOKINGFORCONNECTOR_ONSELL') ?></a></li>
					<?php endif ?>	
					<?php if ($merchant->HasResources):?>
						<?php if ($merchant->HasOffers || true):?>
							<li><a href="<?php echo $uriMerchantOffers; ?>" class="bfi-btn bfi-alternative3"><?php echo JTEXT::_('MOD_BOOKINGFORCONNECTOR_OFFERS') ?></a></li>
						<?php endif ?>
					<?php endif;?>
					<?php if ($merchant->RatingsContext !== 0) :?>
						<li><a href="<?php echo $uriMerchantRatings; ?>" class="bfi-btn bfi-alternative3"><?php echo JTEXT::_('MOD_BOOKINGFORCONNECTOR_RATINGS') ?></a></li>
					<?php endif ?>	
				<?php } ?>
				</ul>
				<?php 
				if(($merchant->AcceptanceCheckIn != "-" && $merchant->AcceptanceCheckOut != "-") || !empty($merchant->OtherDetails) ){
				?>
					<div><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_INFO_TITLE') ?></div>
					<div class="bfi-pad10-0 ">   
						<?php if($merchant->AcceptanceCheckIn != "-"){ ?> <?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_ARRIVO') ?> <?php echo $merchant->AcceptanceCheckIn ?>
						<?php } ?>
						<?php if($merchant->AcceptanceCheckOut != "-"){ ?>
						<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_PARTENZA') ?> <?php echo $merchant->AcceptanceCheckOut ?>
						<?php } ?>
					</div>
					<?php if(!empty($merchant->OtherDetails) ){ ?>
						<div class="applyshorten"><?php echo BFCHelper::getLanguage($merchant->OtherDetails, $language, null, array('ln2br'=>'ln2br', 'striptags'=>'striptags'))  ?></div>
					<?php } ?>					
				<?php 
				}
				?>


			</div>	
		</div>	
	</div>
</div>
<script type="text/javascript">
<!--
	window.bfirecaptcha ={};
window.BFIInitReCaptcha2 = function() {
    "use strict";
	var e = document.getElementsByClassName("bfi-recaptcha"),
        t, n;
    for (var r = 0, i = e.length; r < i; r++){ t = e[r], n = {
            'sitekey': t.getAttribute("data-sitekey"),
            'theme': t.getAttribute("data-theme"),
            'size': t.getAttribute("data-size")
        };
		if (window.bfirecaptcha[t.id]!== undefined) {
				grecaptcha.reset(window.bfirecaptcha[t.id]);
		} else {
				window.bfirecaptcha[t.id] = grecaptcha.render(t, n)
		}
	}
};
	var bfishortenOption = {
		moreText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_PLUS')?>",
		lessText: "<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_MERCHANTS_VIEW_MERCHANTDETAILS_RATING_MINUS')?>",
		showChars: '250'
	};
	jQuery(".applyshorten").shorten(bfishortenOption);

//-->
</script>