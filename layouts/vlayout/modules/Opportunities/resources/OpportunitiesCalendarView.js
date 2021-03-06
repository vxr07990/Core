/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/


jQuery.Class("Opportunities_CalendarView_Js", {
    currentInstance: false,
    getInstanceByView: function () {
        var view = jQuery('#currentView').val();
        var jsFileName = view + 'View';
        var moduleClassName = view + "_" + jsFileName + "_Js";
        if (typeof window[moduleClassName] != 'undefined') {
            var instance = new window[moduleClassName]();
        } else {
            instance = new Opportunities_CalendarView_Js();
        }
        return instance;
    },
    initiateCalendarFeeds: function () {
        Opportunities_CalendarView_Js.performCalendarFeedIntiate();
    }
}, {
    calendarView: false,
    calendarCreateView: false,
    //Hold the conditions for a hour format
    hourFormatConditionMapping: false,
    //Hold the saved values of calendar settings
    calendarSavedSettings: false,
    CalendarSettingsContainer: false,
    weekDaysArray: {Sunday: 0, Monday: 1, Tuesday: 2, Wednesday: 3, Thursday: 4, Friday: 5, Saturday: 6},
    calendarfeedDS: {},
    getCalendarView: function () {
        if (this.calendarView == false) {
            this.calendarView = jQuery('#calendarview');
        }
        return this.calendarView;
    },
    getCalendarSettingsForm: function () {
        return this.CalendarSettingsContainer;
    },
    getCalendarCreateView: function (params) {
        var thisInstance = this;
        var aDeferred = jQuery.Deferred();
        if (this.calendarCreateView !== false) {
            aDeferred.resolve(this.calendarCreateView.clone(true, true));
            return aDeferred.promise();
        }
        var progressInstance = jQuery.progressIndicator();
        this.loadCalendarCreateView(params).then(
                function (data) {
                    progressInstance.hide();
                    thisInstance.calendarCreateView = data;
                    aDeferred.resolve(data.clone(true, true));
                },
                function () {
                    progressInstance.hide();
                }
        );
        return aDeferred.promise();
    },
    loadCalendarCreateView: function (params) {
        var aDeferred = jQuery.Deferred();
        var quickCreateCalendarElement = jQuery('#quickCreateModules').find('[data-name="Surveys"]');
        var url = quickCreateCalendarElement.data('url');
        var name = quickCreateCalendarElement.data('name');

        var headerInstance = new Vtiger_Header_Js();
        headerInstance.getQuickCreateForm(url, name, params).then(
                function (data) {
                    aDeferred.resolve(jQuery(data));
                },
                function () {
                    aDeferred.reject();
                }
        );
        return aDeferred.promise();
    },
    fetchCalendarFeed: function (feedcheckbox) {

        var thisInstance = this;
        var type = feedcheckbox.data('calendar-sourcekey');
        this.calendarfeedDS[type] = function (start, end, callback) {
            if (feedcheckbox.not(':checked').length > 0) {
                callback([]);
                return;
            }
            feedcheckbox.attr('disabled', true);

            //Pull All Appointments
			feedcheckbox.data('calendar-feed', 'Surveys');
            var params = {
                module: 'Calendar',
                action: 'Feed',
                start: thisInstance.toDateString(start),
                end: thisInstance.toDateString(end),
                type: 'Tasks/Events',
                fieldname: feedcheckbox.data('calendar-fieldname'),
                userid: feedcheckbox.data('calendar-userid'),
                color: feedcheckbox.data('calendar-feed-color'),
                textColor: feedcheckbox.data('calendar-feed-textcolor')
            };

            var customData = feedcheckbox.data('customData');
            if (customData != undefined) {
                params = jQuery.extend(params, customData);
            }

            AppConnector.request(params).then(function (events) {
                callback(events);
                feedcheckbox.attr('disabled', false).attr('checked', true);
            },
                    function (error) {
                        //To send empty events if error occurs
                        callback([]);
                    });

        }

        this.getCalendarView().fullCalendar('addEventSource', this.calendarfeedDS[type]);
    },
	getColor: function() {
		return '#357373';

	},
    allocateColorsForAllUsers: function () {
        var calendarfeeds = jQuery('[data-calendar-feed]');
		//console.dir(this);

		var approvedColors = ["#dd7373", "#ce93d8", "#9fa8da", "#64b5f6", "#4dd0e1", "#80cdc4", "#81c784", "#dce775", "#fff176", "#ffc107", "#ffb74d", "#ff7043", "#b0bec5", "#f48fb1", "#857fcd"];
		var colorIndex = 0;

        calendarfeeds.each(function (index, element) {
            var feedUserElement = jQuery(element);
            var feedUserLabel = feedUserElement.closest('.addedCalendars').find('.label');
            var sourcekey = feedUserElement.data('calendar-sourcekey');

			var color = approvedColors[colorIndex];
			colorIndex ++;

			if(colorIndex >= (approvedColors.length)) colorIndex = 0;
			app.cacheSet(sourcekey, color);
			feedUserElement.data('calendar-feed-color', color);
			feedUserLabel.css({'background-color': color});

            var colorContrast = app.getColorContrast(color.slice(1));
            if (colorContrast == 'light') {
                var textColor = 'black'
            } else {
                textColor = 'white'
            }
            feedUserElement.data('calendar-feed-textcolor', textColor);
            feedUserLabel.css({'color': textColor});
        });

    },
    fetchAllCalendarFeeds: function (calendarfeedidx) {
        var thisInstance = this;
        var calendarfeeds = jQuery('[data-calendar-feed]');
        //TODO : see if you get all the feeds in one request
        calendarfeeds.each(function (index, element) {
            var feedcheckbox = jQuery(element);
            var disabledOnes = app.cacheGet('calendar.feeds.disabled', []);
            if (disabledOnes.indexOf(feedcheckbox.data('calendar-sourcekey')) == -1) {
                feedcheckbox.attr('checked', true);
            }
            thisInstance.fetchCalendarFeed(feedcheckbox);
        });
    },
    allocateColorsForAllActivityTypes: function () {
        var calendarfeeds = jQuery('[data-calendar-feed]');
        calendarfeeds.each(function (index, element) {
            var feedUserElement = jQuery(element);
            var feedUserLabel = feedUserElement.closest('.addedCalendars').find('.label')
            var color = feedUserElement.data('calendar-feed-color');
            var feedModule = feedUserElement.data('calendar-feed');
            var feedFieldName = feedUserElement.data('calendar-feed-fieldname');
            var sourcekey = feedModule + '_' + feedFieldName;
            if (color == '' || typeof color == 'undefined') {
                color = app.cacheGet(sourcekey);
                if (color != null) {
                } else {
                    color = '#' + (0x1000000 + (Math.random()) * 0xffffff).toString(16).substr(1, 6);
                    app.cacheSet(sourcekey, color);
                }
                feedUserElement.data('calendar-feed-color', color);
                feedUserLabel.css({'background-color': color});
            }
            var colorContrast = app.getColorContrast(color.slice(1));
            if (colorContrast == 'light') {
                var textColor = 'black'
            } else {
                textColor = 'white'
            }
            feedUserElement.data('calendar-feed-textcolor', textColor);
            feedUserLabel.css({'color': textColor});
        });
    },
    fetchAllEvents: function () {
        var thisInstance = this;
        var result = this.getAllUserColors();
        var params = {
            module: 'Calendar',
            action: 'Feed',
            start: thisInstance.toDateString(thisInstance.getCalendarView().fullCalendar('getView').visStart),
            end: thisInstance.toDateString(thisInstance.getCalendarView().fullCalendar('getView').visEnd),
            type: 'MultipleSurveys',
            mapping: result
        }

        AppConnector.request(params).then(function (multipleEvents) {
            thisInstance.multipleEvents = multipleEvents;
            thisInstance.fetchAllCalendarFeeds();
        },
                function (error) {

                });


    },
    getAllUserColors: function () {
        var result = {};
        var calendarfeeds = jQuery('[data-calendar-feed]');

        calendarfeeds.each(function (index, element) {
            var feedcheckbox = jQuery(element);
            var disabledOnes = app.cacheGet('calendar.feeds.disabled', []);
            if (disabledOnes.indexOf(feedcheckbox.data('calendar-sourcekey')) == -1) {
                feedcheckbox.attr('checked', true);
                var id = feedcheckbox.data('calendar-userid');
                result[id] = feedcheckbox.data('calendar-feed-color') + ',' + feedcheckbox.data('calendar-feed-textcolor');
            }
        });

        return result;
    },
    toDateString: function (date) {
        if(typeof date == 'undefined') {
            return '';
        }
        var d = date.getDate();
        var m = date.getMonth() + 1;
        var y = date.getFullYear();

        d = (d <= 9) ? ("0" + d) : d;
        m = (m <= 9) ? ("0" + m) : m;
        return y + "-" + m + "-" + d;
    },
    performCalendarFeedIntiate: function () {
        //this.allocateColorsForAllActivityTypes(); - Single User Mode
        this.allocateColorsForAllUsers();
        this.registerCalendarFeedChange();
        this.fetchAllEvents(); // Share calendar view
        //this.fetchAllCalendarFeeds(); - Single User Mode
        this.registerEventForEditUserCalendar();
        this.registerEventForDeleteUserCalendar();
    },
    registerCalendarFeedChange: function () {
        var thisInstance = this;
        jQuery('#calendarview-feeds').on('change', '[data-calendar-feed]', function (e) {
            var currentTarget = $(e.currentTarget);
            var type = currentTarget.data('calendar-sourcekey');
            if (currentTarget.is(':checked')) {
                // NOTE: We are getting cache data fresh - as it shared between browser tabs
                var disabledOnes = app.cacheGet('calendar.feeds.disabled', []);
                // http://stackoverflow.com/a/3596096
                disabledOnes = jQuery.grep(disabledOnes, function (value) {
                    return value != type;
                });
                app.cacheSet('calendar.feeds.disabled', disabledOnes);
                if (!thisInstance.calendarfeedDS[type]) {
                    thisInstance.fetchAllCalendarFeeds();
                }
                thisInstance.getCalendarView().fullCalendar('addEventSource', thisInstance.calendarfeedDS[type]);
            } else {
                // NOTE: We are getting cache data fresh - as it shared between browser tabs
                var disabledOnes = app.cacheGet('calendar.feeds.disabled', []);
                if (disabledOnes.indexOf(type) == -1)
                    disabledOnes.push(type);
                app.cacheSet('calendar.feeds.disabled', disabledOnes);
                thisInstance.getCalendarView().fullCalendar('removeEventSource', thisInstance.calendarfeedDS[type]);
            }
        });
    },
    vgsSlotClick: function (start, ends, jsEvent, view) {
        var thisInstance = this;
        var detailInstance = new Vtiger_Detail_Js();

        var referenceModuleName = "Surveys";
        var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="' + referenceModuleName + '"]');
        var recordId = detailInstance.getRecordId();
        var module = app.getModuleName();

        if (quickCreateNode.length <= 0) {
            Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED'))
        }
        var fieldName = detailInstance.referenceFieldNames[module];

        var customParams = {};
        customParams[fieldName] = recordId;


        var dateFormat = $('#date_format').val();
        var startDateInstance = Date.parse(start);
        var startDateString = app.getDateInVtigerFormat(dateFormat, startDateInstance);
        var startTimeString = startDateInstance.toString('hh:mm tt');
        var endDateInstance = Date.parse(ends);
        var endDateString = app.getDateInVtigerFormat(dateFormat, endDateInstance);
        var endTimeString = endDateInstance.toString('hh:mm tt');

        var sourceModule = jQuery('[name="module"]').val();
        var record = jQuery('#recordId').val();

        if(jQuery('#contact_id_value_td').find('span.value').find('a').length){
		  var contactId = jQuery('#contact_id_value_td').find('span.value').find('a').attr('href').split('=').pop();
        }
        else {
            var contactId = null;
        }

		var potentialLabel = jQuery('#potentialname_value_td').find('span.value').html();
        if (!potentialLabel) {
            //@NOTE: made better! TOTALLY BETTER!
            potentialLabel = jQuery('.potentialname').html();
        }
		var contactLabel = jQuery('#contact_id_value_td').find('span.value').find('a').html();

        var fullFormUrl = 'sourceModule=' + sourceModule + '&sourceRecord=' + record + '&relationOperation=true';
        var preQuickCreateSave = function (data) {
            detailInstance.addElementsToQuickCreateForCreatingRelation(data, module, recordId);

            jQuery('[name="survey_date"]').val(startDateString);
            //jQuery('[name="due_date"]').val(endDateString);
            jQuery('[name="survey_time"]').timepicker('setTime', startDateInstance);
            jQuery('[name="survey_end_time"]').timepicker('setTime', endDateInstance);

			var obj = {id:recordId, name:potentialLabel, suppress:true};
			//@NOTE: This pulls this into the quick create popup..

			thisInstance.setReferenceFieldValue(jQuery('[name="potential_id"]').closest('td'), obj);

			var obj = {id:contactId, name:contactLabel, suppress:true};
			thisInstance.setReferenceFieldValue(jQuery('[name="contact_id"]').closest('td'), obj);

            //var taskGoToFullFormButton = data.find('[class^="CalendarQuikcCreateContents"]').find('#goToFullForm');
            //var eventsGoToFullFormButton = data.find('[class^="EventsQuikcCreateContents"]').find('#goToFullForm');
            //var taskFullFormUrl = taskGoToFullFormButton.data('editViewUrl') + "&" + fullFormUrl;
            //var eventsFullFormUrl = eventsGoToFullFormButton.data('editViewUrl') + "&" + fullFormUrl;
            //taskGoToFullFormButton.data('editViewUrl', taskFullFormUrl);
            //eventsGoToFullFormButton.data('editViewUrl', eventsFullFormUrl);
        }

        var callbackFunction = function () {
            thisInstance.getCalendarView().fullCalendar('refetchEvents');
        }

        var QuickCreateParams = {};
        QuickCreateParams['callbackPostShown'] = preQuickCreateSave;
        QuickCreateParams['callbackFunction'] = callbackFunction;
        QuickCreateParams['data'] = customParams;
        QuickCreateParams['noCache'] = false;
        quickCreateNode.trigger('click', QuickCreateParams);

    },
	setReferenceFieldValue : function(container, params) {
		var sourceField = container.find('input[class="sourceField"]').attr('name');
		var fieldElement = container.find('input[name="'+sourceField+'"]');
		var sourceFieldDisplay = sourceField+"_display";
		var fieldDisplayElement = container.find('input[name="'+sourceFieldDisplay+'"]');
		var popupReferenceModule = container.find('input[name="popupReferenceModule"]').val();

		var selectedName = params.name;
		var id = params.id;

		fieldElement.val(id)
		fieldDisplayElement.val(selectedName).attr('readonly',true);
		if(!params.suppress) {
			fieldElement.trigger(Vtiger_Edit_Js.referenceSelectionEvent, {'source_module' : popupReferenceModule, 'record' : id, 'selectedName' : selectedName});
		}

		fieldDisplayElement.validationEngine('closePrompt',fieldDisplayElement);
		fieldElement.trigger('change');
	},
    addCallMeetingIcons: function (event, element) {
        var activityType = event.activitytype;
        if (activityType == 'undefined')
            return;
        //imgContainer is event time div in week and day view and fc-event-inner in month view
        var imgContainer = element.find('.fc-event-head').length ? element.find('.fc-event-time') : element.find('div.fc-event-inner');
        if (activityType == 'Call')
            imgContainer.prepend('&nbsp<img width="13px" title="(call)" alt="(call)" src="layouts/vlayout/skins/images/Call_white.png">&nbsp');
        if (activityType == 'Meeting')
            imgContainer.prepend('&nbsp<img width="14px" title="(meeting)" alt="(meeting)" src="layouts/vlayout/skins/images/Meeting_white.png">&nbsp');
    },
    /**
     *Function : strikes out events and tasks with status Held and Completed
     */
    strikeoutCompletedEventsTasks: function (event, element, view) {
        var activityType = event.activitytype;
        var title = '', titleStriked = '', target = '';
        var status = event.status;
        if (activityType === 'Task') {
            if (status === 'Completed') {
                title = event.title;
                titleStriked = title.strike();
                target = element.find('.fc-event-title');
                target.html(titleStriked);
            }
        }
        else {
            //Item redered is an event
            if (status === 'Held') {
                //Full calendar places title along with time for small duration events
                if (!element.find('.fc-event-title').length) {
                    target = element.find('.fc-event-time');
                    title = target.html();
                    titleStriked = title.strike();
                }
                else {
                    title = event.title;
                    titleStriked = title.strike();
                    target = element.find('.fc-event-title');
                }
                target.html(titleStriked);
            }
        }
    },
    registerEventDelete: function (targetElement, calEvent) {
        var thisInstance = this;
        var recordId = calEvent.id;
        targetElement.find('.delete').click(function (e) {
            var message = app.vtranslate('LBL_DELETE_CONFIRMATION');
            Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(
                    function (e) {
                        //Confirmed to delete
                        var params = {
                            "module": "Calendar",
                            "action": "DeleteAjax",
                            "record": recordId
                        }
                        AppConnector.request(params).then(function (data) {
                            if (data.success) {
                                thisInstance.getCalendarView().fullCalendar('removeEvents', calEvent.id);
                                var param = {text: app.vtranslate('JS_RECORD_DELETED')};
                                Vtiger_Helper_Js.showMessage(param);
                            } else {
                                var params = {
                                    text: app.vtranslate('JS_NO_DELETE_PERMISSION')
                                }
                                Vtiger_Helper_Js.showPnotify(params);
                            }

                        });
                    },
                    function (error, err) {
                        e.preventDefault();
                        return false;
                    });
        });
    },
    registerCalendar: function (customConfig) {
        var thisInstance = this;
        var calendarview = this.getCalendarView();
        //User preferred default view
        var userDefaultActivityView = jQuery('#activity_view').val();
        if (userDefaultActivityView == 'Today') {
            userDefaultActivityView = 'agendaDay';
        } else if (userDefaultActivityView == 'This Week') {
            userDefaultActivityView = 'agendaWeek';
        } else {
            userDefaultActivityView = 'month';
        }

        //Default time format
        var userDefaultTimeFormat = jQuery('#time_format').val();
        if (userDefaultTimeFormat == 24) {
            userDefaultTimeFormat = 'H(:mm)';
        } else {
            userDefaultTimeFormat = 'h(:mm)tt';
        }

        //Default first day of the week
        var defaultFirstDay = jQuery('#start_day').val();
        var convertedFirstDay = thisInstance.weekDaysArray[defaultFirstDay];
        //Default first hour of the day
        var defaultFirstHour = jQuery('#start_hour').val();
        var explodedTime = defaultFirstHour.split(':');
        defaultFirstHour = explodedTime['0'];
        //Date format in agenda view must respect user preference
        var dateFormat = jQuery('#date_format').val();
        //Converting to fullcalendar accepting date format
        monthPos = dateFormat.search("mm");
        datePos = dateFormat.search("dd");
        if (monthPos < datePos)
            dateFormat = "M/d";
        else
            dateFormat = "d/M";
        var config = {
            header: {
                left: 'month,agendaWeek,agendaDay',
                center: 'title today',
                right: 'prev,next'
            },
            columnFormat: {
                month: 'ddd',
                week: 'ddd ' + dateFormat,
                day: 'dddd ' + dateFormat
            },
            height: 600,
            timeFormat: userDefaultTimeFormat + '{ - ' + userDefaultTimeFormat + '}',
            axisFormat: userDefaultTimeFormat,
            firstHour: defaultFirstHour,
            firstDay: convertedFirstDay,
            defaultView: userDefaultActivityView,
            editable: true,
            selectable: true,
            selectHelper: true,
            select: function (start, end, jsEvent, view) {

                thisInstance.vgsSlotClick(start, end, jsEvent, view);
            },
            minTime:'06:00:00',
            maxTime: '21:00:00',
            businessHours: {
                start: '6:00', // a start time (10am in this example)
                end: '21:00', // an end time (6pm in this example)

            },
            slotMinutes: 30,
            defaultEventMinutes: 0,
            monthNames: [app.vtranslate('LBL_JANUARY'), app.vtranslate('LBL_FEBRUARY'), app.vtranslate('LBL_MARCH'),
                app.vtranslate('LBL_APRIL'), app.vtranslate('LBL_MAY'), app.vtranslate('LBL_JUNE'), app.vtranslate('LBL_JULY'),
                app.vtranslate('LBL_AUGUST'), app.vtranslate('LBL_SEPTEMBER'), app.vtranslate('LBL_OCTOBER'),
                app.vtranslate('LBL_NOVEMBER'), app.vtranslate('LBL_DECEMBER')],
            monthNamesShort: [app.vtranslate('LBL_JAN'), app.vtranslate('LBL_FEB'), app.vtranslate('LBL_MAR'),
                app.vtranslate('LBL_APR'), app.vtranslate('LBL_MAY'), app.vtranslate('LBL_JUN'), app.vtranslate('LBL_JUL'),
                app.vtranslate('LBL_AUG'), app.vtranslate('LBL_SEP'), app.vtranslate('LBL_OCT'), app.vtranslate('LBL_NOV'),
                app.vtranslate('LBL_DEC')],
            dayNames: [app.vtranslate('LBL_SUNDAY'), app.vtranslate('LBL_MONDAY'), app.vtranslate('LBL_TUESDAY'),
                app.vtranslate('LBL_WEDNESDAY'), app.vtranslate('LBL_THURSDAY'), app.vtranslate('LBL_FRIDAY'),
                app.vtranslate('LBL_SATURDAY')],
            dayNamesShort: [app.vtranslate('LBL_SUN'), app.vtranslate('LBL_MON'), app.vtranslate('LBL_TUE'),
                app.vtranslate('LBL_WED'), app.vtranslate('LBL_THU'), app.vtranslate('LBL_FRI'),
                app.vtranslate('LBL_SAT')],
            buttonText: {
                today: app.vtranslate('LBL_TODAY'),
                month: app.vtranslate('LBL_MONTH'),
                week: app.vtranslate('LBL_WEEK'),
                day: app.vtranslate('LBL_DAY')
            },
            allDayText: app.vtranslate('LBL_ALL_DAY'),
            /*dayClick: function (date, allDay, jsEvent, view) {
             thisInstance.dayClick(date, allDay, jsEvent, view);
             },*/
            eventAfterRender: function (event, element, view) {
                thisInstance.addCallMeetingIcons(event, element);
                thisInstance.strikeoutCompletedEventsTasks(event, element, view);
                /*
                 *Setting calendar view height to large value for week and day view to
                 *avoid loss of display when more number of allday tasks are available
                 **/
                if (view.name === 'agendaWeek' || view.name === 'agendaDay') {
                    var allDayDiv = jQuery('.fc-view-' + view.name).find('.fc-agenda-divider').prev();
                    if (allDayDiv.height() > 350)
                        view.setHeight(600000);
                }
            },
            eventResize: function (event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view) {
                if (event.module != 'Calendar' && event.module != 'Events') {
                    revertFunc();
                    return;
                }
                var params = {
                    module: 'Calendar',
                    action: 'DragDropAjax',
                    mode: 'updateDeltaOnResize',
                    id: event.id,
                    activitytype: event.activitytype,
                    dayDelta: dayDelta,
                    minuteDelta: minuteDelta,
                    view: view.name
                }
                AppConnector.request(params).then(function (data) {
                    var response = JSON.parse(data);
                    if (!response['result'].ispermitted) {
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_EDIT_PERMISSION'));
                        revertFunc();
                    }
                    if (response['result'].error)
                        revertFunc();
                });
            },
            eventDrop: function (event, dayDelta, minuteDelta, allDay, revertFunc, jsEvent, ui, view) {
                if (event.module != 'Calendar' && event.module != 'Events') {
                    revertFunc();
                    return;
                }
                if ((allDay && event.activitytype != 'Task') || (!allDay && event.activitytype === 'Task')) {
                    revertFunc();
                    return;
                }
                var params = {
                    module: 'Calendar',
                    action: 'DragDropAjax',
                    mode: 'updateDeltaOnDrop',
                    id: event.id,
                    activitytype: event.activitytype,
                    dayDelta: dayDelta,
                    minuteDelta: minuteDelta,
                    view: view.name
                }
                AppConnector.request(params).then(function (data) {
                    var response = JSON.parse(data);
                    if (!response['result'].ispermitted) {
                        Vtiger_Helper_Js.showPnotify(app.vtranslate('JS_NO_EDIT_PERMISSION'));
                        revertFunc();
                    }
                });
            },
            eventMouseover: function (calEvent, jsEvent, view) {
                jQuery(this).css('z-index', '10');
                jQuery(this).data('height', jQuery(this).height());
                jQuery(this).css('height', 'auto');
                var targetElement = jQuery(this).find('.fc-event-time');
                var trashElement = jQuery(this).find('a.delete');
                if (!trashElement.length) {
                    if (!targetElement.length) {
                        targetElement = jQuery(this).find('.fc-event-title');
                        targetElement.append('<a class="delete" style="position:absolute;right:1px;" href="javascript:void(0)"><i class="icon-trash icon-white"></i></a>');
                    }
                    else {
                        if (view.name == 'month')
                            targetElement = jQuery(this).find('.fc-event-inner');
                        targetElement.append('<a class="delete" style="position:absolute;right:1px;" href="javascript:void(0)"><i class="icon-trash icon-white"></i></a>');
                    }
                    thisInstance.registerEventDelete(targetElement, calEvent);
                }
                else {
                    trashElement.removeClass('hide');
                }
            },
            eventMouseout: function (calEvent, jsEvent, view) {
                jQuery(this).css('z-index', '8');
                jQuery(this).find('.delete').addClass('hide');
                jQuery(this).css('height', jQuery(this).data('height') + 'px');
            }
        }
        if (typeof customConfig != 'undefined') {
            config = jQuery.extend(config, customConfig);
        }
        calendarview.fullCalendar(config);
        //To create custom button to create event or task
        jQuery('<span class="pull-left"><button class="btn addButton calAddButton">' + app.vtranslate('LBL_ADD_EVENT_TASK') + '</button></span>')
                .prependTo(calendarview.find('.fc-header .fc-header-right')).on('click', 'button', function (e) {
            thisInstance.getCalendarCreateView().then(function (data) {
                var headerInstance = new Vtiger_Header_Js();
                headerInstance.handleQuickCreateData(data, {callbackFunction: function (data) {
                        thisInstance.addCalendarEvent(data.result);
                    }});
            });
        })
        jQuery('<span class="pull-right marginLeft5px"><button class="btn"><i id="calendarSettings" class="icon-cog"></i></button></span>')
                .prependTo(calendarview.find('.fc-header .fc-header-right')).on('click', 'button', function (e) {
            var params = {
                module: 'Calendar',
                view: 'Calendar',
                mode: 'Settings'
            }
            var progressIndicatorInstance = jQuery.progressIndicator({
            });
            AppConnector.request(params).then(function (data) {
                var callBack = function (data) {
                    thisInstance.CalendarSettingsContainer = jQuery(data);
                    app.showScrollBar(jQuery('div[name="contents"]', data), {'height': '400px'});
                    //register all select2 Elements
                    app.showSelect2ElementView(jQuery(data).find('select.select2'));
                    progressIndicatorInstance.hide();
                    thisInstance.hourFormatConditionMapping = jQuery('input[name="timeFormatOptions"]', data).data('value');
                    thisInstance.changeStartHourValuesEvent();
                    thisInstance.changeCalendarSharingType(data);
                }
                app.showModalWindow({'data': data, 'cb': callBack});
            });
        })

    },
    changeCalendarSharingType: function (data) {
        var selectedUsersContainer = app.getSelect2ElementFromSelect(jQuery('#selectedUsers', data));
        if (jQuery('#selectedUsers').is(':checked')) {
            selectedUsersContainer.attr('style', 'display:block;width:90%;');
        }
        jQuery('[name="sharedtype"]').on('change', function (e) {
            var sharingType = jQuery(e.currentTarget).data('sharingtype');
            if (sharingType == 'selectedusers') {
                selectedUsersContainer.show();
                selectedUsersContainer.attr('style', 'display:block;width:90%;');
            } else {
                selectedUsersContainer.hide();
            }
        });
    },
    changeStartHourValuesEvent: function (e) {
        var form = this.getCalendarSettingsForm();
        var thisInstance = this;
        form.on('click', 'input[name="hour_format"]', function (e) {
            var hourFormatVal = jQuery(e.currentTarget).val();
            var startHourElement = jQuery('select[name="start_hour"]', form);
            var conditionSelected = startHourElement.val();
            var list = thisInstance.hourFormatConditionMapping['hour_format'][hourFormatVal]['start_hour'];
            var options = '';
            for (var key in list) {
                //IE Browser consider the prototype properties also, it should consider has own properties only.
                if (list.hasOwnProperty(key)) {
                    var conditionValue = list[key];
                    options += '<option value="' + key + '"';
                    if (key == conditionSelected) {
                        options += ' selected="selected" ';
                    }
                    options += '>' + conditionValue + '</option>';
                }
            }
            startHourElement.html(options).trigger("change");
        });
    },
    isAllowedToAddCalendarEvent: function (calendarDetails) {
        if (typeof calendarDetails == 'undefined') {
            return false;
        }
        if (typeof calendarDetails.activitytype == 'undefined') {
            return false;
        }
        if (typeof calendarDetails.activitytype.value == 'undefined') {
            return false;
        }
        var activityType = calendarDetails.activitytype.value;
        if (activityType == 'Calendar' && jQuery('[data-calendar-feed="Calendar"]').is(':checked')) {
            return true;
        } else if (jQuery('[data-calendar-feed="Events"]').is(':checked')) {
            return true;
        } else {
            return false;
        }
    },
    addCalendarEvent: function (calendarDetails) {
        //If type is not shown then dont render the created event
        var isAllowed = this.isAllowedToAddCalendarEvent(calendarDetails);
        if (isAllowed == false)
            return;
        var eventObject = {};
        eventObject.id = calendarDetails._recordId;
        eventObject.title = calendarDetails.subject.display_value;
        var startDate = Date.parse(calendarDetails.date_start.calendar_display_value);
        eventObject.start = startDate.toString();
        var endDate = Date.parse(calendarDetails.due_date.calendar_display_value);
        var assignedUserId = calendarDetails.assigned_user_id.value;
        eventObject.end = endDate.toString();
        eventObject.url = 'index.php?module=Surveys&view=Detail&record=' + calendarDetails._recordId;
        if (calendarDetails.activitytype.value == 'Task') {
            var color = jQuery('[data-calendar-feed="Calendar"]').data('calendar-feed-color');
            var textColor = jQuery('[data-calendar-feed="Calendar"]').data('calendar-feed-textcolor');
            eventObject.allDay = true;
            eventObject.activitytype = calendarDetails.activitytype.value;
            eventObject.status = calendarDetails.taskstatus.value;
        } else {
            var userElement = jQuery('[data-calendar-userid=' + assignedUserId + ']');
            if (userElement.length > 0) {
                var color = jQuery('[data-calendar-userid=' + assignedUserId + ']').data('calendar-feed-color');
                var textColor = jQuery('[data-calendar-userid=' + assignedUserId + ']').data('calendar-feed-textcolor');
            } else {
                var color = jQuery('[data-calendar-feed="Events"]').data('calendar-feed-color');
                var textColor = jQuery('[data-calendar-feed="Events"]').data('calendar-feed-textcolor');
            }

            eventObject.activitytype = calendarDetails.activitytype.value;
            eventObject.status = calendarDetails.eventstatus.value;
            eventObject.allDay = false;
        }
        eventObject.color = color;
        eventObject.textColor = textColor;
        this.getCalendarView().fullCalendar('renderEvent', eventObject);
    },
    loadSharedCalendarUsers: function () {
        var instance = this;
        var url = 'module=Opportunities&action=SharedCalendarUserList';

        var params = {
            "type": "GET", "url": "index.php",
            "dataType": "html", "data": url
        }
        AppConnector.request(params).then(function (data) {
            var response = JSON.parse(data);
            if (response['success']) {
                $('#calendarview-feeds').html('');
                $('#calendarview-feeds').append(response['result']);
                instance.performCalendarFeedIntiate();
            }

        }
        );
    },
    restoreActivityTypesWidgetState: function () {
        var key = 'Calendar_sideBar_LBL_ACTIVITY_TYPES';
        var value = app.cacheGet(key);
        var widgetContainer = jQuery("#Calendar_sideBar_LBL_ACTIVITY_TYPES");
        if (value == 0) {
            Vtiger_Index_Js.loadWidgets(widgetContainer, false);
        }
        else {
            Vtiger_Index_Js.loadWidgets(widgetContainer);
        }
    },
    /**
     * Function to register event for add calendar view
     */
    registerEventForAddCalendarView: function () {
        var thisInstance = this;
        jQuery('[data-label="LBL_ADDED_CALENDARS"],[data-label="LBL_ACTIVITY_TYPES"]').find('.addCalendarView').click(function (e) {

            //To stop the accordion default behaviour when click on add icon
            e.stopPropagation();
            var currentTarget = jQuery(e.currentTarget);
            thisInstance.showAddUserCalendarModal(currentTarget);
            /*if (jQuery('#calendarview-feeds').find('.invisibleCalendarViews').val() == 'true') {
                thisInstance.showAddUserCalendarModal(currentTarget);
            } else {
                Vtiger_Helper_Js.showPnotify({text: app.vtranslate('JS_NO_CALENDAR_VIEWS_TO_ADD')});
            }*/
        });
    },
    /**
     * Function to register event for delete user calendar
     */
    registerEventForDeleteUserCalendar: function () {
        var thisInstance = this;
        var calendarView = jQuery('#calendarview-feeds');
        calendarView.on('click','.deleteCalendarView', function (e) {
            e.preventDefault();
            var currentTarget = jQuery(e.currentTarget);
            var feedcheckbox = currentTarget.closest('.addedCalendars').find('[data-calendar-feed]');
            var message = app.vtranslate('JS_CALENDAR_VIEW_DELETE_CONFIRMATION');
            Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(function (data) {
                thisInstance.deleteCalendarView(feedcheckbox).then(function () {
                    var params = {
                        text: app.vtranslate('JS_CALENDAR_VIEW_DELETED_SUCCESSFULLY'),
                        type: 'info'
                    };
                    Vtiger_Helper_Js.showPnotify(params);
                });
            },
                    function (error, err) {
                    }
            );
        })
    },
    /**
     * Function used to delete calendar view
     */
    deleteCalendarView: function (feedcheckbox) {
        var aDeferred = jQuery.Deferred();
        var thisInstance = this;
        var params = {
            module: 'Opportunities',
            action: 'SharedCalendarUserList',
            mode: 'deleteCalendarView',
            viewfieldname: feedcheckbox.data('calendar-userid')
        }

        AppConnector.request(params).then(function (response) {
            var result = response['result'];
            feedcheckbox.closest('.addedCalendars').remove();
            //After delete user reset accodion height to auto
            thisInstance.resetAccordionHeight();
            //Remove the events of deleted user in shared calendar feed
            thisInstance.getCalendarView().fullCalendar('removeEventSource', thisInstance.calendarfeedDS[feedcheckbox.data('calendar-sourcekey')]);
            aDeferred.resolve();
        },
                function (error) {
                    aDeferred.reject();
                });
        return aDeferred.promise();
    },
    resetAccordionHeight: function () {
        var accordionContainer = jQuery('[name="calendarViewTypes"]').parent();
        if (accordionContainer.hasClass('in')) {
            accordionContainer.css('height', 'auto');
        }
    },
    /**
     * Function to register event for edit user calendar color
     */
    registerEventForEditUserCalendar: function () {
        var thisInstance = this;
        var parentElement = jQuery('#calendarview-feeds');
        parentElement.on('click', '.editCalendarColor', function (e) {
            e.preventDefault();
            var currentTarget = jQuery(e.currentTarget);
            var addedCalendarEle = currentTarget.closest('.addedCalendars');
            var feedUserEle = addedCalendarEle.find('[data-calendar-feed]');
            var editCalendarViewsList = jQuery('#calendarview-feeds').find('.editCalendarViewsList');
            var selectElement = editCalendarViewsList.find('[name="editingUsersList"]');
            selectElement.find('option:selected').removeAttr('selected');
            selectElement.find('option[value="' + feedUserEle.data('calendar-fieldname') + '"]').attr('selected', true);
            thisInstance.showAddUserCalendarModal(currentTarget);
        })
    },
    /**
     * Function to show add calendar modal
     */
    showAddUserCalendarModal: function (currentEle) {
        var thisInstance = this;
        /*var addCalendarModal = jQuery('#calendarview-feeds').parent('.quickWidget').find('.addViewsToCalendar');
        console.log(currentEle);
        console.log(addCalendarModal);
        var clonedContainer = addCalendarModal.clone(true, true);
        clonedContainer.find('select[name="usersCalendarList"]').addClass('select2');*/
        var callBackFunction = function (data) {
            data.find('.addViewsToCalendar').removeClass('hide');
            var selectedUserColor = data.find('.selectedUserColor');
            var selectedUser = data.find('.selectedUser');
            var selectedViewModule = data.find('.selectedViewModule');
            var addCalendarViewsList = data.find('.addCalendarViewsList');
            var editCalendarViewsList = data.find('.editCalendarViewsList');
            addCalendarViewsList.removeClass('hide');
            editCalendarViewsList.addClass('hide');
            data.find('.userCalendarMode').val('add');
            //while adding new calendar view set the random color to the color picker
            var randomColor = '#' + (0x1000000 + (Math.random()) * 0xffffff).toString(16).substr(1, 6);
            selectedUserColor.val(randomColor);
            //color picker params for add calendar view
            var customParams = {
                color: randomColor
            };

            //register color picker
            var params = {
                flat: true,
                onChange: function (hsb, hex, rgb) {
                    var selectedColor = '#' + hex;
                    selectedUserColor.val(selectedColor);
                }
            };
            if (typeof customParams != 'undefined') {
                params = jQuery.extend(params, customParams);
            }
            data.find('.calendarColorPicker').ColorPicker(params);
            //save the user calendar with color
            data.find('[name="saveButton"]').click(function (e) {
                var selectElement = data.find('select[name="usersCalendarList"]');
                selectedUser.val(selectElement.val()).attr('data-username', selectElement.find('option:selected').text());
                selectedViewModule.val(selectElement.find('option:selected').data('viewmodule'));
                thisInstance.saveUserCalendar(data, currentEle);
            });
        }

        var params= {
            'module' : 'Opportunities',
            'view' : 'AddCalendarView',
            'mode' : 'getSharedUsersList'
        };
        AppConnector.request(params).then(function (data) {
            app.showModalWindow(data, function (data) {
                if (typeof callBackFunction == 'function') {
                    callBackFunction(data);
                }
            }, {'width': '1000px'});
        });
    },
    /**
     * Function to register change event for users list select element in edit user calendar modal
     */
    registerViewsListChangeEvent: function (data) {
        var parentElement = jQuery('#calendarview-feeds');
        var selectElement = data.find('[name="editingUsersList"]');
        var selectedUserColor = data.find('.selectedUserColor');
        var selectedViewModule = data.find('.selectedViewModule');
        //on change of edit user, update color picker with the selected user color
        selectElement.on('change', function () {
            var selectedOption = selectElement.find('option:selected');
            var fieldName = selectedOption.val();
            var userColor = jQuery('[data-calendar-fieldname="' + fieldName + '"]', parentElement).data('calendar-feed-color');
            selectedUserColor.val(userColor);
            selectedViewModule.val(selectedOption.data('viewmodule'));
            data.find('.calendarColorPicker').ColorPickerSetColor(userColor);
        });
    },
    /**
     * Function to save added user calendar
     */
    saveUserCalendar: function (data, currentEle) {
        var thisInstance = this;
        var userColor = data.find('.selectedUserColor').val();
        var fieldName = data.find('.selectedUser').val();
        var moduleName = data.find('.selectedViewModule').val();
        var userName = data.find('.selectedUser').data('username');
        var params = {
            module: 'Opportunities',
            action: 'SharedCalendarUserList',
            mode: 'addCalendarView',
            viewmodule: moduleName,
            viewfieldname: fieldName,
            viewColor: userColor
        };
        AppConnector.request(params).then(function () {
            app.hideModalWindow();
            var parentElement = jQuery('#calendarview-feeds');
            var colorContrast = app.getColorContrast(userColor.slice(1));
            if (colorContrast == 'light') {
                var textColor = 'black'
            } else {
                textColor = 'white'
            }

            var labelView = jQuery('<label class="checkbox addedCalendars" style="text-shadow: none"><input type="checkbox" /><span class="label" style="text-shadow: none"></span>&nbsp;<i class="icon-trash cursorPointer actionImage deleteCalendarView" title="Delete Calendar"></i></label>');
            var feedUserEle = labelView.find('[type="checkbox"]');

            feedUserEle.attr('checked', 'checked');
            feedUserEle.attr('data-calendar-sourcekey', "Events33_"+fieldName);
            feedUserEle.attr('data-calendar-feed', 'Events');
            feedUserEle.attr('data-calendar-feed-color', userColor);
            feedUserEle.attr('data-calendar-feed-textcolor', textColor);
            feedUserEle.attr('data-calendar-fieldname', fieldName);
            feedUserEle.attr('data-calendar-userid', fieldName);
            feedUserEle.attr('data-calendar-fieldlabel', userName);
            feedUserEle.closest('.addedCalendars').find('.label').css({'background-color': userColor, 'color': textColor}).text(userName);
            parentElement.append(labelView);
            //After add activityType reset accodion height to auto
            thisInstance.resetAccordionHeight();
            thisInstance.fetchCalendarFeed(feedUserEle);
            //Update the adding and editing users list in hidden modal
            var userSelectElement = jQuery('#calendarview-feeds').find('[name="usersCalendarList"]');
            userSelectElement.find('option[value="' + fieldName + '"]').remove();
            if (userSelectElement.find('option').length <= 0) {
                jQuery('#calendarview-feeds').find('.invisibleCalendarViews').val('false');
            }

            var editUserSelectElement = jQuery('#calendarview-feeds').find('[name="editingUsersList"]');
            editUserSelectElement.append('<option value="' + fieldName + '" data-viewmodule="' + moduleName + '">' + userName + '</option>');
            //notification message
            var message = app.vtranslate('JS_CALENDAR_VIEW_ADDED_SUCCESSFULLY');


            //show notification after add or edit user
            var params = {
                text: message,
                type: 'info'
            };
            Vtiger_Helper_Js.showPnotify(params);
        },
                function (error) {

                });
    },

    registerEvents: function () {
        this.registerCalendar();

        this.restoreActivityTypesWidgetState();
        //register event for add calendar view
        this.registerEventForAddCalendarView();

        return this;
    }
});
