<?php
// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BBWP_CustomFields{

  public $prefix = 'bbwpcustomfields';
  static $bbcf = array();


	/******************************************/
	/***** class constructor **********/
	/******************************************/
  public function __construct(){

		// get the plugin options/settings.
    self::$bbcf = SerializeStringToArray(get_option($this->prefix.'_options'));

		// add javascript and css to wp-admin dashboard.
    add_action( 'admin_enqueue_scripts', array($this, 'wp_admin_style_scripts') );

		//localization hook
		add_action( 'plugins_loaded', array($this, 'plugins_loaded') );

		//add settings page link to plugin activation page.
    add_filter( 'plugin_action_links_'.BBWP_CF_PLUGIN_FILE, array($this, 'plugin_action_links') );

		// Plugin activation hook
    register_activation_hook(BBWP_CF_PLUGIN_FILE, array($this, 'PluginActivation'));

		// plugin deactivation hook
    //register_deactivation_hook(BBWP_CF_PLUGIN_FILE, array($this, 'PluginDeactivation'));

  }// construct function end here


	/******************************************/
	/***** get plugin prefix with custom string **********/
	/******************************************/
  public function prefix($string = '', $underscore = "_"){

    return $this->prefix.$underscore.$string;

  }// prefix function end here.


	/******************************************/
	/***** localization function **********/
	/******************************************/
	public function plugins_loaded(){

		load_plugin_textdomain( 'bbwp-custom-fields', false, BBWP_CF_ABS . 'languages/' );

	}// plugin_loaded


	/******************************************/
	/***** add settings page link in plugin activation screen.**********/
	/******************************************/
  public function plugin_action_links( $links ) {

     $links[] = '<a href="'. esc_url(get_admin_url(null, 'admin.php?page='.$this->prefix)) .'">Settings</a>';
     return $links;

  }// localization function


	/******************************************/
  /***** Plugin activation function **********/
  /******************************************/
  public function PluginActivation() {

    $ver = "1.0";
    if(!(isset(self::$bbcf['ver']) && self::$bbcf['ver'] == $ver))
      $this->set_bbcf_option('ver', $ver);

  }// plugin activation


	/******************************************/
  /***** plugin deactivation function **********/
  /******************************************/
  /*public function PluginDeactivation(){

    delete_option($this->prefix.'_options');

	}// plugin deactivation*/


	/******************************************/
  /***** get option function**********/
  /******************************************/
  public function get_bbcf_option($key){

    if(isset(self::$bbcf[$key]))
      return self::$bbcf[$key];
    else
      return NULL;

  }// get_bbcf_option


	/******************************************/
  /***** Debug functions start from here **********/
  /******************************************/
  public function set_bbcf_option($key, $value){

      self::$bbcf[$key] = $value;
      update_option($this->prefix.'_options', ArrayToSerializeString(self::$bbcf));

	}// set_bbcf_option


	/******************************************/
  /***** add javascript and css to wp-admin dashboard. **********/
  /******************************************/
  public function wp_admin_style_scripts() {

    //if(isset($_GET['page']) && $_GET['page'] == $this->prefix){

      global $wp_scripts;
      $ui = $wp_scripts->query('jquery-ui-core');

      wp_enqueue_script('uploads');
      wp_enqueue_script( 'postbox' );
      wp_enqueue_media();

      if (is_ssl())
        $url = "https://code.jquery.com/ui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";
      else
        $url = "http://code.jquery.com/ui/{$ui->ver}/themes/smoothness/jquery-ui.min.css";

      wp_register_style( 'jquery-ui', $url, array(), $ui->ver);
      wp_enqueue_style('jquery-ui');

      wp_register_style( $this->prefix.'_wp_admin_css', BBWP_CF_URL . '/css/style.css', array('wp-color-picker'), '1.0.0' );
      wp_enqueue_style($this->prefix.'_wp_admin_css');

      wp_register_script( $this->prefix.'_wp_admin_script', BBWP_CF_URL . '/js/script.js', array('jquery', 'jquery-ui-sortable' ,'jquery-ui-datepicker', 'wp-color-picker'), '1.0.0' );
      wp_enqueue_script( $this->prefix.'_wp_admin_script' );


      //$js_variables = array('prefix' => $this->prefix."_");
      //wp_localize_script( $this->prefix.'_wp_admin_script', $this->prefix, $js_variables );

		//}

  }// wp_admin_style_scripts

} // BBWP_CustomFields class
