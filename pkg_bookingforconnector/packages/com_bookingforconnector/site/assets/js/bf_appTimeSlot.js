
function initDatepickerTimeSlot() {
    jQuery(".ChkAvailibilityFromDateTimeSlot").datepicker({
        numberOfMonths: 1,
        defaultDate: "+0d",
        dateFormat: "dd/mm/yy",
        minDate: strAlternativeDateToSearch,
        maxDate: strEndDate,
        onSelect: function (date) {
            dateCalculatorChangedTimeSlot(jQuery, jQuery(this), date);
            printChangedDateTimeSlot(date, jQuery(this), jQuery(this).attr("data-id"));
        },
        showOn: "button",
        beforeShowDay: function (date) {
            return enableSpecificDatesTimeSlot(date, 1, daysToEnableTimeSlot[jQuery(this).attr("data-id")]);
        },
        buttonText: strbuttonTextTimeSlot,
        firstDay: 1
    });

    if(bookingfor.bsVersion ==3){
		jQuery('.selectpickerTimeSlotRange').selectpicker({
			container: 'body',
			template: {
				caret: '<i class="fa fa-clock-o"></i>'
			}
		});
	}

    jQuery('.selectpickerTimeSlotRange').on('change', function () {
        var selected = jQuery(this).find("option:selected").val();
        updateTotalSelectable(jQuery(this))
    });


}

    function dateCalculatorChangedTimeSlot($, obj, selectedDate) {
        instance = obj.data("datepicker");
        date = $.datepicker.parseDate(
                instance.settings.dateFormat ||
                $.datepicker._defaults.dateFormat,
                selectedDate, instance.settings);

        var fromDate = new Date(date);
        var intDate = bookingfor.convertDateToInt(fromDate);
        updateTimeSlotRange(intDate);
    }

    function updateTimeSlotRange(currDate) {
        var slotToEnableTimeSlot = [];
        var allSel = jQuery('.selectpickerTimeSlotRange')
        .find('option')
        .remove()
        .end();
        allSel.each(function () {
            var currSel = jQuery(this);
            var currProdId = currSel.data("resid");
            var copyarray = jQuery.extend(true, [], daysToEnableTimeSlot[currProdId]);
            slotToEnableTimeSlot = jQuery.grep(copyarray, function (ts) {
                return ts.StartDate == currDate;
            });
            slotToEnableTimeSlot.sort(function (a, b) { return a.TimeSlotStart - b.TimeSlotStart });
            jQuery.each(slotToEnableTimeSlot, function (i, currTimeSlot) {
                var tmpDate = new Date();
				tmpDate.setHours(0,0,0,0);
				var newTmpDateStart = bookingfor.dateAdd(tmpDate,"minute",Number(currTimeSlot.TimeSlotStart));
				var newTmpDateEnd = bookingfor.dateAdd(tmpDate,"minute",Number(currTimeSlot.TimeSlotEnd));
				var newValStart = bookingfor.pad(newTmpDateStart.getHours(),2) + ":" + bookingfor.pad(newTmpDateStart.getMinutes(),2);
				var newValEnd =  bookingfor.pad(newTmpDateEnd.getHours(),2) + ":" + bookingfor.pad(newTmpDateEnd.getMinutes(),2);   ;

//				var newValStart = moment.duration(Number(currTimeSlot.TimeSlotStart), "minutes").format("HH:mm", { forceLength: true, trim: false });
//              var newValEnd = moment.duration(Number(currTimeSlot.TimeSlotEnd), "minutes").format("HH:mm", { forceLength: true, trim: false });
                
				var currOpt = jQuery('<option>').text(newValStart + " - " + newValEnd).attr('value', currTimeSlot.ProductId);
                jQuery(currOpt).attr("data-startdate", currTimeSlot.StartDate);
                jQuery(currOpt).attr("data-timeslotstart", currTimeSlot.TimeSlotStart);
                jQuery(currOpt).attr("data-timeslotend", currTimeSlot.TimeSlotEnd);
                currSel.append(currOpt);
                currTimeSlotDisp[currTimeSlot.ProductId] = currTimeSlot.Availability;
            });
            if(bookingfor.bsVersion ==3){
				currSel.selectpicker('refresh');
			}
            updateTotalSelectable(currSel)
        }
        );


    }

    function updateTotalSelectable(currEl) {
        var currTr = currEl.closest("tr");
        var id = currEl.data("resid");
        var currSel = currTr.find(".ddlrooms").first();
        var currentSelection = currSel.val();
        //debugger;
        jQuery(currSel)
        .find('option')
        .remove()
        .end();
        var currentTimeOpt = currTr.find(".selectpickerTimeSlotRange option:selected");
        var currentTime = currentTimeOpt.val();
        var correction = 1;
        if (jQuery(currSel).is('[class^="extrasselect"]')) {
           currentSelection = parseInt(currentSelection.split(":")[1]);
           for (var i = 0; i <= currTimeSlotDisp[currentTime]; i++) {
                var opt = $('<option>').text(i).attr('value', id + ":" + i + ":::" + currentTime + ":" + currentTimeOpt.attr("data-timeslotstart") + ":" + currentTimeOpt.attr("data-timeslotend") + ":" + currentTimeOpt.attr("data-startdate"));
                if (currentSelection == i) { opt.attr("selected", "selected"); }
                currSel.append(opt);
//               currSel.append(jQuery('<option>').text(i).attr('value', id + ":" + i + ":::" + currentTime + ":" + currentTimeOpt.attr("data-timeslotstart") + ":" + currentTimeOpt.attr("data-timeslotend") + ":" + currentTimeOpt.attr("data-startdate")));
            }
        } else {
            for (var i = 0; i < currTimeSlotDisp[currentTime]; i++) {
                var opt = jQuery('<option>').text(i).attr('value', i);
                if (currentSelection == i) { opt.attr("selected", "selected"); }
                currSel.append(opt);
            }
        }

        //var currSel = jQuery('#ddlrooms-'+id)
        //.find('option')
        //.remove()
        //.end();
        //var currentTime = jQuery(".selectpickerTimeSlotRange-"+id).find("option:selected").val();

        UpdateQuote(currSel);
    }

    function printChangedDateTimeSlot(date, elem, currId) {
        var checkindate = jQuery(elem).val();

        var d1 = checkindate.split("/");
        var from = new Date(d1[2], d1[1] - 1, d1[0]);
        day1 = ('0' + from.getDate()).slice(-2),
        month1 = from.toLocaleString("en", { month: "short" }),
        year1 = from.getFullYear(),
        weekday1 = from.toLocaleString("en", { weekday: "short" });
        jQuery(elem).next().find('.day span').html(day1);
        jQuery(elem).next().find('.monthyear p').html(weekday1 + "<br />" + month1 + " " + year1);
        //    jQuery('.timeSlotli').find('.day span').html(day1);
        //jQuery('.timeSlotli').find('.monthyear p').html(weekday1 + "<br />" + month1 + " " + year1);
    }

    function enableSpecificDatesTimeSlot(date, offset, enableDays) {
        var month = date.getMonth() + 1;
        var day = date.getDate();
        var year = date.getFullYear();
        var copyarray = jQuery.extend(true, [], enableDays);
        var listDays = jQuery.map(copyarray, function (n, i) {
            return (n.StartDate);
        });
        listDays = jQuery.unique(listDays);
        var listDaysunique = listDays.filter(function (elem, index, self) {
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