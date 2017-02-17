var bookingfor = new function() {
    this.version = "2.0.6";
	this.bsVersion = ( typeof jQuery.fn.typeahead !== 'undefined' ? 2 : 3 );

    this.getDiscountAjaxInformations = function (discountId, hasRateplans) {
        if (cultureCode.length > 1) {
          cultureCode = cultureCode.substring(0, 2).toLowerCase();
        }
        if (defaultcultureCode.length > 1) {
          defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
        }

        var query = "discountId=" + discountId + "&hasRateplans=" + hasRateplans + "&language=en-gb&task=getDiscountDetails";
        jQuery.getJSON(urlCheck + "?" + query, function(data) {

          var name = getXmlLanguage(data.Name, cultureCode, defaultcultureCode);;
          name = nl2br(jQuery("<p>" + name + "</p>").text());
          jQuery("#divoffersTitle" + discountId).html(name);

          var descr = getXmlLanguage(data.Description, cultureCode, defaultcultureCode);;
          descr = nl2br(jQuery("<p>" + descr + "</p>").text());
          jQuery("#divoffersDescr" + discountId).html(descr);
          jQuery("#divoffersDescr" + discountId).removeClass("com_bookingforconnector_loading");
        });

      };

    this.getRateplanAjaxInformations = function (rateplanId) {
        if (cultureCode.length > 1) {
          cultureCode = cultureCode.substring(0, 2).toLowerCase();
        }
        if (defaultcultureCode.length > 1) {
          defaultcultureCode = defaultcultureCode.substring(0, 2).toLowerCase();
        }

        var query = "rateplanId=" + rateplanId + "&language=en-gb&task=getRateplanDetails";
        jQuery.getJSON(urlCheck + "?" + query, function(data) {

          var name = getXmlLanguage(data.Name, cultureCode, defaultcultureCode);;
          name = nl2br(jQuery("<p>" + name + "</p>").text());
          jQuery("#divrateplanTitle" + rateplanId).html(name);

          var descr = getXmlLanguage(data.Description, cultureCode, defaultcultureCode);;
          descr = nl2br(jQuery("<p>" + descr + "</p>").text());
          jQuery("#divrateplanDescr" + rateplanId).html(descr);
          jQuery("#divrateplanDescr" + rateplanId).removeClass("com_bookingforconnector_loading");
        });

      };

    this.getData = function (urlCheck, query, elem, name, act) {
		query += '&simple=1';
		if (typeof(ga) !== 'undefined') {
			ga('send', 'event', 'Bookingfor', act, name);
			ga(function(){
				jQuery.post(urlCheck, query, function(data) {
						jQuery(elem).parent().html(data);
						jQuery(elem).remove();
				});
			});
		}else{
			jQuery.post(urlCheck, query, function(data) {
					jQuery(elem).parent().html(data);
					jQuery(elem).remove();
			});
		}
	};

    this.getXmlLanguage = function (value, cultureCode, defaultcultureCode) {
		var ret = value;
		if(value && value.indexOf("<languages>")>-1){
			var xmlValue = jQuery.parseXML(value);
			var jsonValue = jQuery.xml2json(xmlValue);
			try {
				if (jsonValue.language.hasOwnProperty("code")) {
					ret = (jsonValue.language.hasOwnProperty("text") ? jsonValue.language.text : "") ;
				} else {
					var defaultValue = '';
					jQuery.each(jsonValue.language, function (i, lang) {
						if (lang.code === cultureCode)
						{
							ret = (lang.hasOwnProperty("text") ? lang.text : "") ;
						}
						if (lang.code === defaultcultureCode)
						{
							defaultValue = (lang.hasOwnProperty("text") ? lang.text : "") ;
						}

					});
					if(ret===''){
						ret = defaultValue;
					}

				}
			}
			catch (e) {
			}
		}
		return ret;
	};

	this.make_slug = function ( str )
	{
		str = str.toLowerCase();
		str = str.replace(/\&+/g, 'and');
		str = str.replace(/[^a-z0-9]+/g, '-');
		str = str.replace(/^-|-$/g, '');
		return str;
	};

	this.nl2br = function (str, is_xhtml) {   
		var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
		return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
	};

	this.nomore1br = function (str) {   
		return (str + '').replace(new RegExp('(\n){2,}', 'gim') , '\n');
	};

	this.number_format = function (number, decimals, dec_point, thousands_sep) {
	  //  discuss at: http://phpjs.org/functions/number_format/
	  // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // improved by: davook
	  // improved by: Brett Zamir (http://brett-zamir.me)
	  // improved by: Brett Zamir (http://brett-zamir.me)
	  // improved by: Theriault
	  // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
	  // bugfixed by: Michael White (http://getsprink.com)
	  // bugfixed by: Benjamin Lupton
	  // bugfixed by: Allan Jensen (http://www.winternet.no)
	  // bugfixed by: Howard Yeend
	  // bugfixed by: Diogo Resende
	  // bugfixed by: Rival
	  // bugfixed by: Brett Zamir (http://brett-zamir.me)
	  //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
	  //  revised by: Luke Smith (http://lucassmith.name)
	  //    input by: Kheang Hok Chin (http://www.distantia.ca/)
	  //    input by: Jay Klehr
	  //    input by: Amir Habibi (http://www.residence-mixte.com/)
	  //    input by: Amirouche
	  //   example 1: number_format(1234.56);
	  //   returns 1: '1,235'
	  //   example 2: number_format(1234.56, 2, ',', ' ');
	  //   returns 2: '1 234,56'
	  //   example 3: number_format(1234.5678, 2, '.', '');
	  //   returns 3: '1234.57'
	  //   example 4: number_format(67, 2, ',', '.');
	  //   returns 4: '67,00'
	  //   example 5: number_format(1000);
	  //   returns 5: '1,000'
	  //   example 6: number_format(67.311, 2);
	  //   returns 6: '67.31'
	  //   example 7: number_format(1000.55, 1);
	  //   returns 7: '1,000.6'
	  //   example 8: number_format(67000, 5, ',', '.');
	  //   returns 8: '67.000,00000'
	  //   example 9: number_format(0.9, 0);
	  //   returns 9: '1'
	  //  example 10: number_format('1.20', 2);
	  //  returns 10: '1.20'
	  //  example 11: number_format('1.20', 4);
	  //  returns 11: '1.2000'
	  //  example 12: number_format('1.2000', 3);
	  //  returns 12: '1.200'
	  //  example 13: number_format('1 000,50', 2, '.', ' ');
	  //  returns 13: '100 050.00'
	  //  example 14: number_format(1e-8, 8, '.', '');
	  //  returns 14: '0.00000001'

	  number = (number + '')
		.replace(/[^0-9+\-Ee.]/g, '');
	  var n = !isFinite(+number) ? 0 : +number,
		prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
		s = '',
		toFixedFix = function (n, prec) {
		  var k = Math.pow(10, prec);
		  return '' + (Math.round(n * k) / k)
			.toFixed(prec);
		};
	  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
	  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n))
		.split('.');
	  if (s[0].length > 3) {
		s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
	  }
	  if ((s[1] || '')
		.length < prec) {
		s[1] = s[1] || '';
		s[1] += new Array(prec - s[1].length + 1)
		  .join('0');
	  }
	  return s.join(dec);
	};

	this.updateQueryStringParameter = function (uri, key, value) {
	  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
	  var separator = uri.indexOf('?') !== -1 ? "&" : "?";
	  if (uri.match(re)) {
		return uri.replace(re, '$1' + key + "=" + value + '$2');
	  }
	  else {
		return uri + separator + key + "=" + value;
	  }
	};



	this.waitBlockUI = function (msg1 ,msg2, img1){
	msg1 = msg1 ? msg1 : "";
	msg2 = msg2 ? msg2 : "";
	var msggeneral = jQuery.trim(msg1).length && jQuery.trim(msg2).length ? msg1 + '<br />' + msg2 : (jQuery.trim(msg1).length ? msg1 : msg2);
	jQuery.blockUI({
		message: (jQuery.trim(msggeneral).length ? '<h1 style="font-size: 15px;">'+msggeneral+'</h1><br />' : "") + '<i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>', 
		css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B'},
		overlayCSS: {backgroundColor: '#1D668B', opacity: .7}  
		});
	};

	this.waitBlock = function (msg1 ,msg2, obj){
	obj.block({
		message: '<h1 style="font-size: 15px;">'+msg1+'<br />'+msg2+'</h1><br /><i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>', 
		css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B', width: '80%'},
//		overlayCSS: {backgroundColor: '#1D668B', opacity: .7}  
		overlayCSS: {backgroundColor: '#1D668B', opacity: 0}  
		});
	};

	this.dateAdd = function(date, interval, units) {
		var ret = new Date(date); //don't change original date
		switch(interval.toLowerCase()) {
			case 'year'   :  ret.setFullYear(ret.getFullYear() + units);  break;
			case 'quarter':  ret.setMonth(ret.getMonth() + 3*units);  break;
			case 'month'  :  ret.setMonth(ret.getMonth() + units);  break;
			case 'week'   :  ret.setDate(ret.getDate() + 7*units);  break;
			case 'day'    :  ret.setDate(ret.getDate() + units);  break;
			case 'hour'   :  ret.setTime(ret.getTime() + units*3600000);  break;
			case 'minute' :  ret.setTime(ret.getTime() + units*60000);  break;
			case 'second' :  ret.setTime(ret.getTime() + units*1000);  break;
			default       :  ret = undefined;  break;
		}
		return ret;
	}

	this.convertDateToInt = function(currDate) {
		var month = currDate.getMonth() + 1;
		var day = currDate.getDate();
		var year = currDate.getFullYear();
		var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);
		var intDate = Number(datereformat);
		return (intDate)
	}

	this.pad = function(str, max) {
		if (!str) {
			str = "";
		}
		str = str.toString();
		return str.length < max ? this.pad("0" + str, max) : str;
	}

	this.addToCart = function(objSource) {
		bookingfor.waitBlockUI();
//		jQuery.blockUI({ message: ''});
		var cart = jQuery('.bookingfor-shopping-cart');
		var recalculareOrder = 0;
		var orderDetailSummarytodrag = jQuery("#orderDetailSummary");
		if (jQuery(objSource).length) {
			orderDetailSummarytodrag = objSource;
			recalculateOrder = 1;
		}
		if (cart.length)
		{
			if (orderDetailSummarytodrag.length ) {
				var divClone = orderDetailSummarytodrag.clone().offset({
					top: orderDetailSummarytodrag.offset().top,
					left: orderDetailSummarytodrag.offset().left
				})
					.css({
						'opacity': '0.5',
						'width': orderDetailSummarytodrag.width() + "px",
						'height': orderDetailSummarytodrag.height() + "px",
						'position': 'absolute',
						'z-index': '100',
						'overflow': 'hidden'
					})
					.appendTo(jQuery('body'))
					.animate({
						'top': cart.offset().top + 10,
						'left': cart.offset().left,
						'width': 0,
						'height': 0
					}, 1000, 'easeInOutExpo', function () {
						jQuery(this).remove();
						//cartModel();
						jQuery.ajax({
							cache: false,
							type: 'POST',
							url: bfi_variable.bfi_urlCheck + '?task=addToCart',
							data: 'hdnOrderData=' + jQuery("#hdnOrderDataCart").val() + "&recalculateOrder=" + recalculateOrder +  '&hdnBookingType=' + jQuery("#hdnBookingType").val(),
							success: function (data) {
	//							console.log(data);
								jQuery.unblockUI();

								var thisHtml = "<div>Add to cart</div>";
								jQuery(".bf-summary-body-resourcename").each(function () {
									var cuttTitle = $(this).find("strong").first();
									if (cuttTitle.length) {
										thisHtml += "<div>" + cuttTitle.html() + "</div>";
									}
								});
								jQuery("#bfimodalcart").find(".modal-body").first().html(thisHtml);
								jQuery("#bfimodalcart").modal({ backdrop: 'static' });
							}
						});
						//send data 

						//$("#LoginRegisterModel").html("");
						//$("#LoginRegisterModel").load(cartUrl);
						//$("#LoginRegisterModel").modal({ backdrop: 'static' });

					});

			}else{
				jQuery.ajax({
					cache: false,
					type: 'POST',
					url: bfi_variable.bfi_urlCheck + '?task=addToCart',
					data: 'hdnOrderData=' + jQuery("#hdnOrderDataCart").val() + "&recalculateOrder=" + recalculateOrder+  '&hdnBookingType=' + jQuery("#hdnBookingType").val(),
					success: function (data) {
		//							console.log(data);
						jQuery.unblockUI();
						var thisHtml = "<div>Add to cart</div>";
	//					jQuery(".bf-summary-body-resourcename").each(function () {
	//						var cuttTitle = $(this).find("strong").first();
	//						if (cuttTitle.length) {
	//							thisHtml += "<div>" + cuttTitle.html() + "</div>";
	//						}
	//					});
						jQuery("#bfimodalcart").find(".modal-body").first().html(thisHtml);
						jQuery("#bfimodalcart").modal({ backdrop: 'static' });
					}
				});
			}
		}


	}



	this.removeFromCart = function() {
		jQuery.ajax({
			cache: false,
			type: 'POST',
			url: removeFromCartUrl,
			beforeSend: function () {
				bookingfor.waitBlockUI();
				//blockui();
			},
			data: {
				cartOrderId: $(this).attr("data-cartorderid")
			},
			success: function (data) {
				jQuery("#LoginRegisterModel").html(data);
				//$("#LoginRegisterModel").modal({ backdrop: 'static' });
				jQuery.unblockUI();
			}
		});
	}





}





jQuery(document).ready(function() {
//      jQuery(".variationlabel").click(
//        function() {
//          var discountId = jQuery(this).attr('rel');
//          var hasRateplans = jQuery(this).attr('rel1');
//          if (jQuery.inArray(discountId, offersLoaded) === -1) {
//            bookingfor.getDiscountAjaxInformations(discountId, hasRateplans);
//            offersLoaded.push(discountId);
//          }
//          jQuery("#divoffers" + discountId).slideToggle("slow");
//        }
//      );
//      jQuery(".rateplanslabel").click(
//        function() {
//          var rateplanId = jQuery(this).attr('rel');
//          if (jQuery.inArray(rateplanId, rateplansLoaded) === -1) {
//            getRateplanAjaxInformations(rateplanId);
//            rateplansLoaded.push(rateplanId);
//          }
//          jQuery("#divrateplan" + rateplanId).slideToggle("slow");
//        }
//      );

	 jQuery("#my-account-tabs").tabs();

	   jQuery(".tabcontent:first").show();
            jQuery(".resourcetabmenu a").bind('click', function() {
              jQuery('.tabcontent').hide();
              var activeTab = jQuery(this).attr("rel");
              jQuery(".resourcetabmenu a").removeClass("selected");
              jQuery("#" + activeTab).fadeIn();
              jQuery('html, body').animate({
                scrollTop: jQuery(this).offset().top
              }, 1000);
              jQuery(this).addClass("selected");
              if (activeTab == 'mappa') {
              	if(!jQuery("#" + activeTab).hasClass('loaded')) {
              	  jQuery("#" + activeTab).addClass('loaded');
                  
                }
              }
              if (activeTab == 'foto') {
                var slider = jQuery("#resourcegallery").data('royalSlider');
                slider.updateSliderSize(true);
              }
              if (activeTab == 'planimetria') {
                var slider = jQuery("#resourcePlanimetrygallery").data('royalSlider');
                slider.updateSliderSize(true);
              }
              if (activeTab == 'video') {
                var slider = jQuery("#resourceVideogallery").data('royalSlider');
                slider.updateSliderSize(true);
              }
      });
      
     var start = jQuery('.checkincalendar').val();
     if (typeof start !== "undefined") {
		 date = jQuery.datepicker.parseDate('dd/mm/yy', start);
		 var dstart = new Date(date);
		 
		 var end = jQuery('.checkoutcalendar').val();
		 date = jQuery.datepicker.parseDate('dd/mm/yy', end);
		 var dend = new Date(date);
		 
		 var dendmin = new Date(dstart);
		 dendmin.setDate(dstart.getDate() + 1);

		   jQuery('.checkincalendar').datepicker({
			dateFormat : 'dd/mm/yy',
			defaultDate: dstart,
			onSelect: function(selectedDate) {
			  instance = jQuery('.checkincalendar').data("datepicker");
			  date = jQuery.datepicker.parseDate(
				  instance.settings.dateFormat ||
				  $.datepicker._defaults.dateFormat,
				  selectedDate, instance.settings);
			 var d = new Date(date);
			 d.setDate(d.getDate() + 1);
			 jQuery(".checkoutcalendar").datepicker("option", "minDate", d);
			}
		  });
		 
		 jQuery('.checkoutcalendar').datepicker({
			dateFormat : 'dd/mm/yy',
			defaultDate: dend,
			minDate: dendmin
		 });      
	 }
});      
     
if (typeof String.prototype.endsWith !== 'function') {
    String.prototype.endsWith = function(suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
}   

if (typeof jQuery.fn.serializeObject !== 'function') {
	jQuery.fn.serializeObject = function()
	{
	   var o = {};
	   var a = this.serializeArray();
	   jQuery.each(a, function() {
		   if (o[this.name]) {
			   if (!o[this.name].push) {
				   o[this.name] = [o[this.name]];
			   }
			   o[this.name].push(this.value || '');
		   } else {
			   o[this.name] = this.value || '';
		   }
	   });
	   return o;
	};
}   
