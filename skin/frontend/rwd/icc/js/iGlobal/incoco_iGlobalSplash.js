//
// Begin jquery jsonp plugin
//
/*
 * jQuery JSONP Core Plugin 2.4.0 (2012-08-21)
 *
 * https://github.com/jaubourg/jquery-jsonp
 *
 * Copyright (c) 2012 Julian Aubourg
 *
 * This document is licensed as free software under the terms of the
 * MIT License: http://www.opensource.org/licenses/mit-license.php
 */
( function( $ ) {

    // ###################### UTILITIES ##

    // Noop
    function noop() {
    }

    // Generic callback
    function genericCallback( data ) {
        lastValue = [ data ];
    }

    // Call if defined
    function callIfDefined( method , object , parameters ) {
        return method && method.apply( object.context || object , parameters );
    }

    // Give joining character given url
    function qMarkOrAmp( url ) {
        return /\?/ .test( url ) ? "&" : "?";
    }

    var // String constants (for better minification)
        STR_ASYNC = "async",
        STR_CHARSET = "charset",
        STR_EMPTY = "",
        STR_ERROR = "error",
        STR_INSERT_BEFORE = "insertBefore",
        STR_JQUERY_JSONP = "_jqjsp",
        STR_ON = "on",
        STR_ON_CLICK = STR_ON + "click",
        STR_ON_ERROR = STR_ON + STR_ERROR,
        STR_ON_LOAD = STR_ON + "load",
        STR_ON_READY_STATE_CHANGE = STR_ON + "readystatechange",
        STR_READY_STATE = "readyState",
        STR_REMOVE_CHILD = "removeChild",
        STR_SCRIPT_TAG = "<script>",
        STR_SUCCESS = "success",
        STR_TIMEOUT = "timeout",

    // Window
        win = window,
    // Deferred
        Deferred = $.Deferred,
    // Head element
        head = $( "head" )[ 0 ] || document.documentElement,
    // Page cache
        pageCache = {},
    // Counter
        count = 0,
    // Last returned value
        lastValue,

    // ###################### DEFAULT OPTIONS ##
        xOptionsDefaults = {
            //beforeSend: undefined,
            //cache: false,
            callback: STR_JQUERY_JSONP,
            //callbackParameter: undefined,
            //charset: undefined,
            //complete: undefined,
            //context: undefined,
            //data: "",
            //dataFilter: undefined,
            //error: undefined,
            //pageCache: false,
            //success: undefined,
            //timeout: 0,
            //traditional: false,
            url: location.href
        },

    // opera demands sniffing :/
        opera = win.opera,

    // IE < 10
        oldIE = !!$( "<div>" ).html( "<!--[if IE]><i><![endif]-->" ).find("i").length;

    // ###################### MAIN FUNCTION ##
    function jsonp( xOptions ) {

        // Build data with default
        xOptions = $.extend( {} , xOptionsDefaults , xOptions );

        // References to xOptions members (for better minification)
        var successCallback = xOptions.success,
            errorCallback = xOptions.error,
            completeCallback = xOptions.complete,
            dataFilter = xOptions.dataFilter,
            callbackParameter = xOptions.callbackParameter,
            successCallbackName = xOptions.callback,
            cacheFlag = xOptions.cache,
            pageCacheFlag = xOptions.pageCache,
            charset = xOptions.charset,
            url = xOptions.url,
            data = xOptions.data,
            timeout = xOptions.timeout,
            pageCached,

        // Abort/done flag
            done = 0,

        // Life-cycle functions
            cleanUp = noop,

        // Support vars
            supportOnload,
            supportOnreadystatechange,

        // Request execution vars
            firstChild,
            script,
            scriptAfter,
            timeoutTimer;

        // If we have Deferreds:
        // - substitute callbacks
        // - promote xOptions to a promise
        Deferred && Deferred(function( defer ) {
            defer.done( successCallback ).fail( errorCallback );
            successCallback = defer.resolve;
            errorCallback = defer.reject;
        }).promise( xOptions );

        // Create the abort method
        xOptions.abort = function() {
            !( done++ ) && cleanUp();
        };

        // Call beforeSend if provided (early abort if false returned)
        if ( callIfDefined( xOptions.beforeSend , xOptions , [ xOptions ] ) === !1 || done ) {
            return xOptions;
        }

        // Control entries
        url = url || STR_EMPTY;
        data = data ? ( (typeof data) == "string" ? data : $.param( data , xOptions.traditional ) ) : STR_EMPTY;

        // Build final url
        url += data ? ( qMarkOrAmp( url ) + data ) : STR_EMPTY;

        // Add callback parameter if provided as option
        callbackParameter && ( url += qMarkOrAmp( url ) + encodeURIComponent( callbackParameter ) + "=?" );

        // Add anticache parameter if needed
        !cacheFlag && !pageCacheFlag && ( url += qMarkOrAmp( url ) + "_" + ( new Date() ).getTime() + "=" );

        // Replace last ? by callback parameter
        url = url.replace( /=\?(&|$)/ , "=" + successCallbackName + "$1" );

        // Success notifier
        function notifySuccess( json ) {

            if ( !( done++ ) ) {

                cleanUp();
                // Pagecache if needed
                pageCacheFlag && ( pageCache [ url ] = { s: [ json ] } );
                // Apply the data filter if provided
                dataFilter && ( json = dataFilter.apply( xOptions , [ json ] ) );
                // Call success then complete
                callIfDefined( successCallback , xOptions , [ json , STR_SUCCESS, xOptions ] );
                callIfDefined( completeCallback , xOptions , [ xOptions , STR_SUCCESS ] );

            }
        }

        // Error notifier
        function notifyError( type ) {

            if ( !( done++ ) ) {

                // Clean up
                cleanUp();
                // If pure error (not timeout), cache if needed
                pageCacheFlag && type != STR_TIMEOUT && ( pageCache[ url ] = type );
                // Call error then complete
                callIfDefined( errorCallback , xOptions , [ xOptions , type ] );
                callIfDefined( completeCallback , xOptions , [ xOptions , type ] );

            }
        }

        // Check page cache
        if ( pageCacheFlag && ( pageCached = pageCache[ url ] ) ) {

            pageCached.s ? notifySuccess( pageCached.s[ 0 ] ) : notifyError( pageCached );

        } else {

            // Install the generic callback
            // (BEWARE: global namespace pollution ahoy)
            win[ successCallbackName ] = genericCallback;

            // Create the script tag
            script = $( STR_SCRIPT_TAG )[ 0 ];
            script.id = STR_JQUERY_JSONP + count++;

            // Set charset if provided
            if ( charset ) {
                script[ STR_CHARSET ] = charset;
            }

            opera && opera.version() < 11.60 ?
                // onerror is not supported: do not set as async and assume in-order execution.
                // Add a trailing script to emulate the event
                ( ( scriptAfter = $( STR_SCRIPT_TAG )[ 0 ] ).text = "document.getElementById('" + script.id + "')." + STR_ON_ERROR + "()" )
                :
                // onerror is supported: set the script as async to avoid requests blocking each others
                ( script[ STR_ASYNC ] = STR_ASYNC )

            ;

            // Internet Explorer: event/htmlFor trick
            if ( oldIE ) {
                script.htmlFor = script.id;
                script.event = STR_ON_CLICK;
            }

            // Attached event handlers
            script[ STR_ON_LOAD ] = script[ STR_ON_ERROR ] = script[ STR_ON_READY_STATE_CHANGE ] = function ( result ) {

                // Test readyState if it exists
                if ( !script[ STR_READY_STATE ] || !/i/.test( script[ STR_READY_STATE ] ) ) {

                    try {

                        script[ STR_ON_CLICK ] && script[ STR_ON_CLICK ]();

                    } catch( _ ) {}

                    result = lastValue;
                    lastValue = 0;
                    result ? notifySuccess( result[ 0 ] ) : notifyError( STR_ERROR );

                }
            };

            // Set source
            script.src = url;

            // Re-declare cleanUp function
            cleanUp = function( i ) {
                timeoutTimer && clearTimeout( timeoutTimer );
                script[ STR_ON_READY_STATE_CHANGE ] = script[ STR_ON_LOAD ] = script[ STR_ON_ERROR ] = null;
                head[ STR_REMOVE_CHILD ]( script );
                scriptAfter && head[ STR_REMOVE_CHILD ]( scriptAfter );
            };

            // Append main script
            head[ STR_INSERT_BEFORE ]( script , ( firstChild = head.firstChild ) );

            // Append trailing script if needed
            scriptAfter && head[ STR_INSERT_BEFORE ]( scriptAfter , firstChild );

            // If a timeout is needed, install it
            timeoutTimer = timeout > 0 && setTimeout( function() {
                notifyError( STR_TIMEOUT );
            } , timeout );

        }

        return xOptions;
    }

    // ###################### SETUP FUNCTION ##
    jsonp.setup = function( xOptions ) {
        $.extend( xOptionsDefaults , xOptions );
    };

    // ###################### INSTALL in jQuery ##
    $.jsonp = jsonp;

} )( jQuery );
//
// End jquery jsonp plugin
//

//
// Begin embedded easyModal.js
//
/**
 * easyModal.js v1.1.0
 * A minimal jQuery modal that works with your CSS.
 * Author: Flavius Matis - http://flaviusmatis.github.com/
 * URL: https://github.com/flaviusmatis/easyModal.js
 */

(function($){

    var methods = {
        init : function(options) {

            var defaults = {
                top: 'auto',
                autoOpen: false,
                overlayOpacity: 0.5,
                overlayColor: '#000',
                overlayClose: true,
                overlayParent: 'body',
                closeOnEscape: true,
                closeButtonClass: '.close',
                onOpen: false,
                onClose: false
            };

            options = $.extend(defaults, options);

            return this.each(function() {

                var o = options;

                var $overlay = $('<div class="lean-overlay"></div>');

                $overlay.css({
                    //'display': 'none',
                    //'position': 'fixed',
                    //'z-index': 2000,
                    //'top': 0,
                    //'left': 0,
                    //'height': 100 + '%',
                    //'width': 100+ '%',
                    //'background': o.overlayColor,
                    //'opacity': o.overlayOpacity
                }).appendTo(o.overlayParent);

                var $modal = $(this);

                $modal.css({
                    'display': 'none',
                    'position' : 'absolute',
                    'z-index': 2001
                });

                $modal.bind('openModal', function(){
                    $(this).css({
                        'display' : 'block'
                    });
                    $overlay.fadeIn(200, function(){
                        if (o.onOpen && typeof (o.onOpen) === 'function') {
                            // onOpen callback receives as argument the modal window
                            o.onOpen($modal[0]);
                        }
                    });
                });

                $modal.bind('closeModal', function(){
                    $(this).css('display', 'none');
                    $overlay.fadeOut(200, function(){
                        if (o.onClose && typeof(o.onClose) === 'function') {
                            // onClose callback receives as argument the modal window
                            o.onClose($modal[0]);
                        }
                    });
                });

                // Close on overlay click
                $overlay.click(function() {
                    if (o.overlayClose)
                        $modal.trigger('closeModal');
                });



                $(document).keydown(function(e) {
                    // ESCAPE key pressed
                    if (o.closeOnEscape && e.keyCode == 27) {
                        $modal.trigger('closeModal');
                    }
                });

                // Close when button pressed
                $modal.on('click', o.closeButtonClass, function(e) {
                    $modal.trigger('closeModal');
                    e.preventDefault();
                });

                // Automatically open modal if option set
                if (o.autoOpen)
                    $modal.trigger('openModal');

            });

        }
    };

    $.fn.easyModal = function(method) {

        // Method calling logic
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || ! method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('Method ' + method + ' does not exist on jQuery.easyModal');
        }

    };

})(jQuery);
//
// End embedded easyModal.js
//


//
// Begin embedded jquery cookie plugin, for readying and writing cookies easily
//
(function (factory) {
    if (typeof define === 'function' && define.amd) {
// AMD. Register as anonymous module.
        define(['jquery'], factory);
    } else {
// Browser globals.
        factory(jQuery);
    }
}(function ($) {

    var pluses = /\+/g;

    function raw(s) {
        return s;
    }

    function decoded(s) {
        return decodeURIComponent(s.replace(pluses, ' '));
    }

    function converted(s) {
        if (s.indexOf('"') === 0) {
// This is a quoted cookie as according to RFC2068, unescape
            s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
        }
        try {
            return config.json ? JSON.parse(s) : s;
        } catch(er) {}
    }

    var config = $.cookie = function (key, value, options) {

// write
        if (value !== undefined) {
            options = $.extend({}, config.defaults, options);

            if (typeof options.expires === 'number') {
                var days = options.expires, t = options.expires = new Date();
                t.setDate(t.getDate() + days);
            }

            value = config.json ? JSON.stringify(value) : String(value);

            return (document.cookie = [
                config.raw ? key : encodeURIComponent(key),
                '=',
                config.raw ? value : encodeURIComponent(value),
                options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
                options.path    ? '; path=' + options.path : '',
                options.domain  ? '; domain=' + options.domain : '',
                options.secure  ? '; secure' : ''
            ].join(''));
        }

// read
        var decode = config.raw ? raw : decoded;
        var cookies = document.cookie.split('; ');
        var result = key ? undefined : {};
        for (var i = 0, l = cookies.length; i < l; i++) {
            var parts = cookies[i].split('=');
            var name = decode(parts.shift());
            var cookie = decode(parts.join('='));

            if (key && key === name) {
                result = converted(cookie);
                break;
            }

            if (!key) {
                result[name] = converted(cookie);
            }
        }

        return result;
    };

    config.defaults = {};

    $.removeCookie = function (key, options) {
        if ($.cookie(key) !== undefined) {
// Must not alter options, thus extending a fresh object...
            $.cookie(key, '', $.extend({}, options, { expires: -1 }));
            return true;
        }
        return false;
    };

}));
//
// End embedded jquery cookie plugin, for readying and writing cookies easily
//


//
// Begin iGlobal Stores Splash code
//

function ig_getParameterByName(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

function ig_createSplashHtml() {
    var ig_splashHtml = '<div id="igSplashElement" class="drop" style="display:none;">';
    ig_splashHtml += ig_createSplashContentsHtml();
    ig_splashHtml += '</div>';
    return ig_splashHtml;
}

function ig_createSplashContentsHtml() {
    var ig_splashHtml = '' +
        '<div id="igWelcomeMat">' +
        '<div class="igModalHeader">' +
        //'<img src="http://www.incoco.com/skin/frontend/enterprise/incoco/images/logo.png" alt="Incoco Logo" class="modalLogo" />' +
        '<div class="igWelcomeCountryMessage">' +
        //'<span class="igAboveFlag">Thanks for visiting us from</span><br />' +
        //'<span class="igCountryFlagName"><img src="https://checkout.iglobalstores.com/images/flags/'+((ig_country)?ig_country.toLowerCase():'undefined')+'.gif" class="igWelcomeFlag" alt="'+((ig_country)?'Flag of '+ig_countries[ig_country]:'Please select your country.')+'" /> <span class="country-name">'+((ig_country&&ig_countries[ig_country])?ig_countries[ig_country]:'')+'</span></span>' +
        //'<div class="igUnderFlag"><span>'+((ig_country&&ig_countries[ig_country])?'Not in '+ig_countries[ig_country]+'? Select a different country. ':'We were unable to determined your location.<br>Please select your country from the list.') +
        '<span class="change-location">change your location</span>' +
        '</span><div class="select-wrapper"><select id="countrySelect" onchange="ig_countrySelected();">' +
            '<option value="">Select your country</option>';

    for(var countryCode in ig_countries){
        ig_splashHtml += '<option '+((countryCode===ig_country)?'selected="selected" ':'')+'value="'+countryCode+'">'+ig_countries[countryCode]+'</option>';
    }


    /* var sdgOptionText = ''+
    '<div class="igWelcomeFeatureImages">' +
        '<div class="available-shipping">'+
            '<h3>International Shipping Available</h3>'+
            '<ul>'+
                '<li>&bull; See your product prices and order total in your own currency at checkout.</li>'+
                '<li>&bull; You have the option to prepay duties and taxes to your country.</li>'+
                '<li>&bull; Choose from multiple international shipping options.</li>'+
            '</ul>'+
        '</div>' +
    '</div>' +
    '<p class="igWelcomeMessage">If you have any questions about international orders please call +01.255.884.2233.</p>'; */

    ig_splashHtml += '' +
        '</select></div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '<div class="igModalBody">';


    var buttonText = 'Start Shopping';


    var us_store = ['US', 'CA', 'JP', 'KR', 'CR'];

    /*if (jQuery.inArray(ig_country, us_store) == -1) {

        ig_splashHtml += sdgOptionText; 

    } else { 

        if (ig_country == 'US') {
        ig_splashHtml += '' +
            '<div class="igWelcomeFeatureImages">' +
                '<div class="available-shipping">'+
                    '<ul>'+
                        '<li>&bull; Incoco products are made in the USA.</li>'+
                        '<li>&bull; Enjoy free shipping on orders over $50.</li>'+
                        '<li>&bull; We promise you\'ll love it! Return shipping is free.</li>'+
                    '</ul>'+
                '</div>' +
            '</div>' +
            '<p class="igWelcomeMessage">If you have any questions about products or orders please email <a href="mailto:customerservice@incoco.com">customerservice@incoco.com</a></p>';
        } else if (ig_country == 'CA') {
        ig_splashHtml += '' +
            '<div class="igWelcomeFeatureImages">' +
                '<div class="available-shipping">'+
                    '<h3>We Ship to Canada</h3>'+
                    '<ul>'+
                        '<li>&bull; Enjoy free shipping on orders over $50.</li>'+
                        '<li>&bull; We promise you\'ll love it! Return shipping is free.</li>'+
                    '</ul>'+
                '</div>' +
            '</div>' +
            '<p class="igWelcomeMessage">If you have any questions about products or orders please email <a href="mailto:customerservice@incoco.com">customerservice@incoco.com</a></p>';
        } else if (ig_country == 'CR') {
            ig_splashHtml += '' +
            '<a href="http://www.incoco.cr/" target="_blank" class="faux-btn">Shop incoco.cr</a>'
            buttonText = 'View USA Site';

        } else {

            ig_splashHtml += ((ig_country == 'KR') ? '<a href="http://www.incocokorea.com/" target="_blank" class="faux-btn">Shop incocokorea.com</a>' : '<a href="http://www.incoco.jp/s/" target="_blank" class="faux-btn">Shop incoco.jp</a>');
            buttonText = 'View USA Site';
        }
    } */




    ig_splashHtml += '' +
        '</div>' +
        '<div class="igModalFooter">' +
        '<div class="igWelcomeCTAButton">';


    if (jQuery.inArray(ig_country, us_store) != -1) {

        ig_splashHtml += '<a href="'+'//' + location.host + location.pathname+'?___store=default" class="faux-btn">'+buttonText+'</a>';

    } else { 

        ig_splashHtml += '<a href="'+'//' + location.host + location.pathname+'?___store=international" class="faux-btn">'+buttonText+'</a>';
    }


    ig_splashHtml += ''+
        '</div>' +
        '</div>';
    return ig_splashHtml;
}

function ig_countrySelected() {
    var countryCode = jQuery("select#countrySelect").val();
    ig_setCountry(ig_validateCountryCode(countryCode));

    //var us_store = ['US', 'CA', 'JP', 'KR', 'CR'];
    var us_store = ['US', 'CA'];
	if(countryCode == 'BR'){
		window.location.href = 'http://www.incocobrasil.com.br/';
	} else if(countryCode == 'CR'){
		window.location.href = 'http://www.incoco.cr/';
	} else if(countryCode == 'JP'){
		window.location.href = 'http://www.incoco.jp/';
	} else if(countryCode == 'KR'){
		window.location.href = 'http://www.incocokorea.com/';
	} else if (jQuery.inArray(ig_country, us_store) != -1) {
        window.location.href = '//' + location.host + location.pathname+'?___store=default';
    } else { 
        window.location.href = '//' + location.host + location.pathname+'?___store=international';
    }

    //var container = jQuery("#igSplashElement");
    //container.html(ig_createSplashContentsHtml());
}

//Called by auto popup logic for first time non US customers.  Also called by ALL customers clicking the nested flag on the page
function ig_showTheSplash() {
    //Construct the modal
    jQuery(".site-links #igNest").append(ig_createSplashHtml());

    //init easyModal.js modal, after modal content was placed on the page (line above)
    jQuery("#igSplashElement").easyModal({
        onClose: function(myModal){
            //on close, let's remove the modal contents and the modal smokescreen created by easyModal.js
            jQuery("#igSplashElement").remove();
            jQuery(".lean-overlay").remove();
        }
    });

    //Fire the modal!
    jQuery("#igSplashElement").trigger('openModal');
    jQuery('#igNest').addClass('active');

    //Set cookie for Splash shown
    if (ig_validateCountryCode(jQuery.cookie("igCountry"))) { // Only set the splashShown cookie, if there is a valid countryCookie
        jQuery.cookie('igSplash', 'igSplash', { expires: 7, path: '/', domain: ig_cookieDomain });
    }
}

function ig_createNestContents() {
    return '<a class="trigger-location" href="#"><img onclick="ig_showTheSplash();" src="https://checkout.iglobalstores.com/images/flags/'+((ig_country)?ig_country.toLowerCase():'undefined')+'.gif" class="igWelcomeFlagHeader" alt="Select your country." /></a>';
}

function ig_placeNestHtml() {
    jQuery(function(){
        if (jQuery("#"+ig_nestElementId)) {
            jQuery("#"+ig_nestElementId).html(ig_createNestContents());
        }
    });
}

function ig_placeMobile() {
	var ig_splashHtml = '<div class="globe-icon"></div><div class="select-wrapper"><select id="countrySelect" onchange="ig_countrySelected();">' +
            '<option value="">Select your country</option>';

    for(var countryCode in ig_countries){
        ig_splashHtml += '<option '+((countryCode===ig_country)?'selected="selected" ':'')+'value="'+countryCode+'">'+ig_countries[countryCode]+'</option>';
    }



    ig_splashHtml += '' +
        '</select></div>';
        
	jQuery(function(){
        if (jQuery("#igMobile")) {
            jQuery("#igMobile").append(ig_splashHtml);
        }
    });
}

function ig_setCountry(country) {
    ig_country = country;
    if (ig_country) {
        //Set country cookie
        jQuery.cookie('igCountry', ig_country, { expires: 365, path: '/', domain: ig_cookieDomain });
    }
    ig_placeNestHtml();
    jQuery('#igNest').removeClass('active');
}

function ig_validateCountryCode(countryCode) {
    //Return the country code if valid, return null if not valid
    var countryDisplayName = ig_countries[countryCode];
    if (typeof countryDisplayName !== 'undefined' && countryDisplayName) {
        return countryCode;
    } else {
        return null;
    }
}

function ig_detectCountryCallback(countryCode) {
    ig_setCountry(ig_validateCountryCode(countryCode));
    ig_finishLoading();
}

function ig_detectCountryCallbackError() { // Error handling method for when the jsonp call to get the countryCode fails, if it will get called?
    console.log("Couldn't detect country");
    ig_finishLoading();
}

function ig_detectCountry() {
//    $.ajax({
//        dataType: "jsonp",
//        url: 'https://iprecon.iglobalstores.com/iGlobalIp.js?p=igcCallback',
//        statusCode: {
//            503: function(){igcCallbackError();}
//        }
//    });
    jQuery.jsonp({
        url: 'https://iprecon.iglobalstores.com/iGlobalIp.js?p=igcCallback',
        callback:'igcCallback',
        success: function(json, textStatus, xOptions){ig_detectCountryCallback(json);},
        error: function(){ig_detectCountryCallbackError();}
    });
}

function ig_pingIglobal() {
    if (!ig_countryParam) {//Only ping iGlobal for real visitors, not url parameter testing
        jQuery.ajax({//we do not need to trap errors like 503's, for this call
            dataType: "jsonp",
            url: 'https://iprecon.iglobalstores.com/ping.js?s='+ig_storeId+'&c='+((ig_country)?ig_country:'')
        });
    }
}

//function ig_errorPingIglobal() {
//    console.log("Couldn't update iGlobal");
//}

function ig_finishLoading() {
    ig_placeNestHtml();
    if (!(ig_country && ig_country==="US") && (!ig_splashCookie || !ig_country || ig_countryParam)) {
        jQuery(ig_showTheSplash); //Schedule Showing the Splash
    }
    ig_pingIglobal();
}

var ig_countries = {"AL":"Albania","AS":"American Samoa","AD":"Andorra","AO":"Angola","AI":"Anguilla","AG":"Antigua","AR":"Argentina","AM":"Armenia","AW":"Aruba","AU":"Australia","AT":"Austria","BS":"Bahamas","BH":"Bahrain","BD":"Bangladesh","BB":"Barbados","BE":"Belgium","BZ":"Belize","BJ":"Benin","BM":"Bermuda","BT":"Bhutan","BO":"Bolivia","BQ":"Bonaire, St. Eustatius & Saba","BW":"Botswana","BR":"Brazil","BN":"Brunei","BG":"Bulgaria","BF":"Burkina Faso","BI":"Burundi","KH":"Cambodia","CM":"Cameroon","CA":"Canada","CV":"Cape Verde","KY":"Cayman Islands","TD":"Chad","CL":"Chile","CN":"China - People's Republic of","CO":"Colombia","KM":"Comoros","CG":"Congo","CK":"Cook Islands","CR":"Costa Rica","HR":"Croatia","CW":"Curaçao","CY":"Cyprus","CZ":"Czech Republic","DK":"Denmark","DJ":"Djibouti","DM":"Dominica","DO":"Dominican Republic","EC":"Ecuador","EG":"Egypt","SV":"El Salvador","GQ":"Equatorial Guinea","ER":"Eritrea","ET":"Ethiopia","FK":"Falkland Islands","FO":"Faroe Islands (Denmark)","FJ":"Fiji","FI":"Finland","FR":"France","GF":"French Guiana","GA":"Gabon","GM":"Gambia","DE":"Germany","GI":"Gibraltar","GR":"Greece","GL":"Greenland (Denmark)","GD":"Grenada","GP":"Guadeloupe","GU":"Guam","GT":"Guatemala","GG":"Guernsey","GN":"Guinea","GY":"Guyana","HT":"Haiti","HN":"Honduras","HK":"Hong Kong","HU":"Hungary","IS":"Iceland","IN":"India","ID":"Indonesia","IE":"Ireland - Republic Of","IL":"Israel","IT":"Italy","JM":"Jamaica","JP":"Japan","JE":"Jersey","JO":"Jordan","KE":"Kenya","KI":"Kiribati","KR":"Korea, Republic of (South Korea)","KW":"Kuwait","KG":"Kyrgyzstan","LA":"Laos","LS":"Lesotho","LR":"Liberia","LI":"Liechtenstein","LU":"Luxembourg","MO":"Macau","MG":"Madagascar","MW":"Malawi","MY":"Malaysia","MV":"Maldives","ML":"Mali","MT":"Malta","MH":"Marshall Islands","MQ":"Martinique","MR":"Mauritania","MU":"Mauritius","FM":"Micronesia - Federated States of","MD":"Moldova","MC":"Monaco","MN":"Mongolia","MS":"Montserrat","MA":"Morocco","MZ":"Mozambique","NP":"Nepal","NL":"Netherlands (Holland)","NC":"New Caledonia","NZ":"New Zealand","NI":"Nicaragua","NE":"Niger","NG":"Nigeria","MP":"Northern Mariana Islands","NO":"Norway","OM":"Oman","PK":"Pakistan","PW":"Palau","PA":"Panama","PG":"Papua New Guinea","PY":"Paraguay","PE":"Peru","PH":"Philippines","PL":"Poland","PT":"Portugal","PR":"Puerto Rico","QA":"Qatar","RO":"Romania","RW":"Rwanda","SM":"San Marino","ST":"Sao Tome & Principe","SA":"Saudi Arabia","SN":"Senegal","SC":"Seychelles","SG":"Singapore","SK":"Slovakia","SI":"Slovenia","ZA":"South Africa","ES":"Spain","LK":"Sri Lanka","BL":"St. Barthelemy","KN":"St. Kitts and Nevis","LC":"St. Lucia","MF":"St. Maarten","VC":"St. Vincent","SR":"Suriname","SZ":"Swaziland","SE":"Sweden","CH":"Switzerland","PF":"Tahiti","TW":"Taiwan","TJ":"Tajikistan","TZ":"Tanzania","TH":"Thailand","TL":"Timor-Leste","TG":"Togo","TO":"Tonga","TT":"Trinidad and Tobago","TN":"Tunisia","TR":"Turkey","TM":"Turkmenistan","TC":"Turks and Caicos Islands","AE":"United Arab Emirates","GB":"United Kingdom","US":"United States","UY":"Uruguay","UZ":"Uzbekistan","VU":"Vanuatu","VA":"Vatican City","VE":"Venezuela","VN":"Vietnam","VG":"Virgin Islands (British)","VI":"Virgin Islands (U.S.)","WS":"Western Samoa","YE":"Yemen","ZM":"Zambia","ZW":"Zimbabwe"};

var ig_storeId = 230;

var ig_cookieDomain = ".incoco.com";

var ig_nestElementId = "igNest";

var ig_country = null;
var ig_countryCookie = ig_validateCountryCode(jQuery.cookie("igCountry"));
var ig_countryParam = ig_validateCountryCode(ig_getParameterByName("igCountry"));
var ig_splashCookie = jQuery.cookie("igSplash");

//set country to URL parameter igCountry
if (!ig_country && ig_countryParam) {
    ig_country = ig_countryParam;
}

//else set country to countryCookie
if (!ig_country && ig_countryCookie) {
    ig_country = ig_countryCookie;
}

//else set country to countryIP from iGlobal's IP Recognition Service
if (!ig_country) {
    ig_detectCountry();
} else { // else go with whatever country we have, even no country
    ig_finishLoading();
}


if (isMobile()) {
	ig_placeMobile();
}