<?php
/**
 * Created by PhpStorm.
 */
if (function_exists("call_ms_function_ver")) {
  $version = 1;
  if (call_ms_function_ver(__FILE__, $version)) {
    //already ran
    print "\e[33mSKIPPING: " . __FILE__ . "<br />\n\e[0m";
    return;
  }
}
print "\e[32mRUNNING: " . __FILE__ . "<br />\n\e[0m";

updateFieldUIType('vehicle_type', 'Vehicles', 16);
