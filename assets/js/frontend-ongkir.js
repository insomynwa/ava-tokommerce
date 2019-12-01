(function($) {
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

var avaTokommerceFrontend = {
	init: function () {
		$(document.body).on('country_to_state_changed', function () {
			var fields = avaTokommerceFrontend.getFields();

			$('select.country_to_state, input.country_to_state').each(function () {
				var $countryField = $(this);
				var fieldPrefix = $countryField.attr('id').replace('_country', '');
				var selectedCountry = $countryField.val();

				_.each(fields, function (field) {
					if (field.callback) {
						$('#' + fieldPrefix + '_' + field.suffix).off('change', field.callback);
					}
				});

				if (selectedCountry === 'ID') {
					avaTokommerceFrontend.modifyForm(fieldPrefix);

					_.each(fields, function (field) {
						if (field.callback) {
							$('#' + fieldPrefix + '_' + field.suffix).on('change', field.callback);
						}
					});

					_.each(fields, function (field) {
						if (field.triggerEvent) {
							$('#' + fieldPrefix + '_' + field.suffix).trigger(field.triggerEvent);
						}
					});
				} else {
					avaTokommerceFrontend.restoreForm(fieldPrefix);
				}
			});
		});
	},
	onChangeStateField: function (e) {
		var $provinceField = $(e.currentTarget);
		var fieldPrefix = $provinceField.attr('id').replace('_state', '');
		var provinceSelected = $provinceField.val();
		var cityOptions = [];

		var provinceData = avaTokommerceLocation.getProvince({ code: provinceSelected });

		if (provinceData) {
			var cityData = avaTokommerceLocation.getCity({ province_id: provinceData.province_id }, 'filter');
			if (cityData) {
				for (var i = 0; i < cityData.length; i++) {
					var cityName = cityData[i].type + ' ' + cityData[i].city_name;

					cityOptions.push({
						id: cityName,
						text: cityName,
					});
				}
			}
		}

		var citySelected = $('#' + fieldPrefix + '_city').val();

		$('#' + fieldPrefix + '_city').empty().select2({
			width: '100%',
			data: cityOptions,
			placeholder: avaTokommerceFrontend.getFields({ suffix: 'city' }).placeholder,
		}).val(citySelected).trigger('change');
	},
	onChangeCityField: function (e) {
		var $cityField = $(e.currentTarget);
		var fieldPrefix = $cityField.attr('id').replace('_city', '');
		var $provinceField = $('#' + fieldPrefix + '_state');
		var provinceSelected = $provinceField.val();
		var citySelected = $cityField.val();
		var subdistrictOptions = [];

		var provinceData = avaTokommerceLocation.getProvince({ code: provinceSelected });

		if (provinceData && citySelected) {
			var cityType = citySelected.split(' ')[0];
			var cityName = citySelected.split(' ').splice(1).join(' ');

			var cityData = avaTokommerceLocation.getCity({
				type: cityType,
				city_name: cityName,
				province_id: provinceData.province_id,
			});

			if (cityData) {
				var subdistrictData = avaTokommerceLocation.getSubdistrict({
					province_id: provinceData.province_id,
					city_id: cityData.city_id
				}, 'filter');

				if (subdistrictData) {
					for (var i = 0; i < subdistrictData.length; i++) {
						subdistrictOptions.push({
							id: subdistrictData[i].subdistrict_name,
							text: subdistrictData[i].subdistrict_name,
						});
					}
				}
			}
		}

		var subdistrictSelected = $('#' + fieldPrefix + '_address_2').val();

		$('#' + fieldPrefix + '_address_2').empty().select2({
			width: '100%',
			data: subdistrictOptions,
			placeholder: avaTokommerceFrontend.getFields({ suffix: 'address_2' }).placeholder,
		}).val(subdistrictSelected).trigger('change');
	},
	onChangeSubdistrictField: function (e) {
		var isUpdateCheckout = function () {
			var shipToDifferentAddress = $('#ship-to-different-address-checkbox').is(':checked');
			var fieldPrefix = $(e.currentTarget).attr('id').replace('_address_2', '');

			if (fieldPrefix === 'billing' && !shipToDifferentAddress) {
				return true;
			}

			if (fieldPrefix === 'shipping' && shipToDifferentAddress) {
				return true;
			}

			return false;

		};

		if (isUpdateCheckout()) {
			$(document.body).trigger('update_checkout');
		}
	},
	modifyForm: function (fieldPrefix) {
		_.each(avaTokommerceFrontend.getFields(), function (field) {
			if (field.modifyForm) {
				var $field = $('#' + fieldPrefix + '_' + field.suffix);
				if (!$field || !$field.length) {
					var $cloneFieldWrap = $('#' + fieldPrefix + '_postcode_field');

					if ($cloneFieldWrap && $cloneFieldWrap.length) {
						var $fieldWrap = $cloneFieldWrap.clone().attr({
							id: fieldPrefix + '_' + field.suffix + '_field'
						});

						$fieldWrap.find('input').attr({
							'id': fieldPrefix + '_' + field.suffix,
							'name': fieldPrefix + '_' + field.suffix,
							'value': $('#' + fieldPrefix + '_' + field.suffix + '_dummy').val(),
							'placeholder': field.placeholder || '',
							'data-placeholder': field.placeholder || '',
						});

						$cloneFieldWrap.before($fieldWrap);

						$field = $fieldWrap.find('input');
					}
				}

				if ($field && $field.length) {
					if (!$field.is('select')) {
						var fieldValue = $field.val();
						var fieldAttrs = _.omit(avaTokommerceFrontend.getFieldAttributes($field), ['type']);

						$field.replaceWith($('<select></select>').attr(fieldAttrs).append($('<option value="' + fieldValue + '">' + fieldValue + '</option>')));
					}

					$field = $('#' + fieldPrefix + '_' + field.suffix);
					$field.removeClass('input-text');

					$field.select2({
						width: '100%',
					});
				}
			}
		});
	},
	restoreForm: function (fieldPrefix) {
		_.each(avaTokommerceFrontend.getFields(), function (field) {
			if (field.modifyForm) {
				var $field = $('#' + fieldPrefix + '_' + field.suffix);

				if ($field.is('select')) {
					$field.select2('destroy');

					var fieldAttrs = _.extend(avaTokommerceFrontend.getFieldAttributes($field), { type: 'text' });

					$field.replaceWith($('<input>').attr(fieldAttrs));

					$field = $('#' + fieldPrefix + '_' + field.suffix);
					$field.addClass('input-text');

					if (fieldPrefix === 'calc_shipping' && field.suffix === 'address_2') {
						$field.closest('.form-row').remove();
					}
				}
			}
		});
	},
	getFields: function (search) {
		var fields = [{
			suffix: 'state',
			callback: avaTokommerceFrontend.onChangeStateField,
			triggerEvent: 'change',
		},
		{
			suffix: 'city',
			callback: avaTokommerceFrontend.onChangeCityField,
			modifyForm: true,
			placeholder: ava_tokommerce_params.text.placeholder.city,
		},
		{
			suffix: 'address_2',
			callback: avaTokommerceFrontend.onChangeSubdistrictField,
			modifyForm: true,
			placeholder: ava_tokommerce_params.text.placeholder.address_2,
		}];

		if (search) {
			return _.find(fields, search);
		}

		return fields;
	},
	getFieldAttributes: function ($node) {
		var attrs = {};
		$.each($node[0].attributes, function (index, attribute) {
			attrs[attribute.name] = attribute.value;
		});

		return attrs;
	},
}

$(document.body).on('wc_address_i18n_ready', function () {
	avaTokommerceFrontend.init();

	setTimeout(function () {
		$(document.body).trigger('country_to_state_changed');
	}, 100);
});
}(jQuery));
