<?php
/**
 * @package wblg-series-blogs-details
 * @version 1.0
*/
/*
Plugin Name: WBLG series Blogs Details
Plugin URI: http://wordpress.org/extend/plugins/wblg-series-blogs-details/
Version: 1.0
Description: Add contact details for blogs.
Author: Alain Bariel
Author URI: http://www.la-dame-du-lac.com/
Text Domain: wblg-series-blogs-details
Domain Path: /langs/
License: GPL2
*/
/*  Copyright 2012  Alain Bariel  (email : lancelot@la-dame-du-lac.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* What to do when the plugin is activated? */
if ( function_exists('register_activation_hook') ) {
	register_activation_hook(__FILE__, 'wblg_series_blogs_details_install');
}
/* What to do when the plugin is deactivated? */
if ( function_exists('register_uninstall_hook') ) {
	register_uninstall_hook(__FILE__, 'wblg_series_blogs_details_uninstall');
}

add_action('admin_notices', 'wblg_series_blogs_details_alert');

function wblg_series_blogs_details_alert() {
	// core admin needed		
	if( !function_exists('wblg_series_messages') ) { // this mean core admin is missing
		$actiontodo=false;
		$name_needed="WBLG series Core Admin";		
		$plugin_needed="wblg-series-core-admin";
		$plugin_needed_file=$plugin_needed."/".$plugin_needed.".php";
		//
		if(!is_wp_error(validate_plugin($plugin_needed_file)) ) {			
			$actiontodo= sprintf( __('activate first %s: ','wblg-series-blogs-details'), $name_needed );
			$actiontodo.=' <a href="'.wp_nonce_url('plugins.php?action=activate&amp;plugin='.$plugin_needed_file.'&amp;plugin_status=all', 'activate-plugin_'.$plugin_needed_file).'" title="'.esc_attr__('Activate this plugin').'" class="edit">'.__('Activate').'</a>';
		} else {
			$actiontodo= sprintf( __('download and activate first %s: ','wblg-series-blogs-details'), $name_needed );
			$actiontodo.= ' <a href="'.self_admin_url( 'plugin-install.php?tab=plugin-information&amp;plugin='.$plugin_needed.'&amp;TB_iframe=true&amp;width=600&amp;height=550' ).'" class="thickbox" title="'.esc_attr( sprintf( __( 'More information about %s' ), $name_needed ) ).'">'.__( 'Details' ).'</a>';		
		}
		if($actiontodo) {
			$plugin=plugin_basename( __FILE__ );
			if ( is_plugin_active($plugin) ) {
				unset($_GET['activate']);
				unset($_GET['activate-multi']);
				deactivate_plugins($plugin, true);
			}
			$plugin_Datas=get_plugin_data(__FILE__);
			?>
			<div id="message" class="error">
				<p><strong><?php echo $name_needed.' '.__('(required)'); ?></strong>
				<?php echo " – ".sprintf( __('%s deactived','wblg-series-blogs-details'), $plugin_Datas['Name'] )." – ".$actiontodo; ?></p>
			</div>
			<?php
		}
	}
}

function wblg_series_blogs_details_install() {
	// nothing to do else create options
}

function wblg_series_blogs_details_uninstall() {
	if(!current_user_can('manage_options')) {
		wp_die( __('You do not have sufficient permissions to deactivate plugins for this site.'));
	}
	/* delete options  */
	global $wblg_series_blogs_details_new_slug;
	foreach($wblg_series_blogs_details_new_slug as $opt) {
		delete_option(trim($opt)); 
	}
	return;
}

/**********************************************************/
/* commons params
/**********************************************************/
add_action('init', 'wblg_series_blogs_details_init',0);
function wblg_series_blogs_details_init() {
	/**
	admin and front
	**/
	// load language
	load_plugin_textdomain('wblg-series-blogs-details', false, dirname( plugin_basename( __FILE__ ) ).'/langs/' );

	// params
	$GLOBALS['wblg_series_blogs_details_new_text']=array(
		"blog_coords_name"=> __('Address Name','wblg-series-blogs-details'),
		"blog_coords_address"=> __('Address','wblg-series-blogs-details'),
		"blog_coords_zipcode"=> __('Zipcode','wblg-series-blogs-details'),
		"blog_coords_city"=> __('City','wblg-series-blogs-details'),
		"blog_coords_country"=> __('Country','wblg-series-blogs-details'),
		"blog_coords_phone"=> __('Phone','wblg-series-blogs-details'),
		"blog_coords_fax"=> __('Telecopy','wblg-series-blogs-details'),
		"blog_coords_mobile"=> __('Cellphone','wblg-series-blogs-details'),
		"blog_coords_email"=> __('Email','wblg-series-blogs-details')
	);
	$wblg_series_blogs_details_new_text=$GLOBALS['wblg_series_blogs_details_new_text'];
	$GLOBALS['wblg_series_blogs_details_new_slug']= array_keys($wblg_series_blogs_details_new_text);
	$wblg_series_blogs_details_new_slug=$GLOBALS['wblg_series_blogs_details_new_slug'];

	foreach($wblg_series_blogs_details_new_slug as $opt) {
		add_option(trim($opt),""); 
	}
	/**
	only front
	**/
	if(!defined('WP_ADMIN')) {
		add_shortcode('blog_coords','wblg_series_blogs_details_shortcodes');
	} else {
		//add_action( 'admin_enqueue_scripts', 'wblg_series_blogs_details_admin_js_css' );
	}
}
/*
function wblg_series_blogs_details_admin_js_css() {	
	wp_register_style( 'wblg_series_blogs_details_css', plugins_url('style.css', __FILE__) );
	wp_enqueue_style( 'wblg_series_blogs_details_css' );
	wp_register_script( 'wblg_series_blogs_details_js', plugins_url('wblg-series-blogs_details-settings.js', __FILE__)  );
	wp_enqueue_script( 'wblg_series_blogs_details_js' );
}
*/
/**********************************************************/
/* function widget & shortcodes
/**********************************************************/
function wblg_series_blogs_details_coords($blog_coords,$show) {
	$blog_value = ""; $dash="";
	if($show=='blog_coords_bline') { $dash=" — "; }
	if($blog_coords['blog_coords_name'])    $blog_value.=$blog_coords['blog_coords_name']."\n";
	if($blog_coords['blog_coords_address']) $blog_value.=$dash.$blog_coords['blog_coords_address']."\n";
	if($blog_coords['blog_coords_zipcode']) $blog_value.=$dash.$blog_coords['blog_coords_zipcode'];
	if($blog_coords['blog_coords_city'])    $blog_value.=" ".$blog_coords['blog_coords_city'];
	if($blog_coords['blog_coords_country']) $blog_value.=" (".$blog_coords['blog_coords_country'].")\n";
	if($blog_coords['blog_coords_phone'])   $blog_value.=$blog_coords['blog_coords_phone']."\n";
	if($blog_coords['blog_coords_fax'])     $blog_value.=$blog_coords['blog_coords_fax']."\n";
	if($blog_coords['blog_coords_mobile'])  $blog_value.=$blog_coords['blog_coords_mobile']."\n";
	if($blog_coords['blog_coords_email'])   $blog_value.=$dash.make_clickable($blog_coords['blog_coords_email'])."\n";
	//make_clickable(antispambot($blog_coords['blog_coords_email']));
	return $blog_value;
}

/**********************************************************/
/* widget
/**********************************************************/
function wblg_series_blogs_details_register_widget() {
	// load language
	load_plugin_textdomain('wblg-series-blogs-details', false, dirname( plugin_basename( __FILE__ ) ).'/langs/' );
	register_widget("WP_widget_wblg_series_blogs_details");
}
add_action("widgets_init", "wblg_series_blogs_details_register_widget");

class WP_widget_wblg_series_blogs_details extends WP_widget {
	var $defaults = array('blog_coords_email' => 1);
    /** constructor */
    function WP_widget_wblg_series_blogs_details() {
    	$this->defaults = array(
    		'title' => get_bloginfo('name')
    		//'blog_coords_email' => 1
    	);
    	$widget_ops = array('description' => __('Display the contact details of each blog saved in Wblg Series Blogs Details Settings','wblg-series-blogs-details') );
        parent::WP_Widget(false, $name = __('Contact details','wblg-series-blogs-details'), $widget_ops);	
    }

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $this->defaults['title'] );
		echo $before_widget;
		echo $before_title.$title.$after_title;
		global $wblg_series_blogs_details_new_text;
		foreach($wblg_series_blogs_details_new_text as $kopt => $vopt) {
			if(!$instance[$kopt]) {
				$blog_coords_widget[$kopt]=get_option($kopt);
			}
		}
		echo nl2br( wblg_series_blogs_details_coords($blog_coords_widget,'blog_coords_block') );
		echo $after_widget;
	}
	public function update( $new_instance, $old_instance ) {
		global $wblg_series_blogs_details_new_text;
		foreach($wblg_series_blogs_details_new_text as $kopt => $vopt) {
    		$new_instance[$kopt] = ($new_instance[$kopt] == '') ? $this->defaults[$kopt]:$new_instance[$kopt];
		}
    	return $new_instance;
	}

	public function form( $instance ) {
		$instance = array_merge($this->defaults, $instance);
		?>
		<input type="hidden" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $this->defaults['title']; ?>" />
		<p><?php _e( 'Values to hidden','wblg-series-blogs-details' ); ?></p>
			<?php
			global $wblg_series_blogs_details_new_text;
			foreach($wblg_series_blogs_details_new_text as $kopt => $vopt) {
				$regkopt_id=$this->get_field_id( $kopt );
				$regkopt_name=$this->get_field_name( $kopt );
				?>
				<p>
				<input type="checkbox" id="<?php echo $regkopt_id; ?>" name="<?php echo $regkopt_name; ?>" value="1" 
				<?php echo ($instance[$kopt] == '1') ? 'checked="checked"':''; ?> />
				<label for="<?php echo $regkopt_id; ?>"><?php echo $vopt; ?></label>
				</p>
				<?php
			}
			?>
		<?php 
	}
}

/**********************************************************/
/* shortcode
/**********************************************************/
/**
 shortcode usage:

[blog_coords] default: display='block', hidden='email'
[blog_coords display='block'] // with breakline
[blog_coords display='bline'] // for baseline

display value can be: 'block' 'bline' 'name' 'address' 'zipcode' 'city' 'phone' 'fax' 'mobile' 'email'

[blog_coords hidden='name']
[blog_coords hidden='address']

hidden value can be: 'name' 'address' 'zipcode' 'city' 'phone' 'fax' 'mobile' 'email' or combined ex: 'zipcode,fax,email'

full example
[blog_coords display='bline' hidden='phone,fax,mobile,email'] 
**/
function wblg_series_blogs_details_shortcodes($atts, $content = null) {
	// shortcoding attributs
	extract( shortcode_atts( array(
      'display' => 'block', // default mode value
	  'hidden' => 'zipcode,email' // default hidden value
	), $atts ) );
	$show='blog_coords_'.esc_attr($display);
	$hide_params=explode(",", esc_attr($hidden) );
	foreach($hide_params as $_hide) {
		$_Hide[]='blog_coords_'.$_hide;
	}
	// get values to return
	global $wblg_series_wideset_new_slug;
	$wblg_series_wideset_value=array();
	foreach($wblg_series_wideset_new_slug as $opts) {
		$opt=trim($opts);
		if(!in_array($opt,$_Hide)) {
			$val=get_option($opt); 
			// single value
			$blog_coords[$opt]=trim($val);
		}
	}
	$blog_value=wblg_series_blogs_details_coords($blog_coords,$show);
	// Block address values
	$blog_coords['blog_coords_block']=nl2br($blog_value);
	// Baseline address values
	$blog_coords['blog_coords_bline']=$blog_value;
	return $blog_coords[$show];
}
?>