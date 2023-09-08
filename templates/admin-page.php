
    <div class="wrap">
       <h2><?php _e( 'Select Countries', 'ads-currency-switcher' ); ?></h2>
       <p><?php _e( ' The selected countries will be avaialble to put the price.', 'ads-currency-switcher' ) ?></p>
       <p><?php _e( 'Use this shortcode to show product anywhere. <b>[ads_get_price_by_id id="123"]</b> rememeber to change id with actual ID of product', 'ads-currency-switcher' ); ?></p>
       <form method="post">
           <?php
           settings_fields('country_options');
           do_settings_sections('country_options');

           $default_country_checked = get_option( 'adswcs_default_country', [] );

            echo '<h4>' . __('Please choose default country for the rest of the country you will not cover.', 'ads-currency-switcher') . '</h4>';
            echo '<select name="user_default_country">';
            foreach ($all_countries as $country) {
                $country_code = $country['code'];
                $country_name = __(  $country['name'], 'ads-currency-switcher' );
                $selected = ($country_code === $default_country_checked) ? 'selected' : '';
                echo "<option value='$country_code' $selected>$country_name</option>";
            }
            echo '</select>';

           ?>
           <div class="country-columns">
               <?php
               $selected_countries = get_option('adswcs_selected_countries_option', array());

               $total_countries = count($all_countries);
               $countries_per_column = ceil($total_countries / 4);

               $column_count = 0;

               foreach ($all_countries as $country) {
                $country_code = $country['code'];
                $country_name = __(  $country['name'], 'ads-currency-switcher' );
                $country_currency_symbol = $country['symbol'];
            
                $checked = in_array($country_code, $selected_countries) ? 'checked' : '';
            
                // Open a new column div at the start of each column
                if ($column_count % $countries_per_column === 0) {
                    echo '<div class="column">';
                }
            
                echo "<label><input type='checkbox' name='adswcs_selected_countries_option[]' value='$country_code' $checked> <span class='symbol'> $country_currency_symbol - </span>  $country_name </label><br>";
            
                $column_count++;
            
                // Close the column div at the end of each column
                if ($column_count % $countries_per_column === 0 || $column_count === $total_countries) {
                    echo '</div>';
                }
            }
               ?>
           </div>
           <?php
           wp_nonce_field('adswcs_country_nonce');

           submit_button('Save Selected Countries', 'primary', 'submit'); ?>
       </form>
       <hr>
       <p>
        <?php
         _e('This product includes GeoLite2 data created by MaxMind, available from', 'ads-currency-switcher'); ?>
        <a href="https://www.maxmind.com">https://www.maxmind.com</a>.
       </p>
   </div>
