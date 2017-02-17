        function UpdateQuote(el,isService) //multiresource
        {
            var totalRooms = 0;
            var totalQuote = 0;
            var totalQuoteDiscount = 0;

            jQuery("tr[id^=data-id-]").each(function(index,obj){
                var ddlroom = jQuery(obj).find(".ddlrooms");
                var resId = jQuery(ddlroom).attr('id').split('-').pop();
                //debugger;
//                var txtTotalForNights = '@Res.MERCHANTS_VIEW_MERCHANTDETAILS_RESOURCE_TOTALDAYS';
                totalRooms += parseInt(ddlroom.val());

                var rate = jQuery(obj).find("span.com_bookingforconnector_merchantdetails-resourcelist-stay-total").attr("data-value");

                var discountRate = 0;
                if(jQuery(obj).find('td:eq(3)').find("span.com_bookingforconnector_merchantdetails-resource-stay-discount").length > 0)
                {
                    discountRate = jQuery(obj).find("span.com_bookingforconnector_merchantdetails-resource-stay-discount").attr("data-value");
                    totalQuoteDiscount += (parseInt(ddlroom.val()) * parseFloat(discountRate));
                }
                else
                    totalQuoteDiscount += (parseInt(ddlroom.val()) * parseFloat(rate));

                totalQuote += (parseInt(ddlroom.val()) * parseFloat(rate));

                if(discountRate > 0)
                {
                    jQuery(obj).find("span.com_bookingforconnector_merchantdetails-resource-stay-discount").show();
                    jQuery(obj).find("span.com_bookingforconnector_merchantdetails-resourcelist-stay-total").addClass("red-color");
                    //jQuery(".totalQuoteDiscount").html("&euro;&nbsp;" + bookingfor.number_format(totalQuoteDiscount, 2, '.', ''));
                    //jQuery(".totalQuoteDiscountWithServices").html("&euro;&nbsp;" + bookingfor.number_format(totalQuoteDiscount, 2, '.', ''));
                }
                else
                    jQuery(obj).find("span.com_bookingforconnector_merchantdetails-resourcelist-stay-total").removeClass("red-color");

                if(ddlroom.val() > 0)
                {
                    if(!jQuery(obj).find(".resourceimage").find("span").hasClass("com_bookingforconnector_resourcelist-resourcename-blue"))
                    {
                        jQuery(obj).find(".resourceimage").find("span").addClass("com_bookingforconnector_resourcelist-resourcename-blue");
                        jQuery(obj).find(".resourceimage").find("span").removeClass("com_bookingforconnector_resourcelist-resourcename-grey");
                        jQuery(obj).find(".fa-check-circle").show();
                    }
//                    for(var i=1; i<=ddlroom.val(); i++){
//                        jQuery("#services-room-" + i + '-' + resId).find('table').find('tr:eq(0)').find('th:eq(2)').html(txtTotalForNights.replace('{0}',@DateDiff));
//                    }
                    jQuery('#data-id-' + resId).attr('IsSelected','true');

                }
                else
                {
                    jQuery('#data-id-' + resId).attr('IsSelected','false');

                    jQuery(obj).find(".resourceimage").find("span").removeClass("com_bookingforconnector_resourcelist-resourcename-blue");
                    jQuery(obj).find(".resourceimage").find("span").addClass("com_bookingforconnector_resourcelist-resourcename-grey");
                    jQuery(obj).find(".fa-check-circle").hide();
                }
            });

            jQuery(".lblLodging span").html(totalRooms);
            jQuery(".totalQuote").html(bookingfor.number_format(totalQuote, 2, '.', '') );
            jQuery(".totalQuoteDiscount").html(bookingfor.number_format(totalQuoteDiscount, 2, '.', '') );

            if(totalRooms == 0)
                jQuery(".book-now").addClass("hidden");
            else
                jQuery(".book-now").removeClass("hidden");

            if(totalQuoteDiscount == 0 || totalQuoteDiscount == totalQuote){
                jQuery(".totalQuoteDiscount").addClass("hidden");
                jQuery(".totalQuote").removeClass("red-color");
            }

            else{
                jQuery(".totalQuoteDiscount").removeClass("hidden");
                jQuery(".totalQuote").addClass("red-color");
            }
        }

        function ChangeVariation(obj) //multiresource
        {
            UpdateQuote(); //set service price default value

            var showServices = false;
            var noResources = 0
            jQuery("[id^='ddlrooms-']").each(function(index,objDdl){
                var resId = jQuery(this).attr('id').split("-").pop();
                if(jQuery(this).val() > 0 && jQuery("#services-room-1-" + resId).length){
                    var title = jQuery("#services-room-1-" + resId).find('h5.titleform').first().html();
                    var firstResourceServices = jQuery("#services-room-1-" + resId)[0].outerHTML;

                    for (var i = 1; i <= jQuery(this).val(); i++) {
                        jQuery("#services-room-" + i + '-' + resId).find('h5.titleform').first().html(title + ' ' + i);
                        if(i!=jQuery(this).val() && jQuery("#services-room-" + i + '-' + resId).length ){

                            var nextservice = jQuery(jQuery(firstResourceServices));

                            nextservice.attr('id',"services-room-" + (i + 1) + '-' + resId);

                            //var totalextraselectid = nextservice.find("[class^='totalextrasselect-']").first().attr('class').split('-');
                            //nextservice.find("[class^='totalextrasselect-']").first().attr('class',totalextraselectid[0]+'-'+(i+1)+'-'+totalextraselectid[2]+'-'+totalextraselectid[3]);
                            nextservice.find("[class^='totalextrasselect-']").each(function(){
                                var totalextraselectid = jQuery(this).attr('class').split('-');
                                jQuery(this).attr('class',totalextraselectid[0]+'-'+(i+1)+'-'+totalextraselectid[2]+'-'+totalextraselectid[3]);
                            });

                            //var extrasselectclass = nextservice.find("[class^='extrasselect-']").first().attr('class').split('-');
                            //nextservice.find("[class^='extrasselect-']").first().attr('class',extrasselectclass[0]+'-'+(i+1)+'-'+extrasselectclass[2]);
                            nextservice.find("[class^='extrasselect-']").each(function(){
                                var extrasselectclass = jQuery(this).attr('class').split('-');
                                jQuery(this).attr('class',extrasselectclass[0]+'-'+(i+1)+'-'+extrasselectclass[2]);
                            });

                            //var extrasselectid = nextservice.find("[id^='extras-']").first().attr('id').split('-');
                            //nextservice.find("[id^='extras-']").first().attr('id',extrasselectid[0]+'-'+(i+1)+'-'+extrasselectid[2]+'-'+extrasselectid[3]);
                            //nextservice.find("[id^='extras-']").first().attr('name',extrasselectid[0]+'-'+(i+1)+'-'+extrasselectid[2]+'-'+extrasselectid[3]);
                            nextservice.find("[id^='extras-']").each(function(){
                                var extrasselectid = jQuery(this).attr('id').split('-');
                                jQuery(this).attr('id',extrasselectid[0]+'-'+(i+1)+'-'+extrasselectid[2]+'-'+extrasselectid[3]);
                                jQuery(this).attr('name',extrasselectid[0]+'-'+(i+1)+'-'+extrasselectid[2]+'-'+extrasselectid[3]);
                            });

                            var selectablenameclass = nextservice.find("[class^='name-selectableprice-']").first().attr('class').replace('name-selectableprice-1-', 'name-selectableprice-'+(i+1) + '-');
                            nextservice.find("[class^='name-selectableprice-']").first().attr('class',selectablenameclass);

                            // update id for all datapicker    //multiresource
                            var currCounterId = 0;
                            nextservice.find(".ChkAvailibilityFromDateTimePeriod,.ChkAvailibilityFromDateTimeSlot").each(function(){
                                jQuery(this).removeClass("hasDatepicker");
                                jQuery(this).next("button").remove();
                                currCounterId +=1;
                                jQuery(this).attr("id",  jQuery(this).attr("id") + '-' + (i + 1) + '-' +currCounterId);
                            });
                            nextservice.find("select.selectpickerTimeSlotRange,select.selectpickerTimePeriodStart,select.selectpickerTimePeriodEnd").each(function(){
                                //var newSelectpicker = jQuery(this).clone(true).append();
                                
								if(bookingfor.bsVersion ==3){
									jQuery(this).closest("td").append(jQuery(this).clone(true));
								}
                                jQuery(this).closest("div").remove();
                            });

                            nextservice.insertAfter("#services-room-" + i + '-' + resId);
                        }
                        else{

                        }
                        if(jQuery("#services-room-" + i + '-' + resId).length)
                        {
                            showServices = true;
                            jQuery("#services-room-" + i + '-' + resId).show();
                            if(noResources == 0)//show price and rooms text in last td
                            {
                                jQuery("#services-room-" + i + '-' + resId).find('table').find('tr:first').find('th:last').html('Booking');
                                jQuery("#services-room-" + i + '-' + resId).find('table').find('tr:eq(1)').find('td:eq(4)').find('div').show();
                            }
                            noResources++;



                        }
                    }
                }
            });

            if(showServices){
                jQuery(".table-resources").hide();
                jQuery(".hideonextra").hide();
                jQuery(".div-selectableprice").show();
                if (typeof daysToEnableTimeSlot !== 'undefined' && typeof strAlternativeDateToSearch !== 'undefined' && typeof initDatepickerTimeSlot !== 'undefined' && jQuery.isFunction(initDatepickerTimeSlot)) {
                    initDatepickerTimeSlot();
                }
                if (typeof daysToEnableTimePeriod !== 'undefined' && typeof initDatepickerTimePeriod !== 'undefined' && jQuery.isFunction(initDatepickerTimePeriod)) {
                    initDatepickerTimePeriod();
                }
            }
            else
            {
                BookNow();
            }
        }