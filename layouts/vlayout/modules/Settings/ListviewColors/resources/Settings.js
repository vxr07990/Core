/* * *******************************************************************************
 * The content of this file is subject to the VTE List View Colors ("License");
 * You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is VTExperts.com
 * Portions created by VTExperts.com. are Copyright(C)VTExperts.com.
 * All Rights Reserved.
 * ****************************************************************************** */

 var Settings_ListviewColors_Js = {
    advanceFilterInstance : false,

    registerEditBtn : function() {
        var thisInstance = this;
        jQuery('.editColorButton').on('click', function(event){
            event.preventDefault();
            var url = jQuery(this).data('url');
            app.showModalWindow(null, url, function(){
                thisInstance.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(jQuery('.filterContainer'));
                jQuery(document).find('.blockOverlay').unbind('click');
                thisInstance.registerColorPicker();
            });
        });
    },

    registerDeleteBtn : function() {
        var thisInstance = this;
        jQuery('.deleteColorButton').on('click', function(event){
            event.preventDefault();
            var url = jQuery(this).data('url');
            var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
            Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(function(data) {
                var aDeferred = jQuery.Deferred();
                var progressIndicatorElement = jQuery.progressIndicator({
                    'position' : 'html',
                    'blockInfo' : {
                        'enabled' : true
                    }
                });
                AppConnector.request(url).then(
                    function(data) {
                        thisInstance.loadRecords();
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        aDeferred.resolve(data);
                    },
                    function(error,err){
                        progressIndicatorElement.progressIndicator({
                            'mode' : 'hide'
                        });
                        aDeferred.reject(error,err);
                    }
                );
                return aDeferred.promise();
                },
                function(error, err){
                }
            );
        });
    },

    registerCloseBtn : function() {
        jQuery(document).on('click', '#CustomView .ui-condition-color-closer', function(event){
            event.preventDefault();
            app.hideModalWindow();
        });
    },

    registerModuleChange : function() {
        var thisInstance = this;
        jQuery(document).on('change', '#CustomView select[name=modulename]', function(event){
            event.preventDefault();
            jQuery('#advfilterlist').val('');
            var url = 'index.php?module=ListviewColors&view=ModuleChangeAjax&parent=Settings&module_name='+jQuery(this).val();
            AppConnector.request(url).then(
                function(data) {
                    jQuery('#CustomView .vte-advancefilter').html(data);
                    var container = jQuery('#CustomView .filterContainer');
                    thisInstance.advanceFilterInstance = Vtiger_AdvanceFilter_Js.getInstance(container);
                    app.changeSelectElementView(container);
                }
            );
        });
    },

    registerColorPicker : function(){
        jQuery(document).find('input[name=text_color]').ColorPicker({
            color: '#0000ff',
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                jQuery(colpkr).css({'zIndex': '10010'});
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('input[name=text_color]').css('backgroundColor', '#' + hex);
                jQuery('input[name=text_color]').val('#' + hex);
            }
        }).bind('keyup', function(){
            jQuery(this).ColorPickerSetColor(this.value);
        });
        jQuery(document).find('input[name=bg_color]').ColorPicker({
            color: '#0000ff',
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                jQuery(colpkr).css('zIndex', '10010 !important');
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('input[name=bg_color]').css('backgroundColor', '#' + hex);
                jQuery('input[name=bg_color]').val('#' + hex);
                jQuery('.vtiger-crm-rock').css('backgroundColor', '#' + hex);
            }
        }).bind('keyup', function(){
            jQuery(this).ColorPickerSetColor(this.value);
            jQuery('.vtiger-crm-rock').css('backgroundColor', this.value);
        });
        jQuery(document).find('input[name=related_record_color]').ColorPicker({
            color: '#0000ff',
            onShow: function (colpkr) {
                jQuery(colpkr).fadeIn(500);
                jQuery(colpkr).css('zIndex', '10010 !important');
                return false;
            },
            onHide: function (colpkr) {
                jQuery(colpkr).fadeOut(500);
                return false;
            },
            onChange: function (hsb, hex, rgb) {
                jQuery('input[name=related_record_color]').css('backgroundColor', '#' + hex);
                jQuery('input[name=related_record_color]').val('#' + hex);
                jQuery('.vtiger-crm-rock').css('color', '#' + hex);
            }
        }).bind('keyup', function(){
            jQuery(this).ColorPickerSetColor(this.value);
            jQuery('.vtiger-crm-rock').css('color', this.value);
        });
    },

    registerSaveBtn : function() {
        var thisInstance = this;
        jQuery(document).on('click', '#save-condition-color', function(event){
            event.preventDefault();
            //valid condition name
            if(jQuery.trim(jQuery('input[name=condition_name]').val()) == ''){
                jQuery('input[name=condition_name]').validationEngine('showPrompt', '* This field is required' , 'error','topLeft',true);
                return;
            }
            var aDeferred = jQuery.Deferred();

            var advfilterlist = thisInstance.advanceFilterInstance.getValues();
            jQuery('#advfilterlist').val(JSON.stringify(advfilterlist));
            var form = jQuery('#CustomView');
            var formData = form.serializeFormData();
            var progressIndicatorElement = jQuery.progressIndicator({
                'position' : 'html',
                'blockInfo' : {
                    'enabled' : true
                }
            });
            AppConnector.request(formData).then(
                function(data) {
                    thisInstance.loadRecords();
                    progressIndicatorElement.progressIndicator({
                        'mode' : 'hide'
                    });
                    app.hideModalWindow();
                    aDeferred.resolve(data);
                },
                function(error,err){
                    app.hideModalWindow();
                    aDeferred.reject(error,err);
                }
            );
            return aDeferred.promise();
        });
    },

    loadRecords : function(){
        var thisInstance = this;
        var aDeferred = jQuery.Deferred();
        var url = 'index.php?module=ListviewColors&view=Settings&parent=Settings&ajax=true';
        AppConnector.request(url).then(
            function(data) {
                jQuery('.vte-listview-color tbody').html(data);
                thisInstance.registerEditBtn();
                thisInstance.registerDeleteBtn();
                aDeferred.resolve(data);
            },
            function(error,err){
                app.hideModalWindow();
                aDeferred.reject(error,err);
            }
        );
        return aDeferred.promise();
    },

    sortableRecords : function(){
        var thisInstance = this;
        var container = jQuery( ".vte-listview-color tbody" );
        container.sortable({
            handle: ".icon-move",
            cursor: "move",
            update: function( event, ui ) {
                var items = [];
                jQuery(this).find('.icon-move').each(function(index, el){
                    items.push(jQuery(el).data('record'));
                });
                //update priority
                var aDeferred = jQuery.Deferred();
                var params = {};
                params['module'] = 'ListviewColors';
                params['action'] = 'UpdatePriority';
                params['parent'] = 'Settings';
                params['items'] = items;
                AppConnector.request(params).then(
                    function(data) {
                        aDeferred.resolve(data);
                    },
                    function(error,err){
                        aDeferred.reject(error,err);
                    }
                );
                return aDeferred.promise();
            }
        });
        container.disableSelection();
    },

     unInstall : function() {
         var thisInstance = this;
         jQuery('#rel_uninstall_btn').on('click', function(){
             var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
             Vtiger_Helper_Js.showConfirmationBox({'message' : message}).then(function(data) {
                 app.showModalWindow(null, 'index.php?module=ListviewColors&action=uninstall&parent=Settings');
             });
         });
     },

    registerEvents : function() {
        this.registerEditBtn();
        this.registerDeleteBtn();
        this.registerModuleChange();
        this.registerCloseBtn();
        this.registerSaveBtn();
        this.sortableRecords();
        this.unInstall();
    }

};
jQuery(document).ready(function(){
    Settings_ListviewColors_Js.registerEvents();
});