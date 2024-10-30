jQuery(document).ready(function($) {

	$.fn.initDt = function(options) {

		if(jQuery(this).length===undefined) {
			wpcsc_error_code({ status: 500, statusText:'Table does not exist to load the data.' } );
			return; 
		}
		
		jQuery.each(jQuery(this), function(index, val) {
			let tableAction = jQuery(this).data('action'); 
			let view_key = jQuery(this).data('view_key');
			let table_name = jQuery(this).data('table_name');

			let startDate = jQuery(this).data('start');
			let endDate = jQuery(this).data('end');

			if(startDate!==undefined) {
				startDate = moment().subtract(startDate, 'days');
			}

			let ajaxActionName = 'wpcsc_dt_' + tableAction + '_callback';
			let defaults = {
				action : ajaxActionName,
				table : jQuery(this).data('table'),
			};
			
			let optionsExtended = $.extend({}, defaults, options);	
			
			if(optionsExtended.action==="") {
				console.error('Action not available for DataTable'); 
				wpcsc_error_code({status: 500, statusText:'Action not available for Data Table'}); 
				return; 
			}
			
			let dataTableObject;
			let daterange = wpcsc_init_daterange(startDate, endDate);

			dataTableObject = jQuery(this).DataTable({
				pagingType: "simple_numbers",
				processing:true,
				searching:false,
				ordering: false,
				serverSide:true,
				deferRender: true,
				order: [[1,'asc']],
				columnDefs: [
					{
						'targets': 0,
						'searchable': false,
						'orderable': false,
						'className': 'dt-body-center',
						'render': function (data, type, full, meta){
							return '<input type="checkbox" class="dt-checkbox" name="id[]" value="' + $('<div/>').text(data).html() + '">';
						}
					},{ "width": "5%", "targets": 0 },
				],
				// order: [[ 2, "desc" ]],
				lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
				dom: '<"top"flrtip<"clear">>rt<"bottom"pl<"clear">>',
				language: {
					processing: '<i class="dashicons dashicons-update wpcsc-spin"></i><span class="sr-only">Loading......</span>',
					emptyTable: "No Data Yet",
				},
				createdRow: function( row, data, dataIndex ) {
					// $('td', row).eq(1).addClass('text-wrap');
					$('td', row).addClass('align-middle text-wrap');
					$('td', row).eq(0).removeClass('align-middle');
				},
				
				ajax: {
					'url' : WPCSC_AJAX.ajaxurl + '?action='+optionsExtended.action,
					'data' : function(d) {
						d.data = get_search_filters();
						d.view_key = view_key;
						d.table_name = table_name;
					},
				},
				initComplete: function(settings, json) {
					wpcsc_datepicker()
				},
				drawCallback: function( settings ) {
					jQuery('.dt-master-select').prop("checked", false);
				}
			});

			jQuery(document).on('change', '.all-custom-filter', function() {
				dataTableObject.ajax.reload(null, false);
			});

			jQuery(document).on('click', '.btn-refresh-filter', function() {
				dataTableObject.ajax.reload(null, false);
			});
			
			jQuery(document).on('click', '.btn-reset-filter', function() {
				jQuery('.all-custom-filter').val('');			
				dataTableObject.ajax.reload(null, false);
			});
				
			jQuery('#devon-daterangepicker').on('apply.daterangepicker', function(ev, picker) {		
				dataTableObject.ajax.reload(null, false);
			});

			jQuery(document).on('click', '.btn-export-excel', function() {

				if (WPCSC_AJAX.is_premium!=="true") {
					wpcsc_swal("Premium Only", "You can use this feature in Premium version only. Upgrade now!!");
					return;
				}

				btnClicked = jQuery(this);
				spinner = btnClicked.find('.dashicons-update');
				jQuery('.ajax-result-area').html('');
				spinner.removeClass('d-none').addClass('wpcsc-spin');
				
				let data = get_search_filters();
				data.table = view_key;
				
				jQuery.post( WPCSC_AJAX.ajaxurl,{
					action : 'action_export_data_to_excel',
					fields : data,
				})
				.done(
					function( response ) {
						spinner.removeClass('wpcsc-spin').addClass('d-none');
						jQuery('.ajax-result-area').html(response.data.reason);

						if(response.data.notify) {
							wpcsc_bootstrap_notify('Success!', response.data.notify);
						}
					})
				.fail( function( response ) {
					wpcsc_error_code(response);
					// btnClicked.find(':input').prop("disabled", false);
					spinner.hideSpinner();
					btnClicked.attr('disabled', false);
				});
			});
		});
	}
}); 

jQuery(document).ready(function() {
	jQuery('.rel-dt-common').initDt({});

	jQuery('.dataTables_wrapper').removeClass('form-inline no-footer');
	jQuery('.rel-dt-common').removeClass('no-footer dtr-inline collapsed');
});	

function get_search_filters() {

	let searchFilters = {};
	let inputCheckboxes = [];
	
	jQuery('.all-custom-filter').each(function(){
		if(jQuery(this).attr("name")!==undefined){
			let inputType = jQuery(this).attr("type");
			switch(inputType) {
				case "checkbox":
					let currentElementMetaKey = jQuery(this).attr("name").replace("[]", "");
					if(jQuery.inArray(currentElementMetaKey, inputCheckboxes) === -1) {
						inputCheckboxes.push(currentElementMetaKey);
					}
					break;
				default:
					searchFilters[jQuery(this).attr('name')] = jQuery(this).val();
			}
		}
	});
	
	if(jQuery('.search_col_date').length) {
		searchFilters['date'] = jQuery('.search_col_date').text();
	}

	// Get selected checkboxes for all checkboxes type filter
	jQuery.each(inputCheckboxes, function(key, val) {
		searchFilters[val] = wpcsc_getSelectedCheckboxes(val);
	});

	return searchFilters;
}

function wpcsc_getSelectedCheckboxes(option_name) {
	let selectedCheckboxes = [];
	jQuery('input.'+option_name+':checked').each(function() {
		selectedCheckboxes.push(jQuery(this).val());
	});
	return selectedCheckboxes.join(",");
}