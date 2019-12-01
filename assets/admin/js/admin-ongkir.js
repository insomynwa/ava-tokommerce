(function ($) {
	"use strict";

	var avaTokommerceLocation = {
		storeCountry: function () {
			if (!avaTokommerceLocation.getCountry().length) {
				$.getJSON(ava_tokommerce_params.json.country_url, function (data) {
					data.sort(function (a, b) {
						return (a.country_name > b.country_name) ? 1 : ((b.country_name > a.country_name) ? -1 : 0);
					});
					Lockr.set(ava_tokommerce_params.json.country_key, data);
				});
			}
		},
		getCountry: function (search, searchMethod) {
			var items = Lockr.get(ava_tokommerce_params.json.country_key);
			if (!items || typeof items === 'undefined') {
				return [];
			}

			if (search && search === Object(search)) {
				return avaTokommerceLocation.searchLocation(items, search, searchMethod);
			}

			return items;
		},
		storeProvince: function () {
			if (!avaTokommerceLocation.getProvince().length) {
				$.getJSON(ava_tokommerce_params.json.province_url, function (data) {
					data.sort(function (a, b) {
						return (a.province_name > b.province_name) ? 1 : ((b.province_name > a.province_name) ? -1 : 0);
					});
					Lockr.set(ava_tokommerce_params.json.province_key, data);
				});
			}
		},
		getProvince: function (search, searchMethod) {
			var items = Lockr.get(ava_tokommerce_params.json.province_key);
			if (!items || typeof items === 'undefined') {
				return [];
			}

			if (search && search === Object(search)) {
				return avaTokommerceLocation.searchLocation(items, search, searchMethod);
			}

			return items;
		},
		storeCity: function () {
			if (!avaTokommerceLocation.getCity().length) {
				$.getJSON(ava_tokommerce_params.json.city_url, function (data) {
					data.sort(function (a, b) {
						return (a.city_name > b.city_name) ? 1 : ((b.city_name > a.city_name) ? -1 : 0);
					});
					Lockr.set(ava_tokommerce_params.json.city_key, data);
				});
			}
		},
		getCity: function (search, searchMethod) {
			var items = Lockr.get(ava_tokommerce_params.json.city_key);
			if (!items || typeof items === 'undefined') {
				return [];
			}

			if (search && search === Object(search)) {
				return avaTokommerceLocation.searchLocation(items, search, searchMethod);
			}

			return items;
		},
		storeSubdistrict: function () {
			if (!avaTokommerceLocation.getSubdistrict().length) {
				$.getJSON(ava_tokommerce_params.json.subdistrict_url, function (data) {
					data.sort(function (a, b) {
						return (a.subdistrict_name > b.subdistrict_name) ? 1 : ((b.subdistrict_name > a.subdistrict_name) ? -1 : 0);
					});
					Lockr.set(ava_tokommerce_params.json.subdistrict_key, data);
				});
			}
		},
		getSubdistrict: function (search, searchMethod) {
			var items = Lockr.get(ava_tokommerce_params.json.subdistrict_key);
			if (!items || typeof items === 'undefined') {
				return [];
			}

			if (search && search === Object(search)) {
				return avaTokommerceLocation.searchLocation(items, search, searchMethod);
			}

			return items;
		},
		searchLocation: function (items, search, searchMethod) {
			if (searchMethod === 'filter') {
				return items.filter(function (item) {
					return avaTokommerceLocation.isLocationMatch(item, search);
				});
			}

			return items.find(function (item) {
				return avaTokommerceLocation.isLocationMatch(item, search);
			});
		},
		isLocationMatch: function (item, search) {
			var isItemMatch = true;
			for (var key in search) {
				if (!item.hasOwnProperty(key) || String(item[key]).toLowerCase() !== String(search[key]).toLowerCase()) {
					isItemMatch = false;
				}
			}
			return isItemMatch;
		}
	};

	avaTokommerceLocation.storeCountry(); // Store custom country data to local storage.
	avaTokommerceLocation.storeProvince(); // Store custom province data to local storage.
	avaTokommerceLocation.storeCity(); // Store custom city data to local storage.
	avaTokommerceLocation.storeSubdistrict(); // Store custom subdistrict data to local storage.

	var avaTokommerceBackend = {
		init: function () {
			avaTokommerceBackend.bindEvents();
			avaTokommerceBackend.maybeOpenModal();
		},
		bindEvents: function () {
			$(document.body).off('click', '.wc-shipping-zone-method-settings');
			$(document.body).on('click', '.wc-shipping-zone-method-settings', function (e) {
				$(document.body).off('wc_backbone_modal_loaded', avaTokommerceBackend.loadForm);

				if ($(e.currentTarget).closest('tr').find('.wc-shipping-zone-method-type').text() === ava_tokommerce_params.method_title) {
					$(document.body).on('wc_backbone_modal_loaded', avaTokommerceBackend.loadForm);
				}
			});

			$(document.body).off('wc_backbone_modal_loaded', avaTokommerceBackend.sortCouries);
			$(document.body).on('wc_backbone_modal_loaded', avaTokommerceBackend.sortCouries);

			$(document.body).off('change', '#woocommerce_ava_tokommerce_origin_province', avaTokommerceBackend.loadFormCity);
			$(document.body).on('change', '#woocommerce_ava_tokommerce_origin_province', avaTokommerceBackend.loadFormCity);

			$(document.body).off('change', '#woocommerce_ava_tokommerce_origin_city', avaTokommerceBackend.loadFormSubdistrict);
			$(document.body).on('change', '#woocommerce_ava_tokommerce_origin_city', avaTokommerceBackend.loadFormSubdistrict);

			$(document.body).off('change', '#woocommerce_ava_tokommerce_account_type', avaTokommerceBackend.highlightFeature);
			$(document.body).on('change', '#woocommerce_ava_tokommerce_account_type', avaTokommerceBackend.highlightFeature);

			$(document.body).off('change', '#woocommerce_ava_tokommerce_account_type', avaTokommerceBackend.toggleCouriersBox);
			$(document.body).on('change', '#woocommerce_ava_tokommerce_account_type', avaTokommerceBackend.toggleCouriersBox);

			$(document.body).off('change', '#woocommerce_ava_tokommerce_account_type', avaTokommerceBackend.toggleVolumetricConverter);
			$(document.body).on('change', '#woocommerce_ava_tokommerce_account_type', avaTokommerceBackend.toggleVolumetricConverter);

			$(document.body).off('change', '#woocommerce_ava_tokommerce_volumetric_calculator', avaTokommerceBackend.toggleVolumetricDivider);
			$(document.body).on('change', '#woocommerce_ava_tokommerce_volumetric_calculator', avaTokommerceBackend.toggleVolumetricDivider);

			$(document.body).off('change', '.ava-tokommerce-account-type', avaTokommerceBackend.selectAccountType);
			$(document.body).on('change', '.ava-tokommerce-account-type', avaTokommerceBackend.selectAccountType);

			$(document.body).off('change', '.ava-tokommerce-service--bulk', avaTokommerceBackend.selectServicesBulk);
			$(document.body).on('change', '.ava-tokommerce-service--bulk', avaTokommerceBackend.selectServicesBulk);

			$(document.body).off('change', '.ava-tokommerce-service--single', avaTokommerceBackend.selectServices);
			$(document.body).on('change', '.ava-tokommerce-service--single', avaTokommerceBackend.selectServices);

			$(document.body).off('click', '.ava-tokommerce-couriers-toggle', avaTokommerceBackend.toggleServicesItems);
			$(document.body).on('click', '.ava-tokommerce-couriers-toggle', avaTokommerceBackend.toggleServicesItems);
		},
		maybeOpenModal: function () {
			if (ava_tokommerce_params.show_settings) {
				setTimeout(function () {
					// Try show settings modal on settings page.
					var isMethodAdded = false;
					var methods = $(document).find('.wc-shipping-zone-method-type');
					for (var i = 0; i < methods.length; i++) {
						var method = methods[i];
						if ($(method).text() === ava_tokommerce_params.method_title) {
							$(method).closest('tr').find('.row-actions .wc-shipping-zone-method-settings').trigger('click');
							isMethodAdded = true;
							return;
						}
					}

					// Show Add shipping method modal if the shipping is not added.
					if (!isMethodAdded) {
						$('.wc-shipping-zone-add-method').trigger('click');
						$('select[name="add_method_id"]').val(ava_tokommerce_params.method_id).trigger('change');
					}

				}, 300);
			}
		},
		loadForm: function () {
			var provinceData = avaTokommerceLocation.getProvince();
			var provinceParam = {
				data: [],
				placeholder: ava_tokommerce_params.text.placeholder.state
			};

			if (provinceData.length) {
				for (var i = 0; i < provinceData.length; i++) {
					provinceParam.data.push({
						id: provinceData[i].province_id,
						text: provinceData[i].province,
					});
				}
			}

			$('#woocommerce_ava_tokommerce_origin_province').selectWoo(provinceParam).trigger('change');

			$('#woocommerce_ava_tokommerce_account_type').trigger('change');
		},
		loadFormCity: function () {
			var cityParam = {
				data: [],
				placeholder: ava_tokommerce_params.text.placeholder.city
			};
			var $cityField = $('#woocommerce_ava_tokommerce_origin_city');
			var citySelected = $cityField.val();
			var cityMatch = '';

			var provinceSelected = $('#woocommerce_ava_tokommerce_origin_province').val();
			var provinceData = avaTokommerceLocation.getProvince({ province_id: provinceSelected });
			if (provinceData) {
				var cityData = avaTokommerceLocation.getCity({ province_id: provinceData.province_id }, 'filter');
				if (cityData) {
					for (var i = 0; i < cityData.length; i++) {
						cityParam.data.push({
							id: cityData[i].city_id,
							text: cityData[i].type + ' ' + cityData[i].city_name,
						});

						if (citySelected === cityData[i].city_id) {
							cityMatch = cityData[i].city_id;
						}
					}
				}
			}

			$('#woocommerce_ava_tokommerce_origin_city').selectWoo(cityParam).val(cityMatch).trigger('change');
			$('#woocommerce_ava_tokommerce_volumetric_calculator').trigger('change');
		},
		loadFormSubdistrict: function () {
			var subdistrictParam = {
				data: [],
				placeholder: ava_tokommerce_params.text.placeholder.address_2
			};
			var $subdistrictField = $('#woocommerce_ava_tokommerce_origin_subdistrict');
			var subdistrictSelected = $subdistrictField.val();
			var subdistrictMatch = '';

			var citySelected = $('#woocommerce_ava_tokommerce_origin_city').val();
			var cityData = avaTokommerceLocation.getCity({ city_id: citySelected });
			if (cityData) {
				var subdistrictData = avaTokommerceLocation.getSubdistrict({ city_id: cityData.city_id }, 'filter');
				if (subdistrictData) {
					for (var i = 0; i < subdistrictData.length; i++) {
						subdistrictParam.data.push({
							id: subdistrictData[i].subdistrict_id,
							text: subdistrictData[i].subdistrict_name,
						});

						if (subdistrictSelected === subdistrictData[i].subdistrict_id) {
							subdistrictMatch = subdistrictData[i].subdistrict_id;
						}
					}
				}
			}

			$('#woocommerce_ava_tokommerce_origin_subdistrict').selectWoo(subdistrictParam).val(subdistrictMatch).trigger('change');
		},
		selectAccountType: function (e) {
			e.preventDefault();

			var selected = $(this).val();

			$(this).closest('tr').find('input').not($(this)).prop('disabled', false).prop('checked', false);

			$(this).prop('disabled', true);

			$('#woocommerce_ava_tokommerce_account_type').val(selected).trigger('change');
		},
		highlightFeature: function (e) {
			var selected = $(e.currentTarget).val();
			$('#ava-tokommerce-account-features').find('td, th')
				.removeClass('selected');
			$('#ava-tokommerce-account-features')
				.find('.ava-tokommerce-account-features-col-' + selected)
				.addClass('selected');
		},
		toggleVolumetricConverter: function (e) {
			var $accountType = $('#woocommerce_ava_tokommerce_account_type');
			var accounts = $accountType.data('accounts');
			var account = accounts[$(e.currentTarget).val()] || false;

			if (!account) {
				return;
			}

			if (!account.volumetric) {
				$('#woocommerce_ava_tokommerce_volumetric_calculator, #woocommerce_ava_tokommerce_volumetric_divider').closest('tr').hide();
			} else {
				$('#woocommerce_ava_tokommerce_volumetric_calculator').trigger('change').closest('tr').show();
			}
		},
		toggleVolumetricDivider: function (e) {
			var checked = $(e.currentTarget).is(':checked');

			if (checked) {
				$('#woocommerce_ava_tokommerce_volumetric_divider').closest('tr').show();
			} else {
				$('#woocommerce_ava_tokommerce_volumetric_divider').closest('tr').hide();
			}
		},
		toggleCouriersBox: function () {
			var $accountType = $('#woocommerce_ava_tokommerce_account_type');
			var accounts = $accountType.data('accounts');
			var couriers = $accountType.data('couriers');
			var account = $accountType.val();

			_.each(couriers, function (zoneCouriers, zoneId) {
				var selected_couriers = 0;

				var couriersAccount = _.find(couriers[zoneId], function (courier) {
					return courier.account.indexOf(account) !== -1;
				});

				_.each(zoneCouriers, function (courier, courierId) {
					if (courier.account.indexOf(account) === -1) {
						$('.ava-tokommerce-couriers-item--' + zoneId + '--' + courierId).hide();
						$('.ava-tokommerce-couriers-item--' + zoneId + '--' + courierId).find('.ava-tokommerce-service').prop('checked', false);
					} else {
						$('.ava-tokommerce-couriers-item--' + zoneId + '--' + courierId).show();
					}

					if (!accounts[account].multiple_couriers) {
						if (selected_couriers) {
							$('.ava-tokommerce-couriers-item--' + zoneId + '--' + courierId).find('.ava-tokommerce-service').prop('checked', false);
						}

						if ($('.ava-tokommerce-couriers-item--' + zoneId + '--' + courierId).find('.ava-tokommerce-service--single:checked').length) {
							selected_couriers++;
						}
					}

					avaTokommerceBackend.updateSelectedServicesCounter('.ava-tokommerce-couriers-item--' + zoneId + '--' + courierId);
				});

				if (!couriersAccount) {
					$('.ava-tokommerce-couriers-wrap--' + zoneId).addClass('no-items');
				} else {
					$('.ava-tokommerce-couriers-wrap--' + zoneId).removeClass('no-items');
				}
			});

			var $itemsWrapNoItems = $('.ava-tokommerce-couriers-wrap.no-items');

			if ($itemsWrapNoItems.length === 1) {
				$('.ava-tokommerce-couriers-wrap:not(.no-items)').addClass('full-width');
			} else {
				$('.ava-tokommerce-couriers-wrap').removeClass('full-width');
			}
		},
		selectServicesBulk: function () {
			var courierId = $(this).closest('.ava-tokommerce-couriers-item').data('id');
			var zoneId = $(this).closest('.ava-tokommerce-couriers-item').data('zone');
			var $accountType = $('#woocommerce_ava_tokommerce_account_type');
			var account = $accountType.val();
			var accounts = $accountType.data('accounts');

			if ($(this).is(':checked')) {
				$(this).closest('.ava-tokommerce-couriers-item').find('.ava-tokommerce-service--single').prop('checked', true);

				if (!accounts[account].multiple_couriers) {
					$('.ava-tokommerce-couriers-item').not('.ava-tokommerce-couriers-item--' + zoneId + '--' + courierId).find('.ava-tokommerce-service').prop('checked', false);
				}
			} else {
				$(this).closest('.ava-tokommerce-couriers-item-inner').find('.ava-tokommerce-service--single').prop('checked', false);
			}

			avaTokommerceBackend.updateSelectedServicesCounter('.ava-tokommerce-couriers-item--' + zoneId + '--' + courierId);
		},
		selectServices: function () {
			var courierId = $(this).closest('.ava-tokommerce-couriers-item').data('id');
			var zoneId = $(this).closest('.ava-tokommerce-couriers-item').data('zone');
			var $accountType = $('#woocommerce_ava_tokommerce_account_type');
			var account = $accountType.val();
			var accounts = $accountType.data('accounts');

			if ($(this).is(':checked')) {
				$(this).closest('.ava-tokommerce-couriers-item').find('.ava-tokommerce-service--bulk').prop('checked', true);

				if (!accounts[account].multiple_couriers) {
					$('.ava-tokommerce-couriers-item').not('.ava-tokommerce-couriers-item--' + zoneId + '--' + courierId).each(function (index, item) {
						$(item).find('.ava-tokommerce-service').prop('checked', false);
					})
				}
			} else {
				if (!$(this).closest('.ava-tokommerce-services').find('.ava-tokommerce-service--single:checked').length) {
					$(this).closest('.ava-tokommerce-couriers-item').find('.ava-tokommerce-service--bulk').prop('checked', false);
				}
			}

			avaTokommerceBackend.updateSelectedServicesCounter('.ava-tokommerce-couriers-item--' + zoneId + '--' + courierId);
		},
		updateSelectedServicesCounter: function (itemClass) {
			var selectedServices = $(itemClass).find('.ava-tokommerce-service--single:checked').length;
			$(itemClass).find('.ava-tokommerce-couriers--selected').text(selectedServices);
		},
		toggleServicesItems: function (e) {
			var upArraw;
			$(e.currentTarget).closest('.ava-tokommerce-couriers-item-inner').find('.ava-tokommerce-services-item').each(function (index, item) {
				if ($(item).is(':visible')) {
					$(item).slideUp('fast');
					upArraw = false;
				} else {
					$(item).slideDown('fast');
					upArraw = true;
				}
			});

			if (upArraw) {
				$(e.currentTarget).find('.dashicons').toggleClass('dashicons-admin-generic dashicons-arrow-up-alt2');
				$(e.currentTarget).closest('.ava-tokommerce-couriers-item-info').find('.ava-tokommerce-couriers-item-info-link').show();
			} else {
				$(e.currentTarget).find('.dashicons').toggleClass('dashicons-arrow-up-alt2 dashicons-admin-generic');
				$(e.currentTarget).closest('.ava-tokommerce-couriers-item-info').find('.ava-tokommerce-couriers-item-info-link').hide();
			}
		},
		sortCouries: function () {
			$(".ava-tokommerce-couriers").sortable();
		},
	}

	$(document).ready(avaTokommerceBackend.init);
}(jQuery));
