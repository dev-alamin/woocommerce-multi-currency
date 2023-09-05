
    <div class="wrap">
       <h2><?php _e( 'Select Countries to show price field in product page', 'ads-currency-switcher' ); ?></h2>
       <form method="post">
           <?php
           settings_fields('country_options');
           do_settings_sections('country_options');
           ?>
           <div class="country-columns" style="display: flex;">
               <?php
               $selected_countries = get_option('selected_countries_option', array());

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
                    echo '<div class="column" style="margin-right:20px;">';
                }
            
                echo "<label><input type='checkbox' name='selected_countries_option[]' value='$country_code' $checked> <span class='symbol'> $country_currency_symbol - </span>  $country_name </label><br>";
            
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
   </div>
