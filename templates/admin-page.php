<style>
.wrap{
    padding: 20px;
    border-radius: 10px;
    background: #fff;
}
    /* Style the checkboxes */
input[type="checkbox"] {
    appearance: none; /* Remove default checkbox appearance */
    -webkit-appearance: none;
    -moz-appearance: none;
    width: 16px; /* Set the desired width of the checkbox */
    height: 16px; /* Set the desired height of the checkbox */
    border: 1px solid #ccc; /* Add a border */
    border-radius: 3px; /* Add some border radius for rounded corners */
    outline: none; /* Remove the default focus outline */
    cursor: pointer; /* Show a pointer cursor when hovering */
    position: relative;
}

/* Style the checked state of the checkboxes */
input[type="checkbox"]:checked::before {
    content: "\2713"; /* Unicode checkmark character */
    display: block;
    width: 16px; /* Set the same width as the checkbox */
    height: 16px; /* Set the same height as the checkbox */
    background-color: #0073e6; /* Set the background color for checked checkboxes */
    border: 1px solid #0073e6; /* Set the border color for checked checkboxes */
    border-radius: 3px; /* Add some border radius for rounded corners */
    color: #fff; /* Set the text color for the checkmark */
    text-align: center;
    line-height: 16px; /* Center the checkmark vertically */
    position: absolute;
    top: 2px;
    left: 3px;
    font-size: 12px; /* Adjust the font size as needed */
}

/* Style the label text next to the checkboxes */
label {
    display: inline-block;
    margin-left: 8px;
    margin-top: 15px;
    font-size: 14px;
}

</style>
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
