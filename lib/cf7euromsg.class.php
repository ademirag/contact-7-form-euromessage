<?php

class CF7EuroMsg{

  function __construct(){

    add_action("wpcf7_before_send_mail", array($this,"do_the_job"));
    add_action( 'admin_menu', array($this,"adminSubmenu"));
    add_action( 'admin_enqueue_scripts', array( $this, 'adminAssets' ) );
  }

  function log($info){
    $h = fopen(dirname(__FILE__)."/log.txt","w");
    fwrite($h,$info);
    fclose($h);
  }

  function do_the_job ($cf7) {
      cform7eurmsg_log(print_r($cf7,true));
  }

  function adminSubmenu(){
		add_submenu_page( 'wpcf7','Contact Form Euro Message','Contact Form Euro Message', 'manage_options', 'cf7-euromsg', array($this,'adminPage') );
	}

	function adminPage(){
    echo 'ok';
	}

  function adminAssets(){
    wp_enqueue_style( 'cf7-euromsg-css', CF7EURMSGURL.'lib/css/cf7euromsg-style.css' );
    wp_enqueue_script( 'cf7-euromsg-js', CF7EURMSGURL.'lib/js/cf7euromsg-script.js', array(), '1.0.0', true );
  }

}
