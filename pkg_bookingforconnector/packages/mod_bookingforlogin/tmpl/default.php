<?php
/**
 * @package   Bookingforconnector
 * @copyright Copyright (c)2006-2016 Ipertrade
 * @license   GNU General Public License version 3, or later
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$document     = JFactory::getDocument();
$language = JFactory::getLanguage()->getTag();
$app = JFactory::getApplication();
$sitename = $app->get('sitename');

$currUser = BFCHelper::getSession('bfiUser',null, 'bfi-User');
$accountloginUrl =  str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_ACCOUNTLOGIN);
$accountRegistrationUrl =  str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_ACCOUNTREGISTRATION);
$accountForgotPasswordUrl =  str_replace("{language}", substr($language,0,2), COM_BOOKINGFORCONNECTOR_ACCOUNTFORGOTPASSWORD);

$formRouteLogin = "index.php?option=com_bookingforconnector&task=bfilogin"; 

$currModID = uniqid('bfilogin');

?>
<div class="bfi-mod-bookingforlogin <?php echo $moduleclass_sfx ?> ">
<?php if($currUser==null) { ?>
<form action="<?php echo $formRouteLogin ?>" id="bfi-login-form<?php echo $currModID ?>" class="bfi-form bfi-form-vertical bfi-row">
	<div class="bfi-container">
		<div id="bfi-login-msg<?php echo $currModID ?>">
			<span id="bfi-text-login-msg<?php echo $currModID ?>"></span>
		</div>
<!-- pchLogin -->
		<div id="pchLogin<?php echo $currModID ?>">
			<div class="bfi_form_txt">
				<label for="bfiloginEmail<?php echo $currModID ?>" ><?php echo  JTEXT::_('MOD_BOOKINGFORLOGIN_EMAIL') ?></label>
				<input id="bfiloginEmail<?php echo $currModID ?>" name="email" type="email"  class="bfi-inputtext" placeholder='<?php echo  JTEXT::_('MOD_BOOKINGFORLOGIN_EMAIL') ?>' 
				autocomplete="email" onfocus="this.removeAttribute('readonly');" readonly 
				data-rule-required="true" data-rule-email="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" data-msg-email="<?php echo  JTEXT::_('MOD_BOOKINGFORLOGIN_EMAIL_ERROR') ?>" aria-required="true"
				/>
			</div>
			<div class="bfi_form_txt">
				<label for="bfiloginPassword<?php echo $currModID ?>"><?php echo  JTEXT::_('MOD_BOOKINGFORLOGIN_PASSWORD') ?></label>
				<input id="bfiloginPassword<?php echo $currModID ?>" name="password" type="password" class="bfi-inputtext" placeholder='<?php echo  JTEXT::_('MOD_BOOKINGFORLOGIN_PASSWORD') ?>' 
				data-rule-required="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" aria-required="true"
				/>
			</div>
			<div class="checkbox" style="display: none">
				<label>
					<input type="checkbox"> Remember me
				</label>
			</div>
		</div>
<!-- pchTwoFactorAuthentication -->
		<div id="pchTwoFactorAuthentication<?php echo $currModID ?>" class="bfi-hide">
			<div id="pchTwoFactorAuthenticationError<?php echo $currModID ?>">
				<?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_TWOFACTORMSG')  ?>
			</div>
			<div class="bfi_form_txt">
				<label for="twoFactorAuthCode<?php echo $currModID ?>"><?php JTEXT::_('MOD_BOOKINGFORLOGIN_TWOFACTORLABEL') ?></label>
				<input id="twoFactorAuthCode<?php echo $currModID ?>" name="twoFactorAuthCode" type="text" placeholder="<?php JTEXT::_('MOD_BOOKINGFORLOGIN_TWOFACTORLABEL') ?>" data-rule-required="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" aria-required="true" />
			</div>
		</div>
<!-- bfibtnSendLogin -->
		<div class="bfi-form-sep">
			<a href="javascript: void(0);" class="bfi-btn bfi-btn-warning bfi-btn-lg bfi-btn-block" id="bfibtnSendLogin<?php echo $currModID ?>"><?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_LOGIN')  ?></a>
			<a href="javascript: void(0);" class="bfi-btn bfi-btn-warning bfi-btn-lg bfi-btn-block " style="display:none" id="bfibtnSendConfirm<?php echo $currModID ?>"><?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_CONFIRM')  ?></a>
		</div>
		<div class="bfi-form-sep">
			<a href="javascript: bfilostpass();" id="bfibtnforgotpassword<?php echo $currModID ?>" target="" class="bfi-login-link"><?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_LOSTPASSWORD')  ?></a>
			<a href="<?php echo $accountRegistrationUrl ?>" target="_blank" class="bfi-login-link"><?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_SIGNIN')  ?></a>
		</div>
	</div>
</form>
<form id="bfi-lostpass-form<?php echo $currModID ?>" action="<?php echo $accountForgotPasswordUrl ?>" class="bfi-form bfi-form-vertical bfi-row bfi-hide">
		<div id="pchLostpass<?php echo $currModID ?>" class="bfi-container">
			<div>
				<?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_FORGOTPASSWORD')  ?>
			</div>
			 <div class="" >              
                <?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_FORGOTPASSWORD_TEXT')  ?>
            </div>
			<div class="bfi_form_txt">
				<input id="bfiLostEmail<?php echo $currModID ?>" name="email" type="email"  class="bfi-inputtext" placeholder='<?php echo  JTEXT::_('MOD_BOOKINGFORLOGIN_EMAIL') ?>' 
				data-rule-required="true" data-rule-email="true" data-msg-required="<?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_REQUIRED') ?>" data-msg-email="<?php echo  JTEXT::_('MOD_BOOKINGFORLOGIN_EMAIL_ERROR') ?>" aria-required="true"
			</div>
		</div>
		<div class="bfi-clearfix"></div>
		<div class="bfi-form-sep">
			<a href="javascript: void(0);" class="bfi-btn" id="bfibtnSendLostpass<?php echo $currModID ?>"><?php echo JTEXT::_('COM_BOOKINGFORCONNECTOR_DEFAULT_FORM_BUTTONSUBMIT') ?></a>
		</div>
		<div class="bfi-form-sep">
			<a href="javascript: bfilostpassback();" class="bfi-login-link"><?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_BACK') ?></a>
		</div>
	</div>
</form>

<?php }else{ ?>
<div class="bfi-form bfi-form-vertical bfi-row">
	<?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_WELCOME')  ?> <?php echo $currUser->Name ?> <?php echo $currUser->Surname  ?>
	<a href="<?php echo $accountloginUrl ?>" class="bfi-login-link" target="_blank"><?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_MYACCOUNT')  ?></a>
	<div class="bfi-form-sep">
		<a href="javascript: void(0);" class="bfi-btn" id="bfibtnLogout<?php echo $currModID ?>"><?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_LOGOUT')  ?></a>
	</div>
</div>
<?php } ?>
<!-- Other button -->
<script type="text/javascript">
jQuery(function($)
		{
		    jQuery("#bfi-login-form<?php echo $currModID ?> ").validate(
		    {
				errorClass: "bfi-error",
				submitHandler: function(form) {
					var $form = jQuery(form);
					bookingfor.waitSimpleWhiteBlock($form);
					jQuery(form).ajaxSubmit({
						dataType:'json',
						success:    function(data) {
							jQuery($form).unblock();
							if (data == "-1") {
								jQuery("#bfi-text-login-msg<?php echo $currModID ?>").html('<?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_LOGINSUCCESS')  ?>');
								jQuery('#pchLogin<?php echo $currModID ?>').hide();
								location.reload();
							}
							else {
								if (data == "1") {
									jQuery('#pchTwoFactorAuthentication<?php echo $currModID ?>').show();
									jQuery('#btnresendcode<?php echo $currModID ?>').show();
									jQuery('#bfibtnforgotpassword<?php echo $currModID ?>').hide();
									jQuery('#bfibtnSendConfirm<?php echo $currModID ?>').show();
									jQuery('#bfibtnSendLogin<?php echo $currModID ?>').hide();
									
									var currmsg = jQuery('#pchTwoFactorAuthenticationError<?php echo $currModID ?>').html();
									currmsg = currmsg.replace('{0}', jQuery('#bfiloginEmail<?php echo $currModID ?>').val());
									jQuery('#pchTwoFactorAuthenticationError<?php echo $currModID ?>').html(currmsg);
									jQuery('#pchLogin<?php echo $currModID ?>').hide();
								} else if (data == "2") {
									$('#pchTwoFactorAuthenticationError<?php echo $currModID ?>').html("<?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_CODENOTVALID')  ?>");
								} else if (data.length > 3 && data.substring(0, 1) == "3") {
									var timelock = data.substring(2, data.length);
									var d = new Date(timelock);
									var timelockStr = d.toLocaleString('<?php echo substr($language,0,2); ?>')
									var currmsg = "<?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_CODEUNTILDATA')  ?>";
									currmsg = currmsg.replace('{0}', timelockStr);
									$('#pchLogin<?php echo $currModID ?>').show();
									$('#pchTwoFactorAuthentication<?php echo $currModID ?>').hide();
									$('#bfibtnforgotpassword<?php echo $currModID ?>').show();
									$('#btnresendcode<?php echo $currModID ?>').hide();
									$('#twoFactorAuthCode<?php echo $currModID ?>').val('');
									$('#bfi-text-login-msg<?php echo $currModID ?>').html(currmsg);
									$('#bfi-text-login-msg<?php echo $currModID ?>').show();
								} else {
									jQuery("#bfi-text-login-msg<?php echo $currModID ?>").html('<?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_LOGINFAILED')  ?>');
								}
							}
						}
					});
				}
		    });
			var v = jQuery("#bfi-lostpass-form<?php echo $currModID ?>").validate({
				errorClass: "bfi-error",
				submitHandler: function(form) {
					var $form = jQuery(form);
					bookingfor.waitSimpleWhiteBlock($form);
					jQuery(form).ajaxSubmit({
						success:    function(data) {
							bfilostpassback();
							$form.unblock();
							if (data==true)
							{
								jQuery("#bfi-text-login-msg<?php echo $currModID ?>").html('<?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_LOSTPASSFORM')  ?>');
							}else{
								jQuery("#bfi-text-login-msg<?php echo $currModID ?>").html('<?php echo JTEXT::_('MOD_BOOKINGFORLOGIN_LOSTPASSINFO')  ?>');
							}
						}
					});
				}
			});
			jQuery('#bfibtnSendLogin<?php echo $currModID ?>').click(function() {       
				jQuery("#bfi-login-form<?php echo $currModID ?>").submit();
			});
			jQuery('#bfibtnSendConfirm<?php echo $currModID ?>').click(function() {       
				jQuery("#bfi-login-form<?php echo $currModID ?>").submit();
			});
			
			jQuery('#bfibtnSendLostpass<?php echo $currModID ?>').click(function() {       
				jQuery("#bfi-lostpass-form<?php echo $currModID ?>").submit();
			});
			jQuery('#bfibtnLogout<?php echo $currModID ?>').click(function() {       
				var queryMG = "task=bfilogout";
				jQuery.post(bfi_variable.bfi_urlCheck, queryMG, function(data) {
						if(data=="-1"){
							location.reload();
						}; 
				},'json');				
			});
		});
	function bfilostpass(){
		jQuery("#bfi-login-form<?php echo $currModID ?> ").hide();
		jQuery("#bfi-lostpass-form<?php echo $currModID ?>").show();
	}
	function bfilostpassback(){
		jQuery("#bfi-login-form<?php echo $currModID ?>").show();
		jQuery("#bfi-lostpass-form<?php echo $currModID ?>").hide();
	}

</script>	
</div>	<!-- module -->
