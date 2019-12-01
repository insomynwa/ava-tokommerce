<?php
/**
 * Main dashboard template
 */
?><div id="ava-tokommerce-settings-page">
	<div class="ava-tokommerce-settings-page">
		<h1 class="cs-vui-title"><?php _e( 'Ava Tokommerce Settings', 'ava-tokommerce' ); ?></h1>
		<div class="cx-vui-panel">
			<cx-vui-tabs
				:in-panel="false"
				value="general-settings"
				layout="vertical">

				<?php do_action( 'ava-tokommerce/settings-page-template/tabs-start' ); ?>

				<cx-vui-tabs-panel
					name="general-settings"
					label="<?php _e( 'General settings', 'ava-tokommerce' ); ?>"
					key="general-settings">

					<cx-vui-switcher
						name="svg_uploads"
						label="<?php _e( 'SVG images upload status', 'ava-tokommerce' ); ?>"
						description="<?php _e( 'Enable or disable SVG images uploading', 'ava-tokommerce' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						return-true="enabled"
						return-false="disabled"
						v-model="pageOptions.svg_uploads.value">
					</cx-vui-switcher>

					<cx-vui-switcher
						name="ava_templates"
						label="<?php _e( 'Use Ava Templates', 'ava-tokommerce' ); ?>"
						description="<?php _e( 'Add Ava page templates and blocks to Elementor templates library.', 'ava-tokommerce' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						return-true="enabled"
						return-false="disabled"
						v-model="pageOptions.ava_templates.value">
					</cx-vui-switcher>

					<cx-vui-select
						name="widgets_load_level"
						label="<?php _e( 'Editor Load Level', 'ava-tokommerce' ); ?>"
						description="<?php _e( 'Choose a certain set of options in the widget’s Style tab by moving the slider, and improve your Elementor editor performance by selecting appropriate style settings fill level (from None to Full level)', 'ava-tokommerce' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						:options-list="pageOptions.widgets_load_level.options"
						v-model="pageOptions.widgets_load_level.value">
					</cx-vui-select>

				</cx-vui-tabs-panel>

				<cx-vui-tabs-panel
					name="api-integrations"
					label="<?php _e( 'Integrations', 'ava-tokommerce' ); ?>"
					key="api-integrations">

					<div
						class="cx-vui-subtitle"
						v-html="'<?php _e( 'Google Maps', 'ava-tokommerce' ); ?>'"></div>

					<cx-vui-input
						name="google-map-api-key"
						label="<?php _e( 'Google Map API Key', 'ava-tokommerce' ); ?>"
						description="<?php
							echo sprintf( esc_html__( 'Create own API key, more info %1$s', 'ava-tokommerce' ),
								htmlspecialchars( "<a href='https://developers.google.com/maps/documentation/javascript/get-api-key' target='_blank'>here</a>", ENT_QUOTES )
							);
						?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="pageOptions.api_key.value"></cx-vui-input>

					<cx-vui-switcher
						name="google-map-disable-api-js"
						label="<?php _e( 'Disable Google Maps API JS file', 'ava-tokommerce' ); ?>"
						description="<?php _e( 'Disable Google Maps API JS file, if it already included by another plugin or theme', 'ava-tokommerce' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						return-true="true"
						return-false="false"
						v-model="pageOptions.disable_api_js.value.disable">
					</cx-vui-switcher>

					<div
						class="cx-vui-subtitle"
						v-html="'<?php _e( 'MailChimp', 'ava-tokommerce' ); ?>'"></div>

					<cx-vui-input
						name="mailchimp-api-key"
						label="<?php _e( 'MailChimp API key', 'ava-tokommerce' ); ?>"
						description="<?php
							echo sprintf( esc_html__( 'Input your MailChimp API key %1$s', 'ava-tokommerce' ),
								htmlspecialchars( "<a href='http://kb.mailchimp.com/integrations/api-integrations/about-api-keys' target='_blank'>About API Keys</a>", ENT_QUOTES )
							);
						?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="pageOptions['mailchimp-api-key'].value"></cx-vui-input>

					<cx-vui-input
						name="mailchimp-list-id"
						label="<?php _e( 'MailChimp list ID', 'ava-tokommerce' ); ?>"
						description="<?php
							echo sprintf( esc_html__( 'Input MailChimp list ID %1$s', 'ava-tokommerce' ),
								htmlspecialchars( "<a href='http://kb.mailchimp.com/integrations/api-integrations/about-api-keys' target='_blank'>About Mailchimp List Keys</a>", ENT_QUOTES )
							);?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="pageOptions['mailchimp-list-id'].value"></cx-vui-input>

					<cx-vui-switcher
						name="mailchimp-double-opt-in"
						label="<?php _e( 'Double opt-in', 'ava-tokommerce' ); ?>"
						description="<?php _e( 'Send contacts an opt-in confirmation email when they subscribe to your list.', 'ava-tokommerce' ); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						return-true="true"
						return-false="false"
						v-model="pageOptions['mailchimp-double-opt-in'].value">
					</cx-vui-switcher>

					<div
						class="cx-vui-subtitle"
						v-html="'<?php _e( 'Instagram', 'ava-tokommerce' ); ?>'"></div>

					<cx-vui-input
						name="insta-access-token"
						label="<?php _e( 'Access Token', 'ava-tokommerce' ); ?>"
						description="<?php
							echo sprintf( esc_html__( 'Read more about how to get Instagram Access Token %1$s', 'ava-tokommerce' ),
								htmlspecialchars( "<a href='https://instagram.pixelunion.net/' target='_blank'>here</a>", ENT_QUOTES )
							); ?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="pageOptions.insta_access_token.value"></cx-vui-input>

					<div
						class="cx-vui-subtitle"
						v-html="'<?php _e( 'Weatherbit.io API (APIXU API deprecated)', 'ava-tokommerce' ); ?>'"></div>

					<cx-vui-input
						name="weatherstack-api-key"
						label="<?php _e( 'Weatherbit.io API Key', 'ava-tokommerce' ); ?>"
						description="<?php
						echo sprintf( esc_html__( 'Create own Weatherbit.io API key, more info %1$s', 'ava-tokommerce' ),
							htmlspecialchars( "<a href='https://www.weatherbit.io/' target='_blank'>here</a>", ENT_QUOTES )
						);?>"
						:wrapper-css="[ 'equalwidth' ]"
						size="fullwidth"
						v-model="pageOptions.weather_api_key.value"></cx-vui-input>

				</cx-vui-tabs-panel>

				<cx-vui-tabs-panel
					name="available-widgets"
					label="<?php _e( 'Available Widgets', 'ava-tokommerce' ); ?>"
					key="available-widgets">

					<div class="ava-tokommerce-settings-page__disable-all-widgets">
						<div class="cx-vui-component__label">
							<span v-if="disableAllWidgets"><?php _e( 'Disable All Widgets', 'ava-tokommerce' ); ?></span>
							<span v-if="!disableAllWidgets"><?php _e( 'Enable All Widgets', 'ava-tokommerce' ); ?></span>
						</div>

						<cx-vui-switcher
							name="disable-all-avaliable-widgets"
							:prevent-wrap="true"
							:return-true="true"
							:return-false="false"
							@input="disableAllWidgetsEvent"
							v-model="disableAllWidgets">
						</cx-vui-switcher>
					</div>

					<div class="ava-tokommerce-settings-page__avaliable-controls">
						<div
							class="ava-tokommerce-settings-page__avaliable-control"
							v-for="(option, index) in pageOptions.avaliable_widgets.options">
							<cx-vui-switcher
								:key="index"
								:name="`avaliable-widget-${option.value}`"
								:label="option.label"
								:wrapper-css="[ 'equalwidth' ]"
								return-true="true"
								return-false="false"
								v-model="pageOptions.avaliable_widgets.value[option.value]"
							>
							</cx-vui-switcher>
						</div>
					</div>

				</cx-vui-tabs-panel>

				<cx-vui-tabs-panel
					name="available-extensions"
					label="<?php _e( 'Available Extensions', 'ava-tokommerce' ); ?>"
					key="available-extensions">

					<div class="ava-tokommerce-settings-page__avaliable-controls">
						<div
							class="ava-tokommerce-settings-page__avaliable-control"
							v-for="(option, index) in pageOptions.avaliable_extensions.options">
							<cx-vui-switcher
								:key="index"
								:name="`avaliable-extension-${option.value}`"
								:label="option.label"
								:wrapper-css="[ 'equalwidth' ]"
								return-true="true"
								return-false="false"
								v-model="pageOptions.avaliable_extensions.value[option.value]"
							>
							</cx-vui-switcher>
						</div>
					</div>

				</cx-vui-tabs-panel>

				<?php do_action( 'ava-tokommerce/settings-page-template/tabs-end' ); ?>
			</cx-vui-tabs>
		</div>
	</div>
</div>
