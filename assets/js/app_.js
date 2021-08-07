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
    app.ajax_mode = true;
    /*
     * DETECT MOBILE DEVICES
     * Description: Detects mobile device - if any of the listed device is 
     * detected a class is inserted to $.root_ and the variable thisDevice 
     * is decleard. (so far this is covering most hand held devices)
     */
    app.is_mobile = (/iphone|ipad|ipod|android|blackberry|mini|windows\sce|palm/i.test(navigator.userAgent.toLowerCase()));

    app.pathname = location.pathname;
    app.job_stages = ['clearing', 'sewing', 'laundry'];
    app.styles = ['aso-oke', 'agbada', 'senator wear'];
    app.page = [];

    app.isempty = function(el) {
        for (var i = 0; i < el.length; i++) {
            if (el[i].value.trim() === "") {
                return true;
                //break;
            }
        }
        return false;
    }

    app.isempty_ = function(el) {
        var error_elem_title, error_elem_text = '';
        for (var i = 0; i < el.length; i++) {
            if (el[i].value.trim() === "" && el[i].hasAttribute('required')) {
                error_elem_title = el[i].hasAttribute('required') ? el[i].getAttribute('title') : 'Empty Fields';
                error_elem_text = el[i].hasAttribute('required') ? el[i].getAttribute('required') : 'Please fill in the empty fields';
                App.toast_error(error_elem_text, error_elem_title);
                return true;
                //break;
            }
        }
        return false;
    }

    app.id_or_class_name = function(id, class_name = null) {
        var elem;
        if (class_name == null)
            elem = document.getElementById(id);
        else
            elem = document.getElementById(id).getElementsByClassName(class_name);

        return elem;
    }

    app.next_page = function(prev_obj, nextTab) {
        console.log("Previous object");
        console.log(prev_obj);
        nextTab(prev_obj);

    }

    app.measurment = {
        "weight": "",
        "height": "",
        "shoulder": "",
        "shortsleeves": "",
        "longsleeves": "",
        "tommy": "",
        "neck": "",
        "biceps": "",
        "": ""
    };

    app.measurment_unit = "in";

    app.prev_tab_object = {}

    app.add_measurment = function(obj) {
        var elem_id = app.id_or_class_name('stepContent2'),
            measurment = {};
        measurment = app.measurment;
        obj.measurment = measurment;
        if (elem_id)
            elem_id.onclick = function(e) {
                var target_elem = e.target,
                    target_name = target_elem.getAttribute("aniferaz-measurment-name"),
                    target = target_elem.getAttribute("aniferaz-measurment");

                if (target || target == "") {
                    target_name = target_name.trim().toLowerCase();

                    //Make target active
                    var elem = App.id_or_class_name('stepContent2', 'add-agent-btn');
                    for (var i = 0; i < elem.length; i++) {
                        elem[i].setAttribute('style', 'box-shadow: 0px 2px 4px rgb(126 142 177 / 12%)');
                    }
                    target_elem.setAttribute('style', 'box-shadow: 0px 2px 4px rgb(23 91 241 / 90%)');

                    //Make target value visible
                    elem = App.id_or_class_name('stepContent2', 'form-control');
                    target.trim() == "" ?
                        elem[0].value = target.trim() : elem[0].value = target;
                    elem[0].placeholder = target_name;

                    //Create Measurment Object on State Change
                    elem[0].onchange = function(e) {
                        target = elem[0].value.trim();
                        if (target_name == "weight")
                            measurment.weight = target;
                        else if (target_name == "height")
                            measurment.height = target;
                        else
                            measurment.shoulder = target;

                        target_elem.setAttribute("aniferaz-measurment", target);

                        target_elem.getElementsByTagName("span")[0].innerHTML = target + app.measurment_unit;

                        app.measurment = measurment;
                        obj.measurment = app.measurment;

                    }
                }
                app.prev_tab_object = obj;
            }
    }

    app.upload_photo = function(obj) {
        obj.photo = "";
        console.log(obj);
    }

    app.dialog = function(title, body, footer) {
        return {
            "title": title,
            "body": body,
            "footer": footer
        };
    }

    app.render_dialog = function(id, title, body, footer, dialog_obj) {
        var dialog_title, dialog_body, dialog_footer, obj = "";

        obj = dialog_obj(title, body, footer);
        App.id_or_class_name(id, 'modal-title')[0].innerHTML = obj.title;
        App.id_or_class_name(id, 'modal-body')[0].innerHTML = obj.body;
        App.id_or_class_name(id, 'modal-footer')[0].innerHTML = obj.footer;

    }

    app.load_page_functions = function(dialog_content) {
        //Process Clientele Add
        var elem_id = App.id_or_class_name('clientele-view-add');
        if (elem_id)
            elem_id.onclick = function(e) {
                App.id_or_class_name('clientele-add').click();
                App.render_dialog('aniferaz-dialog', 'Add new client', dialog_content, '', App.dialog);
                App.load_dialog_content();
            }

        //Process Clientele Edit
        var elem_id = App.id_or_class_name('clientele_view_render');
        if (elem_id)
            elem_id.onclick = function(e) {
                e.preventDefault();
                var target = e.target;
                console.log(target);
                if (target.hasAttribute('clientele-view-edit')) {
                    console.log("Attribute confirmed");
                    console.log(target.hasAttribute('clientele-view-edit'));
                    var value = {
                        'client': target.getAttribute('clientele-view-edit')
                    };

                    let postData = function(r) {
                        console.log(typeof r);
                        console.log(r);
                        console.log(r.notif);

                        if (typeof r === 'object' && r.notif) {
                            App.toast_success(r.notif_value, App.pathname);
                            App.render_dialog('aniferaz-dialog', 'Edit Client', r.dialog_content, '', App.dialog);
                            App.load_dialog_content();
                            App.id_or_class_name('clientele-add').click();

                        } else
                            App.toast_error(r.notif_value, 'EDIT CLIENT');
                    }

                    App.ajax_call("POST", "../../clientele/edit", "json", "get-page-edit", value, postData);
                }

            }


        //Show jobs Style carousel in dialog

    }

    app.load_dialog_content = function() {
        //Process Add new client info
        var elem_id = App.id_or_class_name('clientele-bio');
        if (elem_id)
            elem_id.onclick = function() {

                var elem = App.id_or_class_name('stepContent1', 'form-control'),
                    value = {};

                if (App.isempty(elem)) {
                    value.user_type = elem[0].value;
                    value.firstname = elem[1].value;
                    value.lastname = elem[2].value;
                    value.email = elem[3].value;
                    value.phone = elem[4].value;
                    value.birthday = elem[5].value;
                    value.address = elem[6].value;


                    //Load next Tab functions
                    App.next_page(value, App.add_measurment);

                    //Apply Step style
                    App.apply_step_style(1, 'aniferaz-dialog', 'step-trigger', 'step-content');

                    //App.ajax_call("POST", "../../login", "json", "login-form", value, postData);
                } else
                    App.toast_error('Do not leave any field empty', 'Empty Field(s)');
            }

        var elem_id = App.id_or_class_name('clientele-measurment');
        if (elem_id)
            elem_id.onclick = function() {
                console.log("Uploads functions");
                App.next_page(App.prev_tab_object, App.upload_photo);

                //Apply Step style
                App.apply_step_style(2, 'aniferaz-dialog', 'step-trigger', 'step-content');

                console.log("Apply Photo buttons");
                App.apply_form_button('aniferaz-dialog');
            }

    }

    app.apply_form_button = function(modal_id) {
        var footer_btn = "<button class='btn btn-outline-secondary' type='button' data-dismiss='modal' id='remove-all'>Close</button>" +
            "<button class='btn btn-outline-primary' id='clientele-photo'  data-loading='Please wait...' data-text='Process File'>Process File</button>";

        App.id_or_class_name(modal_id, 'modal-footer')[0].innerHTML = footer_btn;

        var elem_id = App.id_or_class_name('clientele-photo');
        if (elem_id)
            elem_id.onclick = function() {
                console.log("Object and Photo Save");

                let postData = function(r) {
                    console.log(typeof r);
                    console.log(r);
                    console.log(r.notif);
                    console.log(r.notif_value);

                    if (r.notif)
                        App.toast_success(r.notif_value, 'SUCCESSFUl');
                    else
                        App.toast_error(r.notif_value, 'Error');


                }

                App.ajax_call("POST", "../../clientele/add", "json", "add-client", App.prev_tab_object, postData);

            }

    }

    app.apply_step_style = function(step_no, step_id, step_trigger, step_content) {
        var elem = App.id_or_class_name(step_id, step_trigger);
        var elem_ = App.id_or_class_name(step_id, step_content);
        if (elem && step_no < elem.length)
            for (var i = 0; i <= step_no; i++) {
                var attr = elem[i].getAttribute('class').split(' ')[0],
                    attr_ = elem_[i].getAttribute('class').split(' ')[0];
                if (i == step_no) {
                    elem[step_no].setAttribute('class', attr + ' active');
                    elem_[step_no].setAttribute('class', attr_ + ' active');
                } else {
                    elem[i].setAttribute('class', attr + ' completed complete');
                    elem_[i].setAttribute('class', attr_);
                }
            }
    }

    app.clientele_view_object = {}

    app.clientele_view = function(profile, measurment) {
        return {
            "profile": profile,
            "measurment": measurment
        }
    }

    app.render_clientele_view = function(id, profile_obj, measurment_obj, obj) {
        var elem = App.id_or_class_name(id);
        var profile = App.id_or_class_name(id, 'clientele-view-profile');
        var measurment = App.id_or_class_name(id, 'clientele-view-measurment');
        var edit = App.id_or_class_name(id, 'clientele-view-edit');
        var obj = obj(profile_obj, measurment_obj);

        if (!elem) {
            console.log("Cannot render view");
            return;
        }

        if (profile) {
            profile[0].setAttribute('style', 'background-image: url(../assets/img/aniferaz/users/' + obj.profile.photo + ')');
            profile[1].innerHTML = obj.profile.fullname;
            profile[2].innerHTML = obj.profile.email;
            profile[3].innerHTML = obj.profile.birthday;
            profile[4].innerHTML = obj.profile.phone;
            profile[5].innerHTML = obj.profile.address;
        }

        if (measurment) {
            measurment[0].innerHTML = obj.measurment.weight;
            measurment[0].innerHTML = obj.measurment.height;
            measurment[0].innerHTML = obj.measurment.shoulder;
        }

        if (edit) {
            edit[0].setAttribute('clientele-view-edit', obj.profile.client);
        }
    }

    app.formOptions = function(option_data, elem_id = null, elem_class = null, needle = null) {
        var elem = App.id_or_class_name('job-style', 'job-styles');
        var options;
        elem[0].innerHTML = "";

        for (var i in option_data) {
            var option = document.createElement('option');
            option.value = option_data[i].id;
            option.innerHTML = option_data[i].name;

            elem[0].appendChild(option);
        }
    }

    app.add_job_profile = function(prev_obj) {
        //Get Style Category data
        App.select_style_category();

        //Get Job accesory
        //App.select_job_accessory();
        App.load_selected_accessory_function();

        //Process next Tab
        var elem_id = App.id_or_class_name('job-step-two');

        if (elem_id)
            elem_id.onclick = function(e) {
                var data = {};
                var elem = App.id_or_class_name('stepContent2', 'job-styles-others');

                //Prevent illegitimate tab clicks
                if (!App.job_profile_data.job_profile) {
                    App.toast_error('Make sure you click continue from the previous tab', 'CLIENT');
                    return;
                }

                if (App.id_or_class_name('stepContent2', 'job-styles')[0].value.trim() == "") {
                    App.toast_error('Select Category', 'JOB PROFILE');
                    return;
                }

                if (!App.job_profile_data.style.length) {
                    App.toast_error('Choose a style', 'JOB PROFILE');
                    return;
                }

                if (!App.job_profile_data.accessory.length) {
                    App.toast_error('Choose one or more accessory', 'JOB PROFILE');
                    return;
                }



                //if(elem && !App.isempty(elem)){
                if (elem && !App.isempty_(elem)) {
                    data.material_available = elem[0].value.trim();
                    data.material_color = elem[1].value.trim();
                    data.material_type = elem[2].value.trim();
                    data.material_code = elem[3].value.trim();
                    data.quantity = elem[4].value.trim();
                    data.completion_date = elem[5].value.trim();

                    //console.log(data);	
                    App.job_profile_data.profile = data;

                    //Load next Tab functions
                    App.job_profile_data.job_cost = true;
                    App.next_page(data, App.add_job_cost);

                    //Apply Step style
                    App.apply_step_style(2, 'aniferaz-job', 'step-trigger', 'step-content');

                } //else
                //App.toast_error('Fields cannot be empty','FIELDS');


            }
    }

    app.job_profile_data = {}

    app.load_selected_accessory_function = function() {
        var elem_ = App.id_or_class_name('select-accessory');
        App.job_profile_data.accessory = [];
        if (elem_) {
            elem_.onclick = function() {
                var elem = App.id_or_class_name('select-accessory', 'job-pix');
                if (elem) {
                    var data_elem = "",
                        data_obj = [];
                    for (var i = 0; i < elem.length; i++) {
                        data_elem = elem[i].getElementsByTagName('input')[0];
                        if (data_elem.checked)
                            data_obj.push(data_elem.getAttribute('accessory-id'));
                    }
                    //console.log(data_obj);
                    //App.job_profile_data.style = data_obj;
                    App.job_profile_data.accessory = data_obj;
                }
            }
        }
    }

    app.load_selected_style_function = function() {
        var elem_ = App.id_or_class_name('stepContent2');
        App.job_profile_data.style = [];
        if (elem_) {
            elem_.onclick = function() {
                var elem = App.id_or_class_name('select-style', 'job-pix');
                if (elem) {
                    var data_elem = "",
                        data_obj = [];
                    for (var i = 0; i < elem.length; i++) {
                        data_elem = elem[i].getElementsByTagName('input')[0];
                        if (data_elem.checked)
                            data_obj.push(data_elem.getAttribute('style-id'));
                    }
                    //console.log(data_obj);
                    App.job_profile_data.style = data_obj;
                }
            }
        }
    }

    app.load_selected_style = function(selected_style_view = "") {
        var elem_id = App.id_or_class_name('select-style');
        if (elem_id)
            elem_id.innerHTML = selected_style_view;

        //Load function for selected styles	
        App.load_selected_style_function();
    }

    app.select_style_category = function() {
        var elem_id = App.id_or_class_name('stepContent2', 'job-styles');

        if (elem_id) {
            elem_id[0].onchange = function() {
                var value = {};
                //Load default styles for changed item
                console.log("Style categoery changed");

                //Process Styles from server
                let postData = function(r) {
                    console.log(typeof r);
                    console.log(r);
                    console.log(r.notif);

                    if (typeof r === 'object' && r.notif) {
                        //App.toast_success(r.notif_value, App.pathname);

                        //Load selected style
                        App.load_selected_style(r.page_content);

                    } else
                        App.toast_error(r.notif_value, 'DATABASE ERROR');
                }

                value.category = elem_id[0].value.trim();
                if (value.category)
                    App.ajax_call("POST", "../../jobs/add", "json", "get-style", value, postData);
                else
                    App.load_selected_style();

            }
        }
    }

    app.add_job_cost = function() {
        //Log new Payments

        //Process Job Profile
        var elem_id = App.id_or_class_name('job-step-three');
        if (elem_id)
            elem_id.onclick = function(e) {
                var data = {};
                var elem = App.id_or_class_name('stepContent3', 'job-styles-cost');

                //Prevent illegitimate tab clicks
                if (!App.job_profile_data.job_cost) {
                    App.toast_error('Make sure you click continue from the previous tab', 'CLIENT');
                    return;
                }

                if (elem && !App.isempty(elem)) {
                    data.accessory_cost = elem[0].value.trim();
                    data.material_cost = elem[1].value.trim();
                    data.job_cost = elem[2].value.trim();

                    App.job_profile_data.cost = data;

                    //Process Jobs to server
                    App.process_job_to_server();


                } else
                    App.toast_error('Fields cannot be empty', 'FIELDS');

            }
    }

    app.process_job_to_server = function() {
        let postData = function(r) {
                console.log(typeof r);
                console.log(r);
                console.log(r.notif);

                if (typeof r === 'object' && r.notif) {
                    App.toast_success(r.notif_value, App.pathname);

                    //Create proceedure for Toast Confirmation to make payment
                    App.job_toast_confirm();

                } else
                    App.toast_error(r.notif_value, 'DATABASE ERROR');
            }
            //Process profile jobs to server
        var value = App.job_profile_data;
        App.ajax_call("POST", "../../jobs/add", "json", "add-job", value, postData);


    }

    app.log_payment = function(obj = {}) {
        //Process Log payment
        var elem_id = App.id_or_class_name('job-step-four');
        if (elem_id)
            elem_id.onclick = function(e) {
                var elem = App.id_or_class_name('stepContent4', 'job-styles-payment'),
                    value = {};

                //Prevent illegitimate tab clicks
                if (!App.job_profile_data.job_payment_log) {
                    App.toast_error('Make sure you click \'Add Job\' from the previous tab', 'CLIENT');
                    return;
                }

                if (!App.isempty(elem)) {
                    value.job = App.job_profile_data.selected_client.client;
                    value.amount = elem[0].value.trim();
                    value.method = elem[1].value.trim();
                    value.destination = elem[2].value.trim();
                    value.description = elem[3].value.trim();

                    let postData = function(r) {
                        console.log(typeof r);
                        console.log(r);
                        console.log(r.notif);

                        if (typeof r === 'object' && r.notif) {
                            App.toast_success(r.notif_value, App.pathname);
                            //setTimeout(function(){
                            //	location.reload();
                            //	},2000);

                            App.id_or_class_name('job-add-payment-log').innerHTML = r.page_content;
                        } else
                            App.toast_error(r.notif_value, 'LOGIN');
                    }

                    App.ajax_call("POST", "../../jobs/add", "json", "log-payment", value, postData);
                } else
                    App.toast_error('Do not leave any field empty', 'Empty Field(s)');
            }
    }

    app.render_job_completed_view = function() {
        var view = "<div class='big-error-w'>" +
            "<h1><span class='os-icon os-icon-check'></span></h1>" +
            "<h5>Job has been added</h5>" +
            "<h4>Hi! This job has been completed. Click <a href='/jobs/add'>here</a> to 'Add' new job</h4>" +
            "</div>";
        return view;
    }

    app.job_toast_confirm = function() {
        //Create proceedure for Toast Confirmation
        var Obj = (function(obj) {
            obj.do_on_yes = function() {
                //Disable job entry and move to Log Payments
                App.id_or_class_name('stepContent1').innerHTML = App.render_job_completed_view();
                App.id_or_class_name('stepContent2').innerHTML = App.render_job_completed_view();
                App.id_or_class_name('stepContent3').innerHTML = App.render_job_completed_view();

                //Load next Tab functions
                App.job_profile_data.job_payment_log = true;
                App.next_page({}, App.log_payment);

                //Apply Step style
                App.apply_step_style(3, 'aniferaz-job', 'step-trigger', 'step-content');
            }

            obj.do_on_no = function() {
                //Redirect to Job-view page
                var origin = location.origin;
                var pathname = location.pathname;
                var new_path = pathname.replace(pathname.split('/')[2], 'view');

                location.href = origin + new_path;

            }

            return obj;

        })({});
        App.toast_confirm('Do you want to proceed to log new payments?', Obj, 'LOG PAYMENTS?');
    }

    app.render_search_client = function(clients, search_needle) {
        var elem_id = App.id_or_class_name('search-client');
        if (elem_id) {
            elem_id.innerHTML = "";
            var client = "";
            for (var i = 0; i < clients.length; i++) {
                client += App.search_client_view(clients[i].fullname, clients[i].photo ? ? 'default-b.png', clients[i].client);
            }

            if (clients.length) {
                elem_id.setAttribute('class', 'row');
                elem_id.innerHTML = client;
            } else {
                elem_id.setAttribute('class', 'col-sm-12');
                elem_id.innerHTML = "<div class='col-sm-12' style='text-align: center'><span>The search content <strong>'" + search_needle + "'</strong> is not found</span></div>";
            }

        }
    }

    app.render_selected_search_client = function(selected_client) {
        var elem_id = App.id_or_class_name('stepContent1', 'selected-client');
        if (elem_id)
            elem_id[0].innerHTML = App.selected_search_client_view(selected_client.fullname, selected_client.photo ? ? 'default-b.png', selected_client.phone);
    }

    app.search_client_view = function(fullname, photo, id) {
        var view = "<div class='job-style job'>" +
            "<div class='fs-main-info'>" +
            "<div class='avatar'>" +
            "<img src='../assets/img/aniferaz/users/" + photo + "'>" +
            "</div>" +
            "</div>" +
            "<div class='fs-extra-info'>" +
            "<span>" + fullname + "</span>" +
            "<h4>" +
            "<span class='form-group'><input class='form-check-input' client-id='" + id + "' type='checkbox'></span>" +
            "</h4>" +
            "</div>" +
            "</div>";

        return view;
    }

    app.selected_search_client_view = function(fullname, photo, phone) {
        var view = "<div class='job-style job-selected'>" +
            "<div class='fs-main-info'>" +
            "<div class='avatar'>" +
            "<img src='../assets/img/aniferaz/users/" + photo + "'>" +
            "</div>" +
            "</div>" +
            "<div class='fs-extra-info'>" +
            "<span>" + fullname + "</span>" +
            "<h4>" +
            "<span class='form-group'>" + phone + "</span>" +
            "</h4>" +
            "</div>" +
            "</div>";

        return view;
    }

    app.search_client = function(clients_obj, needle) {
        var clients = [],
            new_obj = {};
        console.log(clients_obj);
        for (var i = 0; i < clients_obj.length; i++) {
            if (clients_obj[i].fullname.search(needle) >= 0 || clients_obj[i].phone.search(needle) >= 0) {
                console.log(i);
                new_obj.fullname = clients_obj[i].fullname;
                new_obj.phone = clients_obj[i].phone;
                new_obj.client = clients_obj[i].client;
                new_obj.photo = clients_obj[i].photo;
                clients.push(new_obj);
                //return clients;
            }

        }

        return clients;
    }

    app.search_client_by_id = function(clients_obj, needle_id) {
        var new_obj = {};
        console.log(clients_obj);
        for (var i = 0; i < clients_obj.length; i++) {
            if (clients_obj[i].client == needle_id) {
                new_obj.fullname = clients_obj[i].fullname;
                new_obj.phone = clients_obj[i].phone;
                new_obj.client = clients_obj[i].client;
                new_obj.photo = clients_obj[i].photo;
                //return clients;
            }

        }

        return new_obj;
    }

    app.load_job_page_functions = function() {
        if (App.pathname == '/jobs/add') {
            //Process Job entry - Select Client
            var elem_id = App.id_or_class_name('job-step-one');
            if (elem_id)
                elem_id.onclick = function(e) {
                    var data = {};
                    if (!App.job_profile_data.selected_client) {
                        App.toast_error('Make sure you select a client', 'CLIENT');
                        return;
                    }

                    //Load next Tab functions
                    App.job_profile_data.job_profile = true;
                    App.next_page(data, App.add_job_profile);

                    //Apply Step style
                    App.apply_step_style(1, 'aniferaz-job', 'step-trigger', 'step-content');
                }

            //Process Log Payment
            App.log_payment();
            App.add_job_profile();
            App.add_job_cost();

            //Process search to find client
            var elem_id = App.id_or_class_name('stepContent1', 'job-styles-search');
            if (elem_id)
                elem_id[0].onfocus = function() {
                    var el = App.id_or_class_name('stepContent1', 'job-styles-search');
                    if (el)
                        el[0].onkeyup = function() {
                            var data = el[0].value.trim(),
                                clients = App.job_profile_data.clients;
                            if (!App.isempty(el)) {
                                //App.toast_success(data, 'Search');
                                App.render_search_client(App.search_client(App.job_profile_data.clients, data), data);
                            } else
                                App.toast_error('Cannot leave this field empty', 'Search');
                        }
                    else {
                        console.log('element not found');
                        console.log(elem_id[0]);
                    }

                }


            //Process selected search client
            var elem_id = App.id_or_class_name('search-client');
            if (elem_id)
                elem_id.onclick = function(e) {
                    var target = e.target;
                    var target_elem = target.getAttribute('client-id');
                    var selected_client = App.search_client_by_id(App.job_profile_data.clients, target_elem);

                    //Update profile data object of selected client
                    App.job_profile_data.selected_client = selected_client;

                    //Render Selected client
                    App.render_selected_search_client(selected_client);

                    //Clear selected area
                    elem_id.innerHTML = "";
                    //Clear Search input field
                    App.id_or_class_name('stepContent1', 'job-styles-search')[0].value = "";
                }

        }

        if (App.pathname == '/jobs/view') {
            //Process Job edit
            var elem_id = App.id_or_class_name('job_view_render');
            if (elem_id)
                elem_id.onclick = function(e) {
                    e.preventDefault();
                    var target = e.target;
                    console.log(target);
                    if (target.hasAttribute('job-view-edit')) {
                        console.log("Attribute confirmed");
                        console.log(target.hasAttribute('job-view-edit'));
                        var value = {
                            'job': target.getAttribute('job-view-edit')
                        };

                        let postData = function(r) {
                            console.log(typeof r);
                            console.log(r);
                            console.log(r.notif);

                            if (typeof r === 'object' && r.notif) {
                                App.toast_success(r.notif_value, App.pathname);
                                App.render_dialog('aniferaz-dialog', 'Edit Job', r.dialog_content, '', App.dialog);
                                //App.load_dialog_content();
                                App.id_or_class_name('job-add').click();

                            } else
                                App.toast_error(r.notif_value, 'EDIT JOB');
                        }

                        App.ajax_call("POST", "../../jobs/view", "json", "get-job-edit", value, postData);
                    }
                }
        }
    }

    app.render_selected_job_view = function(id, job_obj) {
        var elem = App.id_or_class_name(id);
        var profile = App.id_or_class_name(id, 'job-view-profile');
        var cost = App.id_or_class_name(id, 'job-view-cost');
        var edit = App.id_or_class_name(id, 'clientele-view-edit');
        if (!elem) {
            console.log("Cannot render view");
            return;
        }

        if (profile) {
            profile[0].innerHTML = job_obj.available;
            profile[1].innerHTML = job_obj.material_color;
            profile[2].innerHTML = job_obj.material_type;
            profile[3].innerHTML = job_obj.material_code;
            profile[4].innerHTML = job_obj.quantity;
            profile[5].innerHTML = job_obj.date;
            profile[6].innerHTML = job_obj.expire;
        }

        if (cost) {
            cost[0].innerHTML = job_obj.accessory_cost;
            cost[1].innerHTML = job_obj.material_cost;
            cost[2].innerHTML = job_obj.job_cost;
        }

        if (edit) {
            edit[0].setAttribute('job-view-edit', job_obj.id);
        }
    }

    //Ajax call
    app.ajax_call = function(type, url, dataType, dataSendKey, dataSend, postData) {
        jQuery.ajax({
            type: type,
            url: url,
            dataType: dataType,
            data: dataSendKey + "=" + JSON.stringify({ "ajaxRequest": app.ajax_mode, "dataSend": dataSend }),
            //data: dataSend(key,),
            success: function(dataReceive) {
                postData(dataReceive);
            },
            error: function(xhr, ajaxoptions, thrownError) {}
        });
    }

    return app;
})({}, jQuery);

$ = jQuery;
/*
 * Prerequisite Setup
 */
window.onload = function() {
    // IziToast Settings
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

    //Load page resource
    let page_data = function() {
        console.log(App.pathname);
        let postData = function(r) {
            console.log(typeof r);
            console.log(r);
            console.log(r.notif);
            console.log(r.page_content_json);
            if (r.notif) {
                var elem = App.id_or_class_name('pageContentAjax');
                App.toast_success(r.notif_val, App.pathname);
                App.clientele_view_object = r.page_content_json;
                if (elem)
                    elem.innerHTML = r.page_content;

                App.load_page_functions(r.dialog_content);
            }
        }
        var value = {};
        if (App.pathname == '/clientele/view')
            App.ajax_call("POST", "../.." + App.pathname, "json", "load-page", value, postData);

        //Load Job Page content
        let postDataJobAdd = function(r) {
            //Clear job profile data
            App.job_profile_data = {};

            if (r.notif) {
                var elem = App.id_or_class_name('pageContentAjax');
                App.toast_success(r.notif_value, App.pathname);
                App.job_profile_data.clients = r.page_content_json;
                //if(elem)
                //elem.innerHTML = r.page_content;

                App.load_job_page_functions();
            }
        }

        if (App.pathname == '/jobs/add')
            App.ajax_call("POST", "../.." + App.pathname, "json", "load-page", value, postDataJobAdd);

        //Load Job Page content
        let postDataJobView = function(r) {
            //Clear job profile data
            App.job_view_profile = {};
            console.log(r);
            if (r.notif) {
                var elem = App.id_or_class_name('pageContentAjax');
                App.toast_success(r.notif_value, App.pathname);
                App.job_view_profile.jobs = r.page_content_json;
                console.log(r.page_content);
                if (elem)
                    elem.innerHTML = r.page_content;

                App.job_profile_data.job_payment_log = true;
                App.job_profile_data.selected_client = {};
                App.job_profile_data.selected_client.client = 8;
                App.load_job_page_functions();
            }
        }

        if (App.pathname == '/jobs/view')
            App.ajax_call("POST", "../.." + App.pathname, "json", "load-page", value, postDataJobView);
    }();

    //Login processing
    var elem_id = App.id_or_class_name('form-login');
    if (elem_id)
        elem_id.onsubmit = function(e) {
            e.preventDefault();
            var elem = document.getElementsByClassName('login-form'),
                value = {};

            if (!App.isempty(elem)) {
                value.name = elem[0].value;
                value.password = elem[1].value;

                let postData = function(r) {
                    console.log(typeof r);
                    console.log(r);
                    console.log(r.notif);

                    if (typeof r === 'object' && r.notif) {
                        App.toast_success(r.notif_value, App.pathname);
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else
                        App.toast_error(r.notif_value, 'LOGIN');
                }

                App.ajax_call("POST", "../../login", "json", "login-form", value, postData);
            } else
                App.toast_error('Do not leave any field empty', 'Empty Field(s)');
        }


    //Process  and render selected client in Clientele_render_view
    var elem_id = App.id_or_class_name('pageContentAjax');
    if (App.pathname == '/clientele/view')
        if (elem_id)
            elem_id.onclick = function(e) {
                console.log("clientele view clicked");
                var target = e.target,
                    target_value = "";
                //console.log(target);
                if (target.hasAttribute('clientele')) {
                    //console.log(target.getAttribute('clientele'));
                    target_value = target.getAttribute('clientele');
                    for (var i in App.clientele_view_object) {
                        if (App.clientele_view_object[i].client == target_value) {
                            console.log(App.clientele_view_object[i]);
                            var profile_obj = {
                                "photo": (isNaN(App.clientele_view_object[i].photo) ? App.clientele_view_object[i].photo : "default-b.png"),
                                "fullname": App.clientele_view_object[i].fullname,
                                "email": App.clientele_view_object[i].email,
                                "birthday": App.clientele_view_object[i].birthday,
                                "phone": App.clientele_view_object[i].phone,
                                "address": App.clientele_view_object[i].address,
                                "client": App.clientele_view_object[i].client
                            }
                            var measurment_obj = JSON.parse(App.clientele_view_object[i].measurment);
                            App.render_clientele_view('clientele_view_render', profile_obj, measurment_obj, App.clientele_view);

                        }


                    }
                }

            }


        //Process  and render selected Jobs in Job_render_view
    var elem_id = App.id_or_class_name('pageContentAjax');
    if (App.pathname == '/jobs/view')
        if (elem_id)
            elem_id.onclick = function(e) {
                console.log("job view clicked");
                var target = e.target,
                    target_value = "";
                //console.log(target);
                if (target.hasAttribute('job')) {
                    console.log(target.getAttribute('job'));
                    target_value = target.getAttribute('job');
                    for (var i in App.job_view_profile.jobs) {
                        if (App.job_view_profile.jobs[i].id == target_value) {
                            console.log(App.job_view_profile.jobs[i]);
                            var job_obj = {
                                    "photo": "", //(isNaN(App.clientele_view_object[i].photo)? App.clientele_view_object[i].photo :"default-b.png"),
                                    "stylename": "", //App.job_view_profile.jobs[i].stylename,
                                    "accessory": "", //App.job_view_profile.jobs[i].accessory,
                                    "available": App.job_view_profile.jobs[i].available,
                                    "material_color": App.job_view_profile.jobs[i].material_color,
                                    "material_type": App.job_view_profile.jobs[i].material_type,
                                    "material_code": App.job_view_profile.jobs[i].material_code,
                                    "quantity": App.job_view_profile.jobs[i].quantity,
                                    "date": App.job_view_profile.jobs[i].date,
                                    "expire": App.job_view_profile.jobs[i].expire,
                                    "id": App.job_view_profile.jobs[i].id,
                                    "accessory_cost": App.job_view_profile.jobs[i].accessory_cost,
                                    "material_cost": App.job_view_profile.jobs[i].material_cost,
                                    "job_cost": App.job_view_profile.jobs[i].job_cost,
                                }
                                //var measurment_obj = JSON.parse(App.clientele_view_object[i].measurment);
                            App.render_selected_job_view('job_view_render', job_obj);

                        }
                    }
                }

            }




        //Process Category Add
    var elem_id = App.id_or_class_name('job-category');
    if (elem_id)
        elem_id.onsubmit = function(e) {
            e.preventDefault();
            var elem = App.id_or_class_name('job-category', 'job-styles'),
                value = {};
            if (!App.isempty(elem)) {
                value.name = elem[0].value.trim();
                value.description = elem[1].value.trim();

                let postData = function(r) {
                    console.log(typeof r);
                    console.log(r);
                    console.log(r.notif);

                    if (typeof r === 'object' && r.notif) {
                        App.toast_success(r.notif_value, App.pathname);
                        App.id_or_class_name('job-category-view') ? function() {
                            App.id_or_class_name('job-category-view').innerHTML = r.page_content;
                            App.formOptions(r.json_content);
                        }() : App.toast_error('Cannot load content', 'ERROR');

                        console.log("Fetch Json_ content content");
                        console.log(r.json_content);
                    } else
                        App.toast_error(r.notif_value, 'JOB CATEGORY');

                }

                App.ajax_call("POST", "../.." + App.pathname, "json", "job-category", value, postData);

            } else
                App.toast_error('Do not leave any field empty', 'Empty field(s)');

        }

    //Process Job-Style Add
    var elem_id = App.id_or_class_name('job-style');
    if (elem_id)
        elem_id.onsubmit = function(e) {
            e.preventDefault();
            var elem = App.id_or_class_name('job-style', 'job-styles'),
                value = {};
            if (!App.isempty(elem)) {
                value.name = elem[1].value.trim();
                value.category = elem[0].value.trim();
                value.description = elem[2].value.trim();
                let postData = function(r) {
                    console.log(typeof r);
                    console.log(r);
                    console.log(r.notif);

                    if (typeof r === 'object' && r.notif) {
                        App.toast_success(r.notif_value, App.pathname);
                        App.id_or_class_name('job-style-view') ? function() {
                            App.id_or_class_name('job-style-view').innerHTML = r.page_content;
                        }() : App.toast_error('Cannot load content', 'ERROR');
                    } else
                        App.toast_error(r.notif_value, 'JOB STYLE');

                }

                App.ajax_call("POST", "../../job-styles", "json", "job-style", value, postData);
            } else
                App.toast_error('Do not leave any field empty', 'Empty field(s)');

        }

    //Process Job-Accessory Add
    var elem_id = App.id_or_class_name('job-accessory');
    if (elem_id)
        elem_id.onsubmit = function(e) {
            e.preventDefault();
            var elem = App.id_or_class_name('job-accessory', 'job-styles'),
                value = {};
            if (!App.isempty(elem)) {
                value.name = elem[0].value.trim();
                value.description = elem[1].value.trim();
                let postData = function(r) {
                    console.log(typeof r);
                    console.log(r);
                    console.log(r.notif);

                    if (typeof r === 'object' && r.notif) {
                        App.toast_success(r.notif_value, App.pathname);
                        App.id_or_class_name('job-accessory-view') ? function() {
                            App.id_or_class_name('job-accessory-view').innerHTML = r.page_content;
                        }() : App.toast_error('Cannot load content', 'ERROR');
                    } else
                        App.toast_error(r.notif_value, 'JOB ACCESSORY');

                }

                App.ajax_call("POST", "../../job-styles", "json", "job-accessory", value, postData);
            } else
                App.toast_error('Do not leave any field empty', 'Empty field(s)');

        }


    //Process Deleted Category/ Job-Style/ Accessory
    var elem_id = App.id_or_class_name('category-job-style');
    if (elem_id)
        elem_id.onclick = function(e) {
            var target = e.target,
                value = {};
            if (target.hasAttribute('deleteCat')) {
                value.id = target.getAttribute('deleteCat');
                value.type = 'category';
            } else if (target.hasAttribute('deleteSty')) {
                value.id = target.getAttribute('deleteSty');
                value.type = 'style';
            } else if (target.hasAttribute('deleteAcc')) {
                value.id = target.getAttribute('deleteAcc');
                value.type = 'accessory';
            }


            let postData = function(r) {
                console.log(typeof r);
                console.log(r);
                console.log(r.notif);

                if (typeof r === 'object' && r.notif) {
                    App.toast_success(r.notif_value, App.pathname);
                    (App.id_or_class_name('job-category-view') &&
                        App.id_or_class_name('job-style-view') &&
                        App.id_or_class_name('job-accessory-view')) ? function() {
                        if (value.type == 'category') {
                            App.id_or_class_name('job-category-view').innerHTML = r.page_content;
                            App.formOptions(r.json_content);
                        } else if (value.type == 'style')
                            App.id_or_class_name('job-style-view').innerHTML = r.page_content;
                        else
                            App.id_or_class_name('job-accessory-view').innerHTML = r.page_content;

                    }() : App.toast_error('Cannot load content', 'ERROR');
                } else
                    App.toast_error(r.notif_value, 'DELETE');

            }
            var postKey = value.type == 'category' ? 'delete-job-category' : (value.type == 'style' ? 'delete-job-style' : 'delete-job-accessory');
            if (value.id && value.type)
                App.ajax_call("POST", "../../job-styles", "json", postKey, value, postData);


        }



    typeof(window.setup) === 'function' && window.setup();
};