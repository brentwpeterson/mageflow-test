/**
 * MageFlow
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageflow.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * If you wish to use the MageFlow Connect extension as part of a paid
 * service please contact licence@mageflow.com for information about
 * obtaining an appropriate licence.
 */

/**
 * MageFlow Connector Magento extension JavaScript file
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage JavaScript
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

(function (mageflow, $, undefined) {

    /**
     * Populates company select. After changing company select
     * another query to API is made to get list of this
     * company's projects
     *
     * @param {type} data
     */
//    mageflow.populateCompanies = function (data) {
//        jQuery(document).trigger(new jQuery.Event('disable_dynamic_fields'));
//        jQuery('#mageflow_connect_api_company option[value!=""]').remove();
//        jQuery(data.items).each(function (index, item) {
//            var o = jQuery('<option>');
//            o.attr('value', item.id).html(item.name);
//            jQuery('#mageflow_connect_api_company').append(o);
//        });
//        jQuery('#mageflow_connect_api_company').removeClass('disabled').removeAttr('disabled').removeAttr('readonly');
//    };
    /**
     * Populates list of projects. After selecting a project
     * a request is made to MF API to register current Magento developer
     * instance.
     *
     * @param {type} url
     */
    mageflow.getProjects = function (url, companyName) {
        var postData = {};
        postData.company_id = jQuery('#mageflow_connect_api_company').val();
        postData.company_name = companyName;
        jQuery.ajax(url, {
            type: 'GET',
            data: postData,
            success: function (response) {
                jQuery('#mageflow_connect_api_project').find('option').remove().end().append(jQuery('<option>').html('- Select project -'));
                jQuery(response.items).each(function (index, item) {
                    var o = jQuery('<option>');
                    o.attr('value', item.id).html(item.name);
                    jQuery('#mageflow_connect_api_project').append(o);
                });
                jQuery('#mageflow_connect_api_project').prop('disabled', '').prop('readonly', '').removeClass('disabled').focus();
                jQuery('#mageflow_connect_api_project').data('register_query_url', response.register_query_url);
            }
        });
    };
    /**
     * Sends Magento Instance registration query to MF API
     * @param {type} url
     */
    mageflow.registerInstance = function (url) {
        var postData = {};
        postData.mageflow_connect_api_company = jQuery('#mageflow_connect_api_company').val();
        postData.mageflow_connect_api_project = jQuery('#mageflow_connect_api_project').val();
        postData.mageflow_connect_api_instance_key = jQuery('#mageflow_connect_api_instance_key').val();
        var parameters = {
            type: 'GET',
            data: postData,
            success: function (response) {
                jQuery('#mageflow_connect_api_instance_key').val(response.instance_key);
                jQuery('#btn_instance').closest('td').addClass('success-icon');
            }
        };
        jQuery.ajax(url, parameters);
    };
    /**
     * Create OAuth stuff in Magento
     * @param {type} url
     */
    jQuery(document).on('click', '#btn_oauth', function (event) {
        var el = jQuery(event.target).closest('button');
        var url = el.data('url');
        var postData = {};
        postData.mageflow_connect_api_company = jQuery('#mageflow_connect_api_company').val();
        postData.mageflow_connect_api_project = jQuery('#mageflow_connect_api_project').val();
        postData.mageflow_connect_instance_key = jQuery('#mageflow_connect_api_instance_key').val();
        jQuery.ajax(url, {
            type: 'GET',
            data: postData,
            success: function (response) {
                jQuery('#btn_oauth').closest('td').addClass('success-icon');
            }
        });
    });

    /**
     *
     * @param {type} sender
     */
    mageflow.connect = function (sender) {
        var postData = mageflow.getCredentials();
        jQuery.ajax('/ajax.php', {
            type: 'POST',
            data: postData,
            success: function (response) {
                console.log(response);
            }
        });
    };


    mageflow.testapi = function (url) {
        jQuery.ajax(url, {
            type: 'GET',
            success: function (response) {
                if (response.statusmessage) {
                    jQuery('#api_test_status').html(response.statusmessage);
                    jQuery('#api_test_name').html(response.items.name);
                    jQuery('#api_test_email').html(response.items.email);
                } else {
                    jQuery('#api_test_status').html('Connection failed.');
                }
            }
        });
    }

    /**
     * Class init method. Just a placeholder now.
     */
    mageflow.init = function () {

    };
    mageflow.init();
}(window.mageflow = window.mageflow || {}, jQuery));

jQuery(document).on('click', '#mageflow_connect_api #btn_connect', function (e) {
    var el = jQuery(e.target).closest('button');
    var apiUrl = el.data('api-url');
    var data = {
        consumer_key: jQuery('#mageflow_connect_api_consumer_key').val(),
        consumer_secret: jQuery('#mageflow_connect_api_consumer_secret').val(),
        token: jQuery('#mageflow_connect_api_token').val(),
        token_secret: jQuery('#mageflow_connect_api_token_secret').val(),
        api_url: jQuery('#mageflow_connect_advanced_api_url').val(),
        form_key: FORM_KEY
    }
    jQuery.ajax(apiUrl, {
        type: 'POST',
        data: data,
        success: function (response) {
            if (response.status == 0) {
                alert(response.statusmessage);
            } else {
                jQuery('#mageflow_connect_api_company').find('option').remove().end().append(jQuery('<option>').html('- Select company -'));
                jQuery(response.items).each(function (index, item) {
                    jQuery('#mageflow_connect_api_company').append(
                        jQuery('<option>').attr('value', item.id).html(item.name)
                    );
                });
                jQuery('#mageflow_connect_api_company').prop('disabled', '').focus().removeClass('disabled');
                jQuery(document).on('change', '#mageflow_connect_api_company', function () {
                    var e = new Event('company_selected');
                    e.data_url = response.project_query_url;
                    jQuery(document).trigger(e);
                });
            }
        }
    });
});
/**
 * jQuery AJAX global event handlers
 */
jQuery(document).ajaxSend(function () {
    jQuery('#loading-mask').show();
});
jQuery(document).ajaxComplete(function () {
    jQuery('#loading-mask').hide();
});
/**
 * Event handlers
 */
jQuery(document).on('click', '#btn_apitest', function (e) {
    mageflow.testapi(jQuery(e.target).closest('button').data('api-url'));
});
jQuery(document).on('company_selected', function (e) {
    mageflow.getProjects(e.data_url, jQuery('#mageflow_connect_api_company option:selected').html());
});
jQuery(document).on('change', '#mageflow_connect_api_enabled', function (e) {
    var e = new jQuery.Event('validate_connect_button_status');
    e.sender = e.target;
    jQuery(document).trigger(e);
});
jQuery(document).on('blur', '.validate-connect', function (e) {
    var e = new jQuery.Event('validate_connect_button_status');
    e.sender = e.target;
    jQuery(document).trigger(e);
});
jQuery(document).on('validate_connect_button_status', function (e) {
    e.preventDefault();
    var status = 0;
    var apiEnabled = parseInt(jQuery('#mageflow_connect_api_enabled').val());
    jQuery(document).find('.form-list .validate-connect').each(function (index, item) {
        (jQuery(item).val().toString().length > 0) ? status++ : null;
    });
    if (status >= jQuery('.validate-connect').length && apiEnabled == 1) {
        jQuery('#btn_connect').removeClass('disabled');
    } else {
        jQuery('#btn_connect').addClass('disabled');
    }

});


jQuery(document).on('disable_dynamic_fields', function (event) {
    jQuery('.mageflow-disabled-field').attr('disabled', 'disabled').attr('readonly', 'readonly');
});
jQuery(document).trigger(new jQuery.Event('disable_dynamic_fields'));
jQuery(document).trigger(new jQuery.Event('validate_connect_button_status'));


jQuery(document).ready(function (event) {
    jQuery('#migrationGrid_table').find('select.action-select').each(function (index, item) {
        jQuery(item).attr('onchange', 'javascript:;');
    });

    jQuery('<input>').attr({
        type: 'hidden',
        id: 'migrationGrid_massaction-form-comment',
        name: 'comment',
        value: ''
    }).appendTo('#migrationGrid_massaction-form');
});

jQuery(document).on('change', 'select.action-select', function (e) {
    var hrefValue = e.target.value;
    var url = hrefValue.substr(hrefValue.search('http')).replace(/\\/g, '').replace('}', '').replace('"', '');
    if (url.search('push') > 0) {
        url += 'comment/' + prompt('Changeset description');
    }
    window.location = url;
});

jQuery(document).on('change', '#migrationGrid_massaction-select', function (e) {
    var hrefValue = e.target.value;

    if (hrefValue == 'push') {
        var comment = prompt('Changeset description');
        jQuery('#migrationGrid_massaction-form-comment').val(comment);

    }
});

