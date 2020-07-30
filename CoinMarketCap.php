<?php
/*
    Plugin Name: CoinMarketCap
    Description: Конвертер валют - Курс CoinMarketCap
    Author: MarsianinM
*/

// Подключаем cmc-functions.php
//require_once plugin_dir_path(__FILE__) . 'inc/cmc-functions.php';

if (!defined('ABSPATH')) {
    die('Invalid request.');
}

if (!class_exists('GtsSlider')) :

    define( 'wpgts_PLUGIN', __FILE__ );
    define( 'wpgts_PLUGIN_DIR', untrailingslashit( dirname( wpgts_PLUGIN ) ) );
    global $wpdb;

    //connect the class CoinMarketCap
    require_once(ABSPATH . 'wp-content/plugins/CoinMarketCap/Class/CoinMarketCap.php');

    //Processing request
    if (!empty($_POST)){
        function cmc_covert_form() {
            $CMC = new CoinMarketCap();
            $result = $CMC->getConvertedData($_POST);

            echo $result->data->quote->{$_POST['convert']}->price;
        }
        add_action( 'admin_post_nopriv_covert_form', 'cmc_covert_form' );
        add_action( 'admin_post_covert_form', 'cmc_covert_form' );
    }

    //creating the necessary tables in the database
    function tbl_install_map_cover()
    {
        $CMC = new CoinMarketCap();
        $sql_map_convert = "CREATE TABLE " . CoinMarketCap::returnPrefixtable($CMC->mapConvertDB) . " (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             created_at DATETIME,
             json LONGTEXT NOT NULL COLLATE utf8_general_ci,
             UNIQUE KEY id (id)
          );";

        $sql_convert = "CREATE TABLE " . CoinMarketCap::returnPrefixtable($CMC->convertDB) . " (
             id mediumint(9) NOT NULL AUTO_INCREMENT,
             created_at DATETIME,
             symbol_group VARCHAR(10) NOT NULL COLLATE utf8_general_ci,
             data LONGTEXT NOT NULL COLLATE utf8_general_ci,
             UNIQUE KEY id (id)
          );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_map_convert);
        dbDelta($sql_convert);

    }

    if ($wpdb->get_var("SHOW TABLES LIKE '" . CoinMarketCap::returnPrefixtable('cmc_map_convert') . "'") != CoinMarketCap::returnPrefixtable('cmc_map_convert')) {
        register_activation_hook(__FILE__, 'tbl_install_map_cover');
    }

	// styles and scripts for decoration
	function coin_market_cap_scripts() {
		wp_enqueue_style( 'custom-coin_market_cap', plugins_url('CoinMarketCap/assets/css/custom.css'), [], time() );
        wp_enqueue_script( 'form-coin_market_cap', plugins_url('CoinMarketCap/assets/js/form.js'), ['jquery'], time(), true );
	}
    add_action( 'wp_enqueue_scripts', 'coin_market_cap_scripts' );

    //shortcode for displaying the conversion form
    function getCoin(){
        $CMC = new CoinMarketCap();
        $response = $CMC->getMapConvert(5000);
        include( plugin_dir_path( __FILE__ ) . 'views/form.php');
    }
    add_shortcode( 'getcoin', 'getCoin' );

	//shortcode for displaying the latest conversions
    function getLastConvert(){
        $CMC = new CoinMarketCap();
        $response = $CMC->getLastCover();
        include( plugin_dir_path( __FILE__ ) . 'views/table.php');
    }
    add_shortcode( 'getLastConvert', 'getLastConvert' );

endif;