
    <div class="wrap">
    <h2><?php esc_html_e( 'Select Countries', 'ads-currency-switcher' ); ?></h2>
    <p><?php esc_html_e( 'The selected countries will be available to put the price.', 'ads-currency-switcher' ) ?></p>
    <p><?php echo wp_kses_post( 'Use this shortcode to show the product anywhere. <b>[ads_get_price_by_id id="123"]</b> remember to change the ID with the actual ID of the product', 'ads-currency-switcher' ); ?></p>

       <form method="post">
           <?php
           settings_fields('country_options');
           do_settings_sections('country_options'); 
           ?>
           <div class="country-columns">
               <?php
               $selected_countries = get_option('adswcs_selected_countries_option', array());

               foreach ($all_countries as $country) {
                    $country_code = $country['code'];
                    $country_name = _x($country['name'], 'country name', 'ads-currency-switcher');
                    $country_currency_symbol = $country['symbol'];
                
                    $checked = in_array($country_code, $selected_countries) ? 'checked' : '';
                }

               ?>
                <div class="adswcs-choose-country single-block">
                    <h1 class="wp-heading-inline"><?php esc_html_e( 'Choose the country you will put price for', 'ads-currency-switcher' ); ?></h1>
                    <select name="adswcs_selected_countries_option[]" data-placeholder="<?php esc_html_e('Begin typing the name of country', 'ads-currency-switcher'); ?>" multiple class="adswcs-country-chosen-select">
                        <?php
                        foreach ($all_countries as $country) {
                            $isSelected = in_array($country['code'], $selected_countries) ? 'selected' : '';
                            echo "<option value='" . esc_attr($country['code']) . "' $isSelected>" . esc_html($country['name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

            <?php
            $default_country_checked = get_option( 'adswcs_default_country', [] );
                echo '<div class="adswcs-choose-country single-block">';
                echo '<h1 class="wp-heading-inline">' . esc_html('Please choose default country for the rest of the country you will not cover.', 'ads-currency-switcher') . '</h1>';
                echo '<select name="user_default_country">';
            foreach ($all_countries as $country) {
                $country_code = $country['code'];
                $country_name = _x($country['name'], 'Country Name', 'ads-currency-switcher');
                $selected = ($country_code === $default_country_checked) ? 'selected' : '';
                echo "<option value='" . esc_attr($country_code) . "' " . esc_attr($selected) . ">" . esc_html($country_name) . "</option>";
            }
            echo '</select>';
            echo '</div>';
            ?>
            </div>

           <?php 

           wp_nonce_field('adswcs_country_nonce');
           submit_button('Save Selected Countries', 'primary', 'submit'); ?>
       </form>
       <hr>
       <p>
        <?php
         esc_attr_e('This product includes GeoLite2 data created by MaxMind, available from', 'ads-currency-switcher'); ?>
        <a href="https://www.maxmind.com">https://www.maxmind.com</a>.
       </p>
   </div>