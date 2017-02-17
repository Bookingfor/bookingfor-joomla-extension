function getData(urlCheck, query, elem, name, act) {
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
}
function getXmlLanguage(value, cultureCode, defaultcultureCode) {
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
}

function make_slug( str )
{
    str = str.toLowerCase();
    str = str.replace(/\&+/g, 'and');
    str = str.replace(/[^a-z0-9]+/g, '-');
    str = str.replace(/^-|-$/g, '');
    return str;
}

if (typeof String.prototype.endsWith !== 'function') {
    String.prototype.endsWith = function(suffix) {
        return this.indexOf(suffix, this.length - suffix.length) !== -1;
    };
}
function waitBlockUI(msg1 ,msg2, img1){
	jQuery.blockUI({
		message: '<h1 style="font-size: 15px;">'+msg1+'<br />'+msg2+'</h1><br /><i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>', 
		css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B'},
//		overlayCSS: {backgroundColor: '#1D668B', opacity: .7}  
		overlayCSS: {backgroundColor: '#1D668B', opacity: 0}  
		});
	}

function waitBlock(msg1 ,msg2, obj){
	obj.block({
		message: '<h1 style="font-size: 15px;">'+msg1+'<br />'+msg2+'</h1><br /><i class="fa fa-spinner fa-spin fa-3x fa-fw margin-bottom"></i><span class="sr-only">Loading...</span>', 
		css: {border: '2px solid #1D668B', padding: '20px', backgroundColor: '#fff', '-webkit-border-radius': '10px', '-moz-border-radius': '10px', color: '#1D668B', width: '80%'},
//		overlayCSS: {backgroundColor: '#1D668B', opacity: .7}  
		overlayCSS: {backgroundColor: '#1D668B', opacity: 0}  
		});
	}

function toggleDetails(anchor,openclass,openparent) {
	var c = openclass;
	var a = jQuery(anchor);
	var p = a.parents(openparent).first();
	if (p.hasClass(c))
		p.removeClass(c);
	else
		p.addClass(c);
}

function nl2br (str, is_xhtml) {   
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';    
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
}

function nomore1br (str) {   
    return (str + '').replace(new RegExp('(\n){2,}', 'gim') , '\n');
}

function number_format(number, decimals, dec_point, thousands_sep) {
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
}

function updateQueryStringParameter(uri, key, value) {
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  var separator = uri.indexOf('?') !== -1 ? "&" : "?";
  if (uri.match(re)) {
    return uri.replace(re, '$1' + key + "=" + value + '$2');
  }
  else {
    return uri + separator + key + "=" + value;
  }
}