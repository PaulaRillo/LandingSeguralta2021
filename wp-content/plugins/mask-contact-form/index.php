<?php
/*
*
* Plugin Name: 	Mask Contact Form
* Plugin URI:   https://www.criattus.com.br
* Description: 	Implemntar mascara nos campos do formulário de contato
* Version:      1.2
* Author: 		Marcus Vinicius
* Author URI:   http://www.marcusfs.com.br/
* License:      GPL2
*/

 class CRT_Mask_Input
 {

 	
 	function init()
 	{

 		add_action( 'wp_enqueue_scripts', array('CRT_Mask_Input','loadPlugin') );
 	}

 	function loadPlugin()
 	{
 		wp_register_script( 'masked-input', plugins_url( '/assets/jquery.mask.js', __FILE__ ), array( 'jquery' ) );
 		wp_register_script( 'script', plugins_url( '/assets/script.js', __FILE__ ), array( 'jquery' ) );
 		// For either a plugin or a theme, you can then enqueue the script:
   		wp_enqueue_script( 'masked-input' );
   		wp_enqueue_script( 'script' );
 	}


 }

 add_action( 'plugins_loaded', array('CRT_Mask_Input','init'));