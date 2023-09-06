<?php 
namespace ADSWCS;

class Frontend{
    public function __construct(){
        new \ADSWCS\Frontend\WC_Price_Shortcode();
    }
}