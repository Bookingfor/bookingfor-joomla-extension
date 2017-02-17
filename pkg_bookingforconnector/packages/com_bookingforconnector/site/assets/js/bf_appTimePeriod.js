

function initDatepickerTimePeriod() {
	var evntSelect = "change";
    if(bookingfor.bsVersion ==3){
		jQuery('.selectpickerTimePeriodStart').selectpicker({
			container: 'body',
			template: {
				caret: '<i class="fa fa-clock-o"></i>'
			}
		});
		jQuery('.selectpickerTimePeriodEnd').selectpicker({
			container: 'body',
			template: {
				caret: '<i class="fa fa-clock-o"></i>'
			}
		});
		evntSelect = "shown.bs.select";
	}
    jQuery('.selectpickerTimePeriodStart').on('change', function(){
        var optSelected = jQuery(this).find("option:selected");
        var selected = jQuery(this).attr("data-resid");
        var selIndx  = jQuery(this).prop('selectedIndex');
        var minstay = Number(jQuery(optSelected).attr("data-minstay"));
        var maxIndex = selIndx + Number(jQuery(optSelected).attr("data-maxstay"));
        selIndx += minstay - 1;
        var currTr = jQuery(this).closest("tr");
        var selectpickerTimePeriodEnd = currTr.find('.selectpickerTimePeriodEnd').first();

        currTr.find('.selectpickerTimePeriodEnd option:lt(' + selIndx + ')').prop('disabled', true)
        currTr.find('.selectpickerTimePeriodEnd option:gt(' + selIndx + ')').prop('disabled', false);
        currTr.find('.selectpickerTimePeriodEnd option:gt(' + (maxIndex - 1) + ')').prop('disabled', true);
        //currTr.find('.selectpickerTimePeriodEnd').selectpicker('refresh');
        //var currFromDate =jQuery("#ChkAvailibilityFromDateTimePeriod_"+selected);
        //var timeLength = Number(currFromDate.attr("data-timeLength"));
        //selIndx = selIndx + minstay -1;
        //if(timeLength>1){
        //    selIndx = selIndx * timeLength;
        //}
//        console.log("start: " + selected);
        var selEnd = selectpickerTimePeriodEnd.find('option').eq(selIndx).first();
        if(selEnd.length){
            selEnd.prop('selected', true);
            if(bookingfor.bsVersion ==3){
				selectpickerTimePeriodEnd.selectpicker('refresh');
			}
            selectpickerTimePeriodEnd.trigger('change');
            selectpickerTimePeriodEnd.focus();
        }


    });
    
    var previous_selectedIndex;

    jQuery('.selectpickerTimePeriodEnd').on(evntSelect, function(e) {
        previous_selectedIndex = jQuery(this).prop('selectedIndex');
    }).change(function() {
        var selected = jQuery(this).find("option:selected").val();
        var currTr = jQuery(this).closest("tr");

        var optSelected = currTr.find('.selectpickerTimePeriodStart option:selected');
        var maxstay = Number(jQuery(optSelected).attr("data-maxstay"));
        var PeriodStart_selectedIndex = currTr.find('.selectpickerTimePeriodStart').first().prop('selectedIndex');
        var PeriodEnd_selectedIndex = jQuery(this).prop('selectedIndex');
        if((PeriodEnd_selectedIndex - PeriodStart_selectedIndex) > maxstay){
            currTr.find('.selectpickerTimePeriodEnd option:eq(' + previous_selectedIndex + ')').first().prop('selected', true)
            if(bookingfor.bsVersion ==3){
				currTr.find('.selectpickerTimePeriodEnd').selectpicker('refresh')
			}
            return;
        }
//        console.log("end: " + selected);
        if (jQuery(this).prop('selectedIndex') < PeriodStart_selectedIndex) {
            currTr.find('.selectpickerTimePeriodStart').trigger('change');
            return;
        }
        previous_selectedIndex = jQuery(this).prop('selectedIndex');
        updateTotalSelectablePeriod(jQuery(this))
    });

    jQuery(".ChkAvailibilityFromDateTimePeriod").datepicker({
        numberOfMonths: 1,
        defaultDate: "+0d",
        dateFormat: "dd/mm/yy",
        minDate: strAlternativeDateToSearch,
        maxDate: strEndDate,
        onSelect: function(date) {
            dateTimePeriodChanged($, jQuery(this), date, jQuery(this).attr("data-id"));
            printChangedDateTimePeriod(date, jQuery(this), jQuery(this).attr("data-id"));
        },
        showOn: "button",
        beforeShowDay: function(date) {
            return enableSpecificDatesTimePeriod(date, 1, daysToEnableTimePeriod[jQuery(this).attr("data-id")]);
        },
        buttonText: strbuttonTextTimePeriod,
        firstDay: 1
    });
}

function enableSpecificDatesTimePeriod(date, offset, enableDays) {
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var year = date.getFullYear();
    var copyarray = jQuery.extend(true, [], enableDays);
    var listDays = jQuery.map(copyarray,function( n, i ) {
        return ( n.StartDate );
    });
    listDays = jQuery.unique( listDays );
    var listDaysunique = listDays.filter(function(elem, index, self) {
        return index == self.indexOf(elem);
    })
    for (var i = 0; i < offset; i++)
        listDaysunique.pop();
    var datereformat = year + '' + bookingfor.pad(month,2) + '' + bookingfor.pad(day,2);
    if (jQuery.inArray(Number(datereformat), listDaysunique) != -1) {
        return [true, 'greenDay'];
    }
    return [false, 'redDay'];
}

function printChangedDateTimePeriod(date, elem, currId) {
    var checkindate = jQuery(elem).val();
    var d1 = checkindate.split("/");
    var from = new Date(d1[2], d1[1] - 1, d1[0]);
    day1 = ('0' + from.getDate()).slice(-2),
    month1 = from.toLocaleString("en", { month: "short" }),
    year1 = from.getFullYear(),
    weekday1 = from.toLocaleString("en", { weekday: "short" });
    jQuery(elem).next().find('.day span').html(day1);
    jQuery(elem).next().find('.monthyear p').html(weekday1 + "<br />" + month1 + " " + year1);
}


function dateTimePeriodChanged($, obj, selectedDate, currProdId){
    instance = obj.data("datepicker");
    date = jQuery.datepicker.parseDate(
            instance.settings.dateFormat ||
            jQuery.datepicker._defaults.dateFormat,
            selectedDate, instance.settings);

    var fromDate = new Date(date);
    var intDate = bookingfor.convertDateToInt(fromDate);
    updateTimePeriodRange(intDate, currProdId, obj);
}

function updateTimePeriodRange(currDate,currProdId,obj){
    // pulizia select
    // recupero dati via ajax
    // ricalcolo prezzi via ajax

    var currTr = jQuery(obj).closest("tr");

    //var slotToEnableTimeSlot = [];
    var curSelStart = currTr.find('.selectpickerTimePeriodStart').first()
    .find('option')
    .remove()
    .end();
    var curSelEnd = currTr.find('.selectpickerTimePeriodEnd').first()
    .find('option')
    .remove()
    .end();
    var jqxhr = jQuery.ajax({
        url: urlGetListCheckInDayPerTimes,
        type: "GET",
        dataType: "json",
        data: { resourceId: currProdId, fromDate: currDate }
    }).done(updateOptTimePeriodRange(curSelStart, curSelEnd));

}

var updateOptTimePeriodRange = function (curSelStart, curSelEnd) {
    return function (result, textStatus, jqXHR) {
        if (result) {
            if (result.length > 0) {
//                debugger;

                jQuery.each(result, function (i, currTimeSlot) {
//                   console.log(currTimeSlot.TimeMinStart)
//                    var newValStart = moment(bookingfor.pad(currTimeSlot.TimeMinStart, 6), "HHmmss").format("HH:mm", { forceLength: true, trim: false });
//                    var newValEnd = moment(bookingfor.pad(currTimeSlot.TimeMinEnd, 6), "HHmmss").format("HH:mm", { forceLength: true, trim: false });
//					var tmpDate = new Date(1,1,1);
//					tmpDate.setHours(0,0,0,0);
//
//					var newValStart = bookingfor.dateAdd(tmpDate,"minute",Number(currTimeSlot.TimeMinStart));
//					var newValEnd = bookingfor.dateAdd(tmpDate,"minute",Number(currTimeSlot.TimeMinStart));

					var newValStart = new Date(1,1,1);
					var tmpCorrTimeStart = bookingfor.pad(currTimeSlot.TimeMinStart, 6);
					newValStart.setHours(Number(tmpCorrTimeStart.substring(0, 2)),Number(tmpCorrTimeStart.substring(2, 4)),0,0);
					var newValEnd = new Date(1,1,1);
					var tmpCorrTimeEnd = bookingfor.pad(currTimeSlot.TimeMinEnd, 6);
					newValEnd.setHours(Number(tmpCorrTimeEnd.substring(0, 2)),Number(tmpCorrTimeEnd.substring(2, 4)),0,0);

                    var currOptStart = jQuery('<option>').text(bookingfor.pad(newValStart.getHours(), 2) + ":" + bookingfor.pad(newValStart.getMinutes(), 2)).attr('value', currTimeSlot.ProductId);
                    jQuery(currOptStart).attr("data-TimeMinStart", currTimeSlot.TimeMinStart);
                    jQuery(currOptStart).attr("data-availability", currTimeSlot.Availability);
                    jQuery(currOptStart).attr("data-minstay", currTimeSlot.MinStay);
                    jQuery(currOptStart).attr("data-maxstay", currTimeSlot.MaxStay);

                    var currOptEnd = jQuery('<option>').text(bookingfor.pad(newValEnd.getHours(), 2) + ":" + bookingfor.pad(newValEnd.getMinutes(), 2)).attr('value', currTimeSlot.ProductId);
                    jQuery(currOptEnd).attr("data-TimeMinEnd", currTimeSlot.TimeMinEnd);
                    jQuery(currOptStart).attr("class", "hourdenabled");
                    jQuery(currOptEnd).attr("class", "hourdenabled");
                    jQuery(currOptEnd).attr("data-availability", currTimeSlot.Availability);

                    if (currTimeSlot.Availability == 0) {
                        jQuery(currOptStart).attr("disabled", "disabled");
                        jQuery(currOptEnd).attr("disabled", "disabled");
                        jQuery(currOptStart).attr("class", "hourdisabled");
                        jQuery(currOptEnd).attr("class", "hourdisabled");
                    }
                    curSelStart.append(currOptStart);
                    curSelEnd.append(currOptEnd);

                });
                //console.log(jQuery(curSelStart).attr("data-id"));

                if(bookingfor.bsVersion ==3){
					jQuery(curSelStart).selectpicker('refresh');
					jQuery(curSelEnd).selectpicker('refresh');
				}

                jQuery(curSelStart).trigger('change');
                //updateTotalSelectablePeriod(currProdId);

            }
        }
    };
};

function updateTotalSelectablePeriod(currEl){
//    console.log("updateTotalSelectablePeriod" + currEl.data("resid"))
    var currTr = currEl.closest("tr");
    var id = currEl.data("resid");
    var currSel = currTr.find(".ddlrooms").first();
   
    //var currSel = jQuery('#ddlrooms-'+id)
    //debugger;
    jQuery(currSel)
    .find('option')
    .remove()
    .end();
    var isSelectable = true;
    var maxSelectable = 0

    var selectStart = currTr.find(".selectpickerTimePeriodStart").first();
    var selectEnd = currTr.find(".selectpickerTimePeriodEnd").first();
    for (var i = selectStart.prop('selectedIndex'); i <= selectEnd.prop('selectedIndex'); i++) {
        var currOption = currTr.find('.selectpickerTimePeriodStart option').eq(i);
        var currAvailability =  Number(jQuery(currOption).attr('data-availability') );
        if(currAvailability==0){
            isSelectable = false;
            break;
        }
        if(currAvailability<maxSelectable || i==selectStart.prop('selectedIndex')){
            maxSelectable = currAvailability;
        }
    }
    if(isSelectable){
        currSel.show();
        jQuery("#btnBookNow").show();

        if (jQuery(currSel).is('[class^="extrasselect"]')) {
            var currFromDate = currTr.find(".ChkAvailibilityFromDateTimePeriod").first();
            
//			var mcurrFromDate = moment(currFromDate.val(), "DD/MM/YYYY").format("YYYYMMDD", { forceLength: true, trim: false });
			var mcurrFromDate = bookingfor.convertDateToInt(jQuery(currFromDate).datepicker( "getDate" ));

            
			var currentTimeStart = selectStart.find("option:selected");
            var currentTimeEnd = selectEnd.find("option:selected");
            
//			var newValStart = moment(pad(currentTimeStart.attr("data-TimeMinStart"), 6), "HHmmss");
//            var newValEnd = moment(pad(currentTimeEnd.attr("data-TimeMinEnd"), 6), "HHmmss");
			var tmpDate = new Date();
			tmpDate.setHours(0,0,0,0);
			var newValStart = bookingfor.dateAdd(tmpDate,"minute",Number(currentTimeStart.attr("data-TimeMinStart")));
			var newValEnd = bookingfor.dateAdd(tmpDate,"minute",Number(currentTimeEnd.attr("data-TimeMinEnd")));

//			var duration = moment.duration(newValEnd.diff(newValStart));
			var diffMs = (newValEnd - newValStart);
			var duration =  Math.floor((diffMs/1000)/60);


            for (var i = 0; i <= maxSelectable; i++) {
                currSel.append(jQuery('<option>').text(i).attr('value', id + ":" + i + ":" + mcurrFromDate + bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6) + ":" + duration + "::::"));
                //currSel.append(jQuery('<option>').text(i).attr('value', i));
            }
        } else {
            for (var i = carttypeCorrector; i <= maxSelectable; i++) {
                currSel.append(jQuery('<option>').text(i).attr('value', i));
            }
        }


        quoteCalculatorPeriodChanged(currSel);

    }else{
        currSel.hide();
        jQuery("#btnBookNow").hide();
    }

    //UpdateQuote(currSel);
}
function quoteCalculatorPeriodChanged(currEl)
{
    var currTr = currEl.closest("tr");
    var id = currEl.data("resid");
    //var currSel = currTr.find(".ddlrooms").first();

    //debugger;
    var currentTimeStart = currTr.find(".selectpickerTimePeriodStart option:selected").val();
    var currentTimeEnd= currTr.find(".selectpickerTimePeriodEnd option:selected").val();
    //jQuery('.totalextrasselect-'+ ddlid[1]+'-'+currSelPrice[0]+'-'+resId).block({message: ''});
    getcompleterateplansstaybyidPerTime(currEl);
}

function getcompleterateplansstaybyidPerTime(currEl) {
    //debugger;
    var currTr = currEl.closest("tr");
    var resourceId = currEl.data("resid");
    //resourceId = resId;
    //el = jQuery('#data-id-' + resId);
    var currFromDate =currTr.find(".ChkAvailibilityFromDateTimePeriod").first();

    var currentTimeStart = currTr.find(".selectpickerTimePeriodStart option:selected");
    var currentTimeEnd= currTr.find(".selectpickerTimePeriodEnd option:selected");

    jQuery(currEl).block({
        message: '<i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>',
        css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B', width: '80%'},
        overlayCSS: {backgroundColor: '#1D668B', opacity: .7}
    });

//		var tmpDate = new Date();
//		tmpDate.setHours(0,0,0,0);
//		var newValStart = bookingfor.dateAdd(tmpDate,"minute",Number(currentTimeStart.attr("data-TimeMinStart")));
//		var newValEnd = bookingfor.dateAdd(tmpDate,"minute",Number(currentTimeEnd.attr("data-TimeMinEnd")));

		var newValStart = new Date(1,1,1);
		var tmpCorrTimeStart = bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6);
		newValStart.setHours(Number(tmpCorrTimeStart.substring(0, 2)),Number(tmpCorrTimeStart.substring(2, 4)),0,0);
		var newValEnd = new Date(1,1,1);
		var tmpCorrTimeEnd = bookingfor.pad(currentTimeEnd.attr("data-TimeMinEnd"), 6);
		newValEnd.setHours(Number(tmpCorrTimeEnd.substring(0, 2)),Number(tmpCorrTimeEnd.substring(2, 4)),0,0);

		var diffMs = (newValEnd - newValStart);
		var duration =  Math.floor((diffMs/1000)/60);

//		var newValStart = moment(bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6), "HHmmss");
//		var newValEnd = moment(bookingfor.pad(currentTimeEnd.attr("data-TimeMinEnd"), 6), "HHmmss");

//		var duration = moment.duration(newValEnd.diff(newValStart)).asMinutes();
//		var checkInTime = moment(currFromDate.val(), "DD/MM/YYYY").format("YYYYMMDD", { forceLength: true, trim: false }) + bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6);
		var mcurrFromDate = bookingfor.convertDateToInt(jQuery(currFromDate).datepicker( "getDate" ));
		var checkInTime = mcurrFromDate + bookingfor.pad(currentTimeStart.attr("data-TimeMinStart"), 6);


		var searchModel = jQuery('#calculatorForm').serializeObject();
		var dataarray = jQuery('#calculatorForm').serializeArray();
		dataarray.push({name: 'id', value: resourceId});
		dataarray.push({name: 'resourceId', value: resourceId});
//		dataarray.push({name: 'pricetype', value:  accomodation.RatePlanId});
//		dataarray.push({name: 'rateplanid', value: accomodation.RatePlanId});
		dataarray.push({name: 'timeMinStart', value: currentTimeStart.attr("data-TimeMinStart")});
		dataarray.push({name: 'timeMinEnd', value: currentTimeEnd.attr("data-TimeMinEnd")});
		dataarray.push({name: 'CheckInTime', value: checkInTime});

//		dataarray.push({name: 'selectableprices', value: accomodation.ExtraServices.join("|")});
		dataarray.push({name: 'availabilitytype', value: currFromDate.attr("data-availabilityType")});
		dataarray.push({name: 'duration', value: duration});
		dataarray.push({name: 'searchModel', value: searchModel});

    var jqxhr = jQuery.ajax({
        url: urlGetCompleteRatePlansStay,
        type: "POST",
        dataType: "json",
		data : dataarray
//        data: {
//            id: resourceId,
//            fromDate: currFromDate.val() ,
//            timeMinStart: currentTimeStart.attr("data-TimeMinStart"),
//            timeMinEnd: currentTimeEnd.attr("data-TimeMinEnd"),
//            productAvailabilityType: currFromDate.attr("data-availabilityType"),
//            searchModel : searchModel
//        }
    });

    jqxhr.done(function(result, textStatus, jqXHR)
    {
        if (result) {
            if(result.length > 0)
            {
                //debugger
                currStay = result[0].SuggestedStay;
                //var CalculatedPrices = JSON.parse(result[0].CalculatedPricesString);
                //var showPrice = false;

                //var totalPrice = parseFloat(jQuery("#dvTotal-" + resId + " .totalQuote").html().replace("€&nbsp;",""));
                //var totalDiscount = jQuery("#dvTotal-" + resId + " .totalQuoteDiscount").length > 0 ? parseFloat(jQuery("#dvTotal-" + resId + " .totalQuoteDiscount").html().replace("€&nbsp;","")) : 0;
                //var totalRooms = parseInt(jQuery("#dvTotal-" + resId + " .lblLodging span").html());

                jQuery("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resource-stay-discount").html(bookingfor.number_format(currStay.TotalPrice, 2, '.', ''));
                jQuery("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resource-stay-discount").attr("data-value",currStay.TotalPrice);
                jQuery("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").html(bookingfor.number_format(currStay.DiscountedPrice, 2, '.', ''));
                jQuery("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").attr("data-value",currStay.DiscountedPrice);

                jQuery("#data-id-" + resourceId).find(".variationlabel").show();
                jQuery("#data-id-" + resourceId).find(".variationlabel").attr("rel", currStay.SimpleDiscountIds);
                if (currStay.DiscountedPrice >= currStay.TotalPrice) {
                    jQuery("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").removeClass("red-color");
                    jQuery("#data-id-" + resourceId).find(".variationlabel").hide();
                } else {
                    jQuery("#data-id-" + resourceId).find(".com_bookingforconnector_merchantdetails-resourcelist-stay-total").addClass("red-color");
                    jQuery("#data-id-" + resourceId).find(".variationlabel_percent").html(currStay.VariationPercent);
                }
                var currSel = jQuery('#ddlrooms-'+resourceId);
                UpdateQuote(currSel);




            }
        }
    });


    jqxhr.always(function() {
        jQuery(currEl).unblock();
    });
}