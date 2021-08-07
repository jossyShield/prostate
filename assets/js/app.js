/*
 * Chymalla
 * Description: utility object to handy functions
 */
window.App = (function(app, $) {
    /* 
     * GLOBAL ROOT (DO NOT CHANGE)
     */
    $.root_ = $('body');
    /*
     * GLOBAL: Sound Config (define sound path, enable or disable all sounds)
     */
    $.sound_path = "assets/sound/";
    $.sound_on = true;
    $.is_production = false;
    $.recaptcha_mode = false;
    $.users = ['admin', 'manager', 'accountant', 'client'];
    $.tpls = { 'members': 'CHY_MEMBERS.xlsx' };
    $.theme_img = { 'LIGHT': 'default-b.png', 'DARK': 'default.png' };
    $.notif_time = 60e3 * 2;
    /*
     * DEBUGGING MODE
     * app.debug.state = true; will spit all debuging message inside browser console.
     * The colors are best displayed in chrome browser.
     */
    app.debug = {
        state: true,
        style: 'font-weight: bold; color: #00f;',
        style_green: 'font-weight: bold; font-style:italic; color: #46C246;',
        style_red: 'font-weight: bold; color: #ed1c24;',
        style_warning: 'background-color:yellow',
        style_success: 'background-color:green; font-weight:bold; color:#fff;',
        style_error: 'background-color:#ed1c24; font-weight:bold; color:#fff;',
    };
    /*
     * These elements are ignored during DOM object deletion for ajax version 
     * It will delete all objects during page load with these exceptions:
     */
    app.ignore_key_elms = ['script,.content,header,footer'];
    /*
     * 
     *    Email reg
     */
    app.email_reg = /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
    /**
     * 
     *   Url reg
     */
    app.url_reg = /^(?:(?:(?:https?|ftp):)?\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})).?)(?::\d{2,5})?(?:[/?#]\S*)?$/i;
    /**
     *  Switch betweeen Ajax Mode and Html
     */
    app.ajax_mode = false;
    /*
     * DETECT MOBILE DEVICES
     * Description: Detects mobile device - if any of the listed device is 
     * detected a class is inserted to $.root_ and the variable thisDevice 
     * is decleard. (so far this is covering most hand held devices)
     */
    app.is_mobile = (/iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()));
    /*
     * Top menu on/off
     */
    app.top_menu = false;
    /*
     * desktop or mobile
     */
    app.this_device = null;
    /*
     * table display size
     */
    app.display_size = 50;
    /*
     * JS ARRAY SCRIPT STORAGE
     * Description: used with loadScript to store script path and file name
     * so it will not load twice
     */
    app.js_array = {};
    /*
     * ADD DEVICE TYPE
     * Detect if mobile or desktop
     */
    app.add_device_type = function() {

        if (!app.is_mobile) {
            // Desktop
            $.root_.addClass("desktop-detected");
            app.this_device = "desktop";
        } else {
            // Mobile
            $.root_.addClass("mobile-detected");
            app.this_device = "mobile";
        }

    };
    /*
     * LOAD SCRIPTS
     * Usage:
     * Define function = my_pretty_ode ()...
     * load_script("js/my_lovely_script.js", my_pretty_code);
     */
    app.load_script = function(script_name, callback, options) {
        if (!app.js_array[script_name]) {
            let promise = $.Deferred();
            // adding the script tag to the head as suggested before
            let body = document.getElementsByTagName('body')[0],
                script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = script_name;

            options && app.in_array('defer', options) && (script.async = true);
            options && app.in_array('async', options) && (script.defer = true);
            // then bind the event to the callback function
            // there are several events for cross browser compatibility
            script.onload = function() {
                promise.resolve();
            };
            // fire the loading
            body.appendChild(script);
            // clear DOM reference
            body = null;
            script = null;
            app.js_array[script_name] = promise.promise();
        } else if (app.debug.state) {
            !$.is_production && console.log("This script was already loaded %c: " + script_name, app.debug.style_warning);
        }

        app.js_array[script_name].then(function() {
            typeof callback === 'function' && callback();
        });
    };
    /*
     * Load ajax pages
     */
    app.load_url = function(url, container) {
        container || (container = $('.content'));
        // app.debug.state
        if (app.debug.state) {
            !$.is_production && console.log("Loading URL: %c" + url, app.debug.style);
        }

        $.ajax({
            type: "GET",
            url: url,
            dataType: 'html',
            cache: true, // (warning: setting it to false will cause a timestamp and will call the request twice)
            beforeSend: function() {
                // destroy page object instance info
                app.current && app[app.current] && (app[app.current].clean_up(), delete app[app.current], (app.debug.state && console.log("✔ instances destroyed")));
                // clear page content
                container.removeData().empty();
                // place cog to show loading...
                container.html('<h1 class="ajax-loading-animation"><i class="fa fa-cog fa-spin"></i> Loading...</h1>');
                // clear everything else except these key DOM elements
                // we do this because sometime plugins will leave dynamic elements behind
                $('body').find('> *').filter(':not(' + app.ignore_key_elms + ')').empty().remove();
                // scroll up
                $("html").animate({
                    scrollTop: 0
                }, "fast");
            },
            success: function(data) {
                // dump data to container
                container.css({
                    opacity: '0.0'
                }).html(data).delay(50).animate({
                    opacity: '1.0'
                }, 300);
                // clear data var
                data = null;
                container = null;
                // change url
                // window.history.replaceState(null, null, url.replace('page/', ''));
                // load script in ajax mode
                // app.ajax_mode && app.load_script(App_sett.url + App_sett.path.custom + url.split('&')[0].split('=')[1] + '.js');
            },
            error: function(xhr, status, thrownError, error) {
                container.html('<h4 class="ajax-loading-error"><i class="fa fa-warning txt-color-orangeDark"></i> Error requesting <span class="txt-color-red">' + url.replace('page/', '') + '</span>: ' + xhr.status + ' <span style="text-transform: capitalize;">' + thrownError + '</span></h4>');
            },
            async: true
        });
    };
    app.ajax_call = function(options) {
        /*
         * data_type - datatype - json, text, html, xml, etc
         * type - carrier method - post, get
         * url - path
         * data - information
         * pre_callback - pre callback function
         * post_callback - post callback function
         * g - isloading - help display loading view
         * f - loading handle
         */
        let sett = $.extend({
            data_type: 'json',
            type: 'post',
            pre_callback: null,
            post_callback: null,
            cache: true
        }, options);
        $.ajax({
            dataType: sett.data_type,
            type: sett.type,
            url: sett.url,
            data: sett.data,
            beforeSend: sett.pre_callback,
            cache: sett.cache
        }).done(function(res) {
            !$.is_production && console.log(res);
            if (res && res.redirect) {
                app.toast_error(res.reason + '<br/><br/>' + lang.text_redirect + (res.timer / 1000) + lang.date_seconds);
                setTimeout(function() {
                    localStorage.removeItem('_sl'), window.location = res.redirect;
                }, res.timer);
            } else {
                typeof sett.post_callback === 'function' && sett.post_callback(res);
            }
        }).fail(function(xhr, status, error_thrown) {
            !$.is_production && (app.toast_error(status + ': ' + error_thrown), console.log(xhr.responseText));
        });
    };
    app.ajax_form = function(data_type, url, pre_callback, post_callback, elem) {
        /*
         * data_type - datatype json, text, html, xml, etc
         * url - processing link
         * pre_callback - validation method befor submission
         * post_callback - function called after response
         * elem - the caller <form> tag
         */
        $(elem).ajaxSubmit({
            resetForm: true,
            dataType: data_type,
            type: 'post',
            url: url,
            cache: true,
            beforeSend: pre_callback,
            success: function(data) {
                typeof post_callback === 'function' && post_callback(data);
            },
            error: function(xhr, status, error_thrown) {
                !$.is_production && app.toast_error(status + ': ' + error_thrown);
            }
        });
    };
    /*
     * Ajax send/load
     */
    app.to_server = function(pay_load, url, page, action, action_fxn, success_fxn, fail_fxn, dt) {
        app.ajax_call({
            url: url,
            data_type: dt || 'json',
            data: {
                pay_load: JSON.stringify(pay_load),
                page: page,
                action: action
            },
            post_callback: function(res) {
                res.positive_fb && (app.toast_success(res.positive_fb), (typeof success_fxn === 'function' && success_fxn(res.data)));
                res.negative_fb && (app.toast_warning(res.negative_fb), (typeof fail_fxn === 'function' && fail_fxn(res.data)));
                'function' === typeof action_fxn && res && action_fxn(res);
            }
        });
    };
    /*
     * Ajax Loader
     */
    app.from_server = function(target, url, page, action, action_fxn) {
        app.to_server(target, url, page, action, action_fxn);
    };
    app.in_array = function(needle, array) {
        return array.some(function(elem) {
            return needle == elem;
        });
    };

    app.array_sum = function(val) {
        let total = 0;
        for (const i in val)
            total += isNaN(1 * val[i]) ? 0 : parseFloat(val[i]);
        return total;
    };
    app.is_leap_year = function(year) {
        year = parseInt(year);
        return (year % 4 == 0 && year % 100 != 0) || (year % 400 == 0)
    };
    app.is_date_valid = function(date) {
        if (!/^[\d]{4}(\-[\d]{1,2}){2}$/.test(date))
            return !1;
        date = date.split('-');
        if (date[0] === '0000' || date[1] === '00' || date[2] === '00')
            return !1;
        let months = { 1: 31, 2: app.is_leap_year(date[0]) ? 29 : 28, 3: 31, 4: 30, 5: 31, 6: 30, 7: 31, 8: 31, 9: 30, 10: 31, 11: 30, 12: 31 };
        if (!months[parseInt(date[1])])
            return !1;
        return !(parseInt(date[2]) > months[parseInt(date[1])]);
    };
    app.query_csc_loader = function(elem, is_new_info, sel_val, state_key, type) {
        if (is_new_info)
            $(elem).html("<option value=''>SELECT " + type + "</option>"),
            (type !== 'CITY' || state_key) && $.each(type === 'COUNTRY' ? _countries : (type === 'STATE' ? _states : _cities[state_key]), function(key, val) {
                $(elem).append("<option value='" + key + "'>" + val + "</option>");
            });
        if (sel_val)
            $(elem).val(sel_val);
    };
    app.vanilla_js_csc_loader = function(id_or_class, id_or_class_name, class_pos, is_new_info, sel_val, state_key, type) {
        let elem, options = [];
        if (id_or_class)
            elem = document.getElementById(id_or_class_name);
        else
            elem = document.getElementsByClassName(id_or_class_name)[class_pos];
        if (is_new_info)
            options.push("<option value=''>SELECT " + type + "</option>"),
            (type !== 'CITY' || state_key) && $.each(type === 'COUNTRY' ? _countries : (type === 'STATE' ? _states : _cities[state_key]), function(key, val) {
                options.push("<option value='" + key + "'" + (key === sel_val ? " selected" : "") + ">" + val + "</option>");
            }), elem.innerHTML = options.join('');
    };
    app.validate_file = function(elem, output, load_elem, file_types, type_name, max_size) {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            if (!$(elem).val().trim()) {
                $(output).html(lang.upload_file_no_file_select);
                return !1;
            }
            let bytes = $(elem)[0].files[0].size,
                type = $(elem)[0].files[0].type;
            if (!in_array(type, file_types)) {
                $(output).html(lang.upload_file_unsupported + ' ' + lang.upload_file_accepted + file_types.join(', '));
                return !1;
            }
            if (bytes > max_size) {
                $(output).html(lang.upload_file_exceeds_limit + ' ' + lang.upload_file_max_text + app.mem_unit_converter(max_size).join(''));
                return !1;
            }

            load_elem && $(load_elem).removeClass('sl_hide');
            $(output).empty();
            return !0;
        }

        $(output).html(lang.browser_deficiency);
        return !1;
    };
    app.mem_unit_converter = function(bytes) {
        /*
         * uc - unit converter
         */
        return bytes > 1099511627776 ? [bytes / 1099511627776, lang.terabyte_abbr] : (bytes > 1073741824 ? [Math.floor(bytes / 1073741824), lang.gigabyte_abbr] : (bytes > 1048576 ? [Math.floor(bytes / 1048576), lang.megabyte_abbr] : (bytes > 1024 ? [Math.floor(bytes / 1024), lang.kilobyte_abbr] : [bytes, lang.bytes])));
    };
    app.str_replace = function(search_str, replace_str, str) {
        let new_str = str.replace(search_str, replace_str);
        return new_str === str ? new_str : app.str_replace(search_str, replace_str, new_str);
    };
    app.uc_first = function(str) {
        if (str.length === 0 || str.length === 1)
            return str.toUpperCase();
        return str[0].toUpperCase() + str.substr(1).toLowerCase();
    };
    app.uc_words = function(str) {
        if (0 === str.length)
            return str;
        str = str.split(/\s/);
        for (const sub in str)
            str[sub] = app.uc_first(str[sub]);
        return str.join(" ");
    };
    app.nf = function(a, b, c, d) {
        return app.number_format(a, b, c, d);
    };
    app.number_format = function(number, decimals, decPoint, thousandsSep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
        const n = !isFinite(+number) ? 0 : +number
        const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
        const sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
        const dec = (typeof decPoint === 'undefined') ? '.' : decPoint
        let s = ''
        const toFixedFix = function(n, prec) {
                if (('' + n).indexOf('e') === -1) {
                    return +(Math.round(n + 'e+' + prec) + 'e-' + prec)
                } else {
                    const arr = ('' + n).split('e')
                    let sig = ''
                    if (+arr[1] + prec > 0) {
                        sig = '+'
                    }
                    return (+(Math.round(+arr[0] + 'e' + sig + (+arr[1] + prec)) + 'e-' + prec)).toFixed(prec)
                }
            }
            // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec).toString() : '' + Math.round(n)).split('.')
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || ''
            s[1] += new Array(prec - s[1].length + 1).join('0')
        };

        return s.join(dec);

    };
    app.format_money = function(amount) {
        let m_range = [3, 6, 9, 12, 15, 18, 21, 24],
            m_unit = ['', 'K', 'M', 'B', 'T'],
            new_amt = (Number(amount) + '').length;
        for (let k in m_range) {
            if (new_amt <= m_range[k]) {
                return (Number.parseInt(amount / Math.pow(10, m_range[k] - 3) * 100) / 100) + m_unit[k];
            }
        }

        return new_amt;
    };
    app.selection_key_sort = function(array_keys, array, key, desc_order) {

        desc_order = typeof desc_order === 'undefined' ? false : desc_order;
        let i, current_min, current_min_index, j;
        for (i = 0; i < array_keys.length - 1; i++) {
            current_min = array_keys[i];
            current_min_index = i;
            // lowest to highest - false
            // highest to lowest - true
            for (j = i + 1; j < array_keys.length; j++) {
                if (desc_order) {
                    if (array[current_min][key] < array[array_keys[j]][key]) {
                        current_min = array_keys[j];
                        current_min_index = j;
                    }
                } else {
                    if (array[current_min][key] > array[array_keys[j]][key]) {
                        current_min = array_keys[j];
                        current_min_index = j;
                    }
                }
            }

            // swap array[i] with array[current_min_index] if necessary
            if (current_min_index != i) {
                array_keys[current_min_index] = array_keys[i];
                array_keys[i] = current_min;
            }
        }

        i = current_min = current_min_index = j = null;
        // return array_keys;
    };
    app.selection_key_sort_combined = function(array_keys, array, keys, desc_order) {

        desc_order = typeof desc_order === 'undefined' ? false : desc_order;
        let i, current_min, current_min_index, j;
        for (i = 0; i < array_keys.length - 1; i++) {
            current_min = array_keys[i];
            current_min_index = i;
            // lowest to highest - false
            // highest to lowest - true
            for (j = i + 1; j < array_keys.length; j++) {
                if (desc_order) {
                    if ((array[current_min][keys[0]] + array[current_min][keys[1]]) < (array[array_keys[j]][keys[0]] + array[array_keys[j]][keys[1]])) {
                        current_min = array_keys[j];
                        current_min_index = j;
                    }
                } else {
                    if ((array[current_min][keys[0]] + array[current_min][keys[1]]) > (array[array_keys[j]][keys[0]] + array[array_keys[j]][keys[1]])) {
                        current_min = array_keys[j];
                        current_min_index = j;
                    }
                }
            }

            // swap array[i] with array[current_min_index] if necessary
            if (current_min_index != i) {
                array_keys[current_min_index] = array_keys[i];
                array_keys[i] = current_min;
            }
        }

        i = current_min = current_min_index = j = null;
        // return array_keys;
    };
    /**
     * 
     * Search for item
     */
    app.search_item = function(items, items_pos, query, search_pos, search_pointer) {
        return items_pos.filter(function(id) {
            if (Array.isArray(search_pos)) {
                let i, size = search_pos.length;
                for (i = 0; i < size; i++)
                    if (items[id][search_pos[i]].toLowerCase().indexOf(query) >= 0)
                        return true;
            }

            return false;
        });
    };

    app.initializeToupBox = function(id, role, username, source) {
        $('.currency-unit').html(App_sett.sys.unit == '$' ? '$' : '&#x20A6;');

        $('#top-up-form').trigger('reset').data('id', id).data('role', role).data('source', source);

        $('.top-up-label').find('span').html(username);

        $('.exchange-value>small').empty();

        $('#top-up-dialog').modal('show');
    };

    app._update_a_top_up = function(role, id, amt) {
        // update bal top up for account
        $('.fs-sub>strong').html(App_sett.sys.unit.replace('NGN', '&#x20A6;') + App.nf(App_sett.sys.unit == '$' ? Number(amt.bal_dollar) : Number(amt.bal_naira), 2));
    };

    return app;
})({}, jQuery);

$ = jQuery;
/*
 * Prerequisite Setup
 */
window.onload = function() {
    /*
     * DELETE MODEL DATA ON HIDDEN
     * Clears the model data once it is hidden, this way you do not create duplicated data on multiple modals
     */
    $('body').on('hidden.bs.modal', '.modal', function() {
        $(this).removeData('bs.modal');
    });

    $.fn.serialize_form_json = function() {
        let o = {},
            a = this.serializeArray();
        $.each(a, function() {
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
    // Attempt to load default language of the system
    String.prototype.endsWith || (String.prototype.endsWith = function(a, b) {
        let c = this.toString();
        ("number" !== typeof b || !isFinite(b) || Math.floor(b) !== b || b > c.length) && (b = c.length), b -= a.length;
        let d = c.lastIndexOf(a, b);
        return -1 !== d && d === b
    });
    String.prototype.startsWith || (String.prototype.startsWith = function(a, b) {
        return b = b || 0, this.substr(b, a.length) === a
    });
    iziToast.settings({
        timeout: 10000,
        close: true,
        progressBar: true,
        progressBarEasing: 'ease'
    });
    App.toast_success = function(text, title) {
        iziToast.success({
            title: title || '',
            message: text
        });
    };
    App.toast_error = function(text, title) {
        iziToast.error({
            title: title || '',
            message: text
        });
    };
    App.toast_info = function(text, title) {
        iziToast.info({
            title: title || '',
            message: text
        });
    };
    App.toast_warning = function(text, title) {
        iziToast.warning({
            title: title || '',
            message: text
        });
    };
    App.toast_confirm = function(text, options, title) {
        iziToast.question({
            timeout: 0,
            close: false,
            overlay: true,
            displayMode: 'once',
            id: 'question',
            zindex: 999,
            title: title || '',
            message: text,
            position: 'center',
            buttons: [
                ['<button><b>YES</b></button>', function(instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    'object' === typeof options && options.do_on_yes && options.do_on_yes();
                }, true],
                ['<button>NO</button>', function(instance, toast) {
                    instance.hide({ transitionOut: 'fadeOut' }, toast, 'button');
                    'object' === typeof options && options.do_on_no && options.do_on_no();
                }]
            ]
        });
    };
    // Global listeners
    window.addEventListener('popstate', function(e) {
        !$.is_production && console.log('new state change');
    }, false);
    // Listen for click action on auth page
    $('body').on('click', 'a.sllink', function(e) {
        e.preventDefault();
        $(this).attr('href') !== location.href && App.load_url(App_sett.url + App_sett.path.page + 'auth/?' + ($(this).attr('href').split('?')[1] || 'page=login'));
    });

    // Execute code as long as its not authentication
    if (window.location.href.indexOf('/auth') < 0) {
        // Listen for click action on content page
        // and load appropriate page and script
        $('body').on('click', 'a.cllink', function(e) {
            e.preventDefault();

            if ($(this).data('info') === 'logout')
                App.toast_confirm('Logout?', {
                    'do_on_yes': function() {
                        localStorage.clear();
                        window.location = App_sett.url + '?page=logout';
                    }
                }, 'Goodbye!');
            else if (App.ajax_mode) {
                // $(this).attr('href') !== location.href && (App.load_url(App_sett.url + App_sett.path.page + '?' + ($(this).attr('href').split('?')[1] || 'page=dashboard')));
            }
        });

        $('[name="top-up-amt"]').on('keyup', function(e) {
            e = Number($(this).val());
            $('.exchange-value>small').html((App_sett.sys.unit == '$' ? '&#x20A6;' + App.nf(e * Number(App_sett.sys.exchange), 2) : '$' + App.nf(e / Number(App_sett.sys.exchange), 2)));
        });

        $('#cc-unit').on('click', function(e) {

            if ($(this).hasClass('disabled')) return;

            $(this).addClass('disabled');

            App.to_server({
                d_unit: $(this).data('unit'),
                d_currency: $(this).data('currency'),
                type: 2
            }, App_sett.url + App_sett.path.intercept, 'settings', 'change', function(res) {
                $('#cc-unit').removeClass('disabled');
                res.data && res.data == 1 && window.location.reload();
            });
        });

        $('#toup-admin').on('click', function(e) {
            e.preventDefault();

            e = $(this).data('id');

            const role = $(this).data('role');

            const name = $(this).data('name');

            App.initializeToupBox(e, role, name, '');
        });

        // Top-up balance
        $('#top-up-form').on('submit', function(e) {
            e.preventDefault();

            if ($('#top-up-submit').hasClass('disabled'))
                return;

            let top_up_amt = Number($('[name=top-up-amt]').val().trim()),
                role = $(this).data('role'),
                id = $(this).data('id'),
                source = $(this).data('source');

            if (isNaN(top_up_amt))
                App.toast_warning('Please enter a valid top-up amount, a negative sign before the amount signifies a top-down');
            else {
                App.to_server({
                    top_up_amt: top_up_amt,
                    id: id,
                    role: role,
                    source: source,
                    type: 2
                }, App_sett.url + App_sett.path.intercept, 'members', 'process', function(res) {
                    $('#top-up-submit').removeClass('disabled').html($('#top-up-submit').data('text'));
                }, function(data) {
                    $('#top-up-dialog').modal('hide'), data.id && (data.source ? _update_top_up(data.role, data.id, data.amt) : App._update_a_top_up(data.role, data.id, data.amt));
                });
            }

            top_up_amt = role = id = null;
        });

        // handles notification event
        $('.messages-notifications').on('click', function(e) {
            window.location.href.indexOf('?page=notifications') < 0 && (window.location = '?page=notifications');
        });

        // add notification sound
        window._nt_s = document.createElement("audio");
        _nt_s.setAttribute("src", window.location.href.split('/?')[0] + '/' + $.sound_path + "notif.mp3");

        window._notif_handler = void 0, window._load_extra_settings = function() {
            if (navigator.onLine)
                App.to_server({ 'ns': /*(localStorage.getItem('an') && JSON.parse(localStorage.getItem('an')).notify_server) || */ 0 },
                    App_sett.path.intercept,
                    'notifications',
                    'get',
                    function(res) {
                        res = res.data;
                        res.notif = parseInt(res.notif);
                        let ls = localStorage.getItem('an') /*, notify = 0*/ ;

                        if (ls) {
                            ls = JSON.parse(localStorage.getItem('an'));

                            if (res.notif > ls.notif)
                                _nt_s.play();
                        } else {
                            if (res.notif > 0)
                                _nt_s.play();
                        }

                        // res.notify_server = notify;
                        localStorage.setItem('an', JSON.stringify(res));
                        res.bal && $('.fs-sub>strong').html(res.bal.replace('NGN', '&#x20A6;'));
                        res.trans && $('.fs-extra-info>strong').html(App.nf(res.trans))
                        $('.new-messages-count').html(res.notif);
                        ls = notify = res = null;
                    });

            _notif_handler && clearTimeout(_notif_handler), _notif_handler = setTimeout(_load_extra_settings, $.notif_time);
        };

        _load_extra_settings();
    }

    typeof(window.setup) === 'function' && window.setup();
};