
spinnerClasses = "wpcsc-spin dashicons-update";
let $ = jQuery;
// jQuery.noConflict();
// $ = jQuery.noConflict(true);

$(document).ready(function() { // wait for page to finish loading

	jQuery(document).on('click', '.wpcsc-dt-filter-handle', function() {
		let dtFilters = jQuery(this).parent().find('.wpcsc-dt-filters');
		dtFilters.slideToggle();
	});

	jQuery('.dt-master-select').on('click', function(){
		jQuery('.dt-checkbox').prop('checked', this.checked);
	});

	let wpcscTextareas = jQuery('.wpcsc-layout__body textarea');
	wpcscTextareas.each(function(item) {
		if( ! jQuery(this).hasClass("wp-editor-area")) {
			console.log(jQuery(this).prop('scrollHeight'));
			jQuery(this).outerHeight(38).outerHeight(this.scrollHeight+10); // 38 or '1em' -min-height
		}
	});

	/**
	 * This allows to add data attribute to the selected_bulk class [data-selected_bulk=postIDs/userIDs]
	 */
	jQuery(document).on('change', '.dt-checkbox, .dt-master-select', function(){
		let selected_rows = wpcsc_get_selected_rows();
		let rowIDs = selected_rows.toString();
		jQuery('.selected_bulk').data('selected_bulk', rowIDs);
	});

	// Handle click on checkbox to set state of "Select all" control
	jQuery(document).on('change', '.dt-checkbox', function(){
		// If checkbox is not checked
		if(!this.checked){
			let el = jQuery('.dt-master-select').get(0);
			let el2 = jQuery('.dt-master-select').get(1);

			// If "Select all" control is checked and has 'indeterminate' property
			if(el && el.checked && ('indeterminate' in el)){
				el.indeterminate = true;
			}

			if(el2 && el2.checked && ('indeterminate' in el2)){
				el2.indeterminate = true;
			}
		}
	});

	jQuery(document).on('submit', '.form-ajax', function(e) {
		e.preventDefault();
		let parentForm = jQuery(this);

		let btnClicked = parentForm.find('input[type=submit]');
		let ajaxResult = parentForm.find('div.ajax-result');
		let spinner = parentForm.find('.dashicons-update');

		let action = ( undefined !== parentForm.data('action') ) ? parentForm.data('action') : 'action_ajax_handler';

		let form_data = new FormData(parentForm[0]);
		form_data.append('action', action);

		// if ( window.wpcsc.can_use_premium_code ) {
		// 	alert('good');
		// }

		if(form_data.get('handle') === null){
			let handle = ( undefined !== parentForm.data('handle') ) ? parentForm.data('handle') : 'wpcsc';
			form_data.append('handle', handle);
		}
		if(form_data.get('task') === null){
			let task = ( undefined !== parentForm.data('task') ) ? parentForm.data('task') : 'add';
			form_data.append('task', task);
		}

		parentForm.find(':input').prop("disabled", true);
		ajaxResult.removeClass('alert alert-success alert-danger').html('');
		btnClicked.attr('disabled', true);
		spinner.addClass('wpcsc-spin').removeClass('d-none');

		jQuery.ajax({
			contentType: false,
			processData: false,
			data: form_data,
			type: 'post',
			url: WPCSC_AJAX.ajaxurl,
		}).done(
			function(response) {
				parentForm.find(':input').prop("disabled", false);
				spinner.removeClass('wpcsc-spin').addClass('d-none');
				btnClicked.attr('disabled', false);

				if(jQuery('.btn-refresh-filter') !== undefined) jQuery('.btn-refresh-filter').click();

				if( response.success === true ) {
					ajaxResult.removeClass('d-none').addClass('alert alert-success').html(response.data.reason);
				}else {
					ajaxResult.removeClass('d-none').addClass('alert alert-danger').html(response.data.reason);
				}
				wpcsc_perform_post_ajax(response);
			}
		).fail(
			function( response ) {
				wpcsc_error_code(response);
				parentForm.find(':input').prop("disabled", false);
				spinner.removeClass('wpcsc-spin').addClass('d-none');
				btnClicked.attr('disabled', false);
			}
		);
	});

	jQuery(document).on('click', '.wpcsc-pr-only', function () {
		wpcsc_swal("Premium Only", "You can use this feature in Premium version only. Upgrade now!!");
	});

	jQuery(document).on('click', '.btn-ajaxy', function(e){
		e.preventDefault();
		let btnClicked = jQuery(this);
		let spinner = btnClicked.find('.dashicons');
		let faIcon = (btnClicked.data('fa-icon')!==undefined);
		let ajaxAction = (btnClicked.data('action')!==undefined) ? btnClicked.data('action') : 'action_ajax_handler';

		spinner.removeClass(faIcon).addClass(spinnerClasses);

		if(btnClicked.hasClass('selected_bulk') && (btnClicked.data('selected_bulk')===undefined || btnClicked.data('selected_bulk')==='')) {
			wpcsc_swal("Nothing Selected", "Please select the desired rows", "warning");
			return;
		}

		let data = btnClicked.data();
		data['action'] = ajaxAction;
		btnClicked.swalme({
			title: (data.title!==undefined) ? data.title : wpcsc_get_name(data.op) + " " + wpcsc_get_name(data.handle),
			text: (data.text!==undefined) ? data.text : "Are you sure?",
			dangerMode: true,
			buttons: {
				cancel: { text: "No", visible: true },
				confirm: { text: "Yes, I am", closeModal: false }
			}
		})
		.then((inputValue) => {
			if(!inputValue) {
				spinner.addClass(faIcon).removeClass(spinnerClasses);
				return;
			}
			jQuery.post(WPCSC_AJAX.ajaxurl, data).done(
				function( response ) {

					spinner.removeClass(spinnerClasses).addClass(faIcon);

					if(response.success===true){
						if(response.data.notify) {
							wpcsc_bootstrap_notify('Success! ', response.data.notify);
						}

						if(undefined !== btnClicked.data("redirect")){
							window.location.href = btnClicked.data("redirect");
						}
						else if(response.data.redirect!==undefined && response.data.redirect!==false) {
							window.location.href = response.data.redirect;
						}

						if(data.op==='trash') {
							if(undefined === btnClicked.data("redirect")){
								$('#row_'+data.id).remove();
							}
						}
						if(data.op==='clone') {
							if(undefined !== btnClicked.data("redirect")){
								window.location.href = response.data.redirect;
							}else{
								$('.btn-refresh-filter').click();
							}
						}
						if(jQuery.inArray(data.ajax, ['bulk_delete', 'bulk_clone'])!== -1) {
							if(undefined !== btnClicked.data("redirect")){
								window.location.href = response.data.redirect;
							}else{
								$('.btn-refresh-filter').click();
							}
						}
						if(response.data.mail === true)
							wpcsc_swal('Success!', response.data.reason, 'success');
						else
							swal.close();
					}else{
						wpcsc_swal('Error!', response.data.reason, 'error');
					}
				}
			).fail(
				function( response ) {
					spinner.removeClass(spinnerClasses).addClass(faIcon);
					wpcsc_error_code(response);
				}
			);
		});
	});

	jQuery(document).on('click', '.modal_actions', function (e) {
		if (WPCSC_AJAX.is_wpcscpro!=="true") {
			wpcsc_swal("Premium Only", "You can use this feature in Premium version only. Upgrade now!!");
			return;
		}

		// e.preventDefault();
		let btnClicked = jQuery(this);

		let action = (undefined !== btnClicked.data('action')) ? btnClicked.data('action') : 'action_get_universal_html';
		let modal = jQuery('#action_modal');
		let modalSpinner = modal.find('.modalSpinner');
		let modalTitle = modal.find('.modal-title');
		let modalData = modal.find('.modal-data');

		let modal_size = btnClicked.data('modal_size');
		if(modal_size !== undefined){
			jQuery('.modal-dialog').addClass('modal-'+modal_size);
		}else{
			jQuery('.modal-dialog').removeClass().addClass('modal-dialog modal-dialog-centered');
		}

		if(modal.hasClass('.md-modal')){
			modal.addClass('md-show');
		}else{
			modal.modal("show");
		}
		modalSpinner.show();
		modalData.hide();
		modalTitle.html(btnClicked.data('title'));

		jQuery.post( WPCSC_AJAX.ajaxurl, {
			action : action,
			data: btnClicked.data() 	// required - id, op and key
		},
		function( response ) {
			modalSpinner.hide();
			modalData.show();
			modalData.html(response.data.reason);
		});
	});

	jQuery(document).on('click', '.wpcsc-modal-close', function (e) {
		e.preventDefault();
		let btnClicked = jQuery(this);
		let modal = btnClicked.closest('#action_modal');
		modal.modal('hide');
	});

	jQuery(document).on('click', '.expand-handle', function(e) {
		e.preventDefault();
		let btnClicked = jQuery(this);
		let handleIcon = btnClicked.find('.dashicons');
		let parent = jQuery(this).closest('.tab-content');

		let colEditor = parent.find('.col-editor');
		let colPreview = parent.find('.col-preview');

		if(colEditor.hasClass('expanded')) {
			colEditor.removeClass("expanded");
			colEditor.removeClass("col-md-12");
			colEditor.addClass("col-md-6");
			colPreview.removeClass("d-none");

			handleIcon.removeClass('dashicons-fullscreen-exit-alt');
			handleIcon.addClass('dashicons-fullscreen-alt');
		}
		else {
			colEditor.addClass("expanded");
			colEditor.addClass("col-md-12");
			colEditor.removeClass("col-md-6");
			colPreview.addClass("d-none");

			handleIcon.addClass('dashicons-fullscreen-exit-alt');
			handleIcon.removeClass('dashicons-fullscreen-alt');
		}
	});

	/**
	 * Function to alert user, if enabling this option
	 */
	jQuery(document).on('click', '#wpcsc_delete_data', function(){
		let btnClicked = jQuery(this);
		let changedValue = btnClicked.prop('checked');

		if(changedValue!==true) return;

		btnClicked.swalme({
			title: 'Are you sure?',
			text: "This option will delete all the data, including leads, when the plugin is uninstalled. Even if by mistake.",
			dangerMode: true,
			buttons: {
				cancel: { text: "No", visible: true },
				confirm: { text: "Yes, I am", closeModal: false }
			}
		})
		.then((inputValue) => {
			if (!inputValue) {
				btnClicked.prop('checked', false);
				return;
			}

			swal.close();
		});
	});

	jQuery(document).on('click', '.csc-state-edit', function (e) {
		e.preventDefault();
		let editIcon = jQuery(this);
		let state = editIcon.parents().eq(1);
		state.find('b').toggleClass('d-none');
		editIcon.parent().find('select').toggleClass('d-none');
		if(editIcon.hasClass('dashicons-no')){
			editIcon.removeClass('dashicons-no text-danger').addClass('dashicons-edit text-primary');
		}else{
			editIcon.removeClass('dashicons-edit text-primary').addClass('dashicons-no text-danger');
		}
	});

	jQuery(document).on('change', '.csc-state-dropdown', function () {
		let selectedVal = jQuery(this).val();
		if(selectedVal === '') {
			alert("Please choose appropriate option");
			return;
		}
		jQuery.post( WPCSC_AJAX.ajaxurl, {
			action : 'action_set_state',
			state: selectedVal
		}).done( function (response) {
			wpcsc_perform_post_ajax(response)
		}).fail( function (response) {
			wpcsc_error_code(response);
		});
	});
});

$(function() {
	//caches a jQuery object containing the header element
	let header = $(".wpcsc-layout__header");
	$(window).scroll(function() {
		let scroll = $(window).scrollTop();

		if (scroll >= 25) {
			header.addClass("is-scrolled");
		} else {
			header.removeClass("is-scrolled");
		}
	});
});

function wpcsc_swal(swal_title, swal_text, swal_type) {
	swal({
		title: swal_title,
		icon: swal_type,
		content: { element: "div", attributes: {
				innerHTML: swal_text,
			}},
		dangerMode: true,
		buttons: {
			confirm: { text: "Ok!", closeModal: true }
		}
	});
}

function wpcsc_perform_post_ajax(response, ajaxAction, ele) {
	if(response.data.redirect!==undefined) {
		window.location.href = response.data.redirect;
	}
	else if(response.data.reload!==undefined) {
		location.reload();
	}
	else if(response.data.removeHandler!==undefined) {
		ele.remove();
	}
}

function wpcsc_error_code(response){
	let error = response.status + ': ' + response.statusText ;

	if(response.status === 400){
		swal(error, "Please contact support", "error");
	}
	else if(response.status === 500){
		swal(error, 'Please Contact developer support', "error");
	}
	else if(response.status === 503){
		swal(error, 'Error: 503. Please Contact developer support', "error");
	}
	else{
		swal('Error', 'Please Contact support', "error");
	}
}

function wpcsc_init_daterange(start, end){
	if(!jQuery('#devon-daterangepicker').length) {
		return;
	}

	if(start===undefined) {
		start = moment().subtract(0, 'days');
	}

	if(end===undefined) {
		end = moment();
	}

	function tb(start, end) {
		jQuery('#devon-daterangepicker span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
		jQuery('.search_col_date').text(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
	}

	let daterange = jQuery('#devon-daterangepicker').daterangepicker({
		startDate: start,
		endDate: end,
		ranges: {
			'Today': [moment(), moment()],
			'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
			'2 days ago': [moment().subtract(2, 'days'), moment().subtract(2, 'days')],
			'Last 7 Days': [moment().subtract(7, 'days'), moment()],
			'Last 30 Days': [moment().subtract(29, 'days'), moment()],
			'This Month': [moment().startOf('month'), moment().endOf('month')],
			'Prev Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
			'All Time': ['January 1, 2014', end]
		}
	}, tb);

	tb(start, end);
	return daterange;
}

function wpcsc_get_selected_rows() {

	let selected_rows = [];
	// hint: get all the selected rows
	jQuery.each(jQuery('.dt-checkbox'), function(index){
		let current = jQuery(this);
		if(current.is(":checked")) {
			selected_rows.push(current.val());
		}
	});

	return selected_rows;
}

function wpcsc_get_name(str) {
	return str.toUpperCase();
}

function wpcsc_datepicker(){
	var datePickers = $('.rel-date-picker .datepicker');
	if(datePickers.length) {
		// alert('test');

		allPickers = {};

		datePickers.each(function(index, value) {

			allPickers[index] = {};

			allPickers[index]['parentSpan'] = $(this).parent();
			allPickers[index]['datehidden'] = allPickers[index]['parentSpan'].find('.datehidden');
			allPickers[index]['date-text'] = allPickers[index]['parentSpan'].find('.date-text');

			allPickers[index]['existingDate'] = allPickers[index]['datehidden'].val();
			allPickers[index]['existingDateMoment'] = moment(allPickers[index]['existingDate'], 'YYYY-MM-DD');

			allPickers[index]['options'] = {
				"singleDatePicker": true,
				"minDate": moment(),
				"dateFormat": 'YYYY-MM-DD',
				"autoUpdateInput": false,
			};

			allPickers[index]['existingDateValue'] = '';
			if(allPickers[index]['existingDateMoment'].isValid()) {
				allPickers[index]['existingDateValue'] = allPickers[index]['existingDateMoment'].format("MM/DD/YYYY");
			}

			if(allPickers[index]['existingDateValue']!='') {
				allPickers[index]['additionalOptions'] = {
					"startDate": allPickers[index]['existingDateValue'],
					"autoUpdateInput": true,
				};
				jQuery.extend(allPickers[index]['options'], allPickers[index]['additionalOptions']);
			}

			$(this).daterangepicker(allPickers[index]['options'], function(start, end, label) {
				// console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
				allPickers[index]['datehidden'].val(start.format('YYYY-MM-DD')).change();
				allPickers[index]['date-text'].html(start.format('YYYY-MM-DD'));
			});
		});
	}
}

(function($) {
	$.fn.swalme = function(options) {

		// This is the easiest way to have default options.
		var settings = $.extend({
			// These are the defaults.
			title: 'Important Action',
			text: "Are you sure?",
			content: "Are you sure?",
			icon: "info",
			buttons: {}
		}, options );

		return swal(settings);
	};
}(jQuery));

/*
* Function rel_bootstrap_notify
* Param:
* 	title - title of the notify
* 	message - message of the notify
* 	type - success, warning, danger, primary
* 	icon - Font awesome icon with 'fa fa-user'
 */
function wpcsc_bootstrap_notify(title, message, type, icon, placementFrom, delay=5000) {

	if(type==undefined || type=='')
		type = 'success';

	if(placementFrom==undefined || placementFrom=='')
		placementFrom = 'top';

	if(icon==undefined || icon=='') {
		if(type=='success')
			icon = 'fa fa-check';
		else
			icon = 'fa fa-icon';
	}

	jQuery.notify({
		// options
		icon: icon,
		title: title,
		message: message,
	},{
		// settings
		type : type,
		offset: 40,
		delay: delay,
		z_index: 9999,
		placement: {
			from : placementFrom,
			align : "right",
		}
	});
}