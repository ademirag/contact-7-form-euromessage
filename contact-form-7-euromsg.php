<?php
/**
 * @package Contact_Form_7_EuroMsg
 * @version 1.0
 */
/*
Plugin Name: Contact Form 7 Euro Message
Plugin URI: http://wordpress.org/plugins/hello-dolly/
Description: Send data to a Euro Message list entered via Contact Form 7. Tested with Contact Form 7 version 4.5.1
Author: Rolakosta & Asım Demirağ
Version: 1.0
Author URI: http://rolakosta.com
Text Domain: cf7euromsg
*/
define('CF7EURMSGURL',plugin_dir_url(__FILE__));

define('PLUGIN_BASENAME',plugin_basename(__FILE__));

require( 'lib/cf7euromsg.class.php' );
$CF7EuroMsg = new CF7EuroMsg();


// enka_live_wsuer 7M4EOum3
