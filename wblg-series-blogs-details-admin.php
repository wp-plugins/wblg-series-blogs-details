<?php
/**
plugin:	wblg-series-blogs-details [-admin]
**/
if(!function_exists('wblg_series_blogs_details_init')) {
	wblg_series_messages('no-plugin');
} else {
	add_filter('blog_name_variante', 'wblg_series_blogs_details_name_vars',10,3);
	add_filter('blog-name-suffix-alt', 'wblg_series_blogs_details_extras');
	add_filter('blog-name-prefix-alt', 'wblg_series_blogs_details_extras');
	add_filter('blog-name-suffix-main', 'wblg_series_blogs_details_extras');
	add_filter('blog-name-prefix-main', 'wblg_series_blogs_details_extras');
 	wblg_series_blogs_details_page();
}
function wblg_series_blogs_details_name_vars($content,$prefix_alt,$suffix_alt) {
	$prefix_main=get_site_option('blog_name_prefix');
	$prefix_main=apply_filters('blog-name-prefix-main',$prefix_main);
	$suffix_main=get_site_option('blog_name_suffix');
	$suffix_main=apply_filters('blog-name-suffix-main',$suffix_main);	
	if ( $prefix_main !='' && $prefix_alt!='' ) {
		$content=str_replace($prefix_main,$prefix_alt,$content);
	}
	if ( $suffix_main !='' && $suffix_alt!='' ) {
		$content=str_replace($suffix_main,$suffix_alt,$content);	
	}	
	return $content;
}
/* pp */
function wblg_series_blogs_details_extras($content) {
	return $content;
}
function wblg_series_blogs_details_page() {	
	if ( ! current_user_can( 'manage_options' ) )
		wp_die( __( 'You do not have sufficient permissions to manage plugins for this site.' ) );
	global $blog_id, $wpdb;
	
	/* POST */
	$action = isset( $_POST['action'] ) ? $_POST['action'] : 'splash';
	$updated = false;		

	/* post new option blog */
	if ( 'wblg_series_blogs_details-setnewoptions' == $action && isset( $_POST['blogid_local']) ) {
		check_admin_referer('set_newoptions');
		
		if ( is_multisite() ) {
			$blogid_local = (int) $_POST['blogid_local'];
			$blog = get_blog_details( $blogid_local );
		} else {
			$blog = get_bloginfo();
		}
		if($blog) {	
			global $wblg_series_blogs_details_new_slug;
			foreach($wblg_series_blogs_details_new_slug as $opts) {
				$opt=trim($opts);
				$val=trim(stripslashes($_POST[$opt]));
				update_option($opt,$_POST[$opt]);
			}
			$updated = true;
		} else {
			wp_die( __( 'The primary site you chose does not exist.' ) );
		}
	}
	/* post date creation blog */
	if ( 'wblg_series_blogs_details-setdatecreation' == $action 
		&& isset( $_POST['blogid_local']) && isset($_POST['date-registered']) ) {
		check_admin_referer('set_datecreation');
		
		if ( is_multisite() ) {
			$blogid_local = (int) $_POST['blogid_local'];
			$blog = get_blog_details( $blogid_local );
		} else {
			$blog = get_bloginfo();
		}
		if($blog) {
			update_blog_details( $blogid_local, array('registered'=>$_POST['date-registered']) );
			$updated = true;
		} else {
			wp_die( __( 'The primary site you chose does not exist.' ) );
		}
	}
	
	// define plugin used
	$plugns_extra_opts=array('Connections','WPPA');
	//
	/* post catg for plugn by blog */
	if ( 'wblg_series_blogs_details-setcategplugin' == $action && isset( $_POST['blogid_local']) ) {
		check_admin_referer('set_categplugin');
		
		if ( is_multisite() ) {
			$blogid_local = (int) $_POST['blogid_local'];
			$blog = get_blog_details( $blogid_local );
		} else {
			$blog = get_bloginfo();
		}
		if($blog) {
			foreach($plugns_extra_opts as $plugname) {
				$opt_name='blog_plgn_'.$plugname;
				$opt_value=$_POST[$opt_name];
				update_option( $opt_name,$opt_value);
			}
			$updated = true;
		} else {
			wp_die( __( 'The primary site you chose does not exist.' ) );
		}
	}
	?>
	<div class="wrap">
	<?php if ( $updated ) { ?>
	<div id="message" class="updated"><p><strong><?php _e( 'Settings saved.' ); ?></strong></p></div>
	<?php } ?>
	<h2><?php $blog_name=get_bloginfo('name'); echo $blog_name; ?></h2>
	<div id="poststuff">				
	<div class="postbox">
		<div class="handlediv" title="<?php __('Click to toggle'); ?>"><br></div>
		<h3 class="hndle"><?php _e('Set new options for this blog','wblg-series-blogs-details'); ?> </h3>
		<div class="inside">
			<form method="post">
				<table class="form-table">
				<?php
				global $wblg_series_blogs_details_new_text, $wblg_series_blogs_details_new_slug;
				foreach($wblg_series_blogs_details_new_slug as $opts) {
					$opt=trim($opts);
					$wblg_series_blogs_details_new=get_option($opt); $suggest_mail="";
					//$wblg_series_blogs_details_new=$opt;
					if($opt=='blog_coords_email' && $wblg_series_blogs_details_new=="") { 
						$suggest_mail="<span class=\"suggest\">". __('suggestion','wblg-series-blogs-details') ."</span>"; 
						$current_site = site_url();
						$blog_url=parse_url($current_site);
						
						// blog as subdomain... 
						// ???
						
						// blog under main site	
						$logn=str_replace('/','',$blog_url['path']);
						$host=str_replace('www.','',$blog_url['host']);
						
						if($logn=='') $logn='contact';
						$wblg_series_blogs_details_new=$logn."@".$host;
					}
					?>
					<tr class="form-field">
					<th scope="row"><?php echo $wblg_series_blogs_details_new_text[$opt]; ?></th>
					<td><input type="text" class="all-options" name="<?php echo $opt; ?>" value="<?php echo $wblg_series_blogs_details_new; ?>" />
						<?php echo $suggest_mail; ?>
					</td>
					</tr>	
					<?php
				}
				?>					
					<tr>
						<td><input type="hidden" name="action" value="wblg_series_blogs_details-setnewoptions" />
						<input type="hidden" name="blogid_local" value="<?php echo esc_attr( $blog_id ) ?>" />
					<?php if ( function_exists('wp_nonce_field') ) { wp_nonce_field('set_newoptions'); } ?></td>
					<td><input type="submit" name="submit" id="submit_options" class="button-primary" value="<?php _e("Save"); ?>" /></td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<!-- / Set new options -->
	
	<!-- // if multisite -->
	<?php
		if ( is_multisite() ) {
	?>
	<p><?php
		$network_blog_admin_url=network_admin_url()."site-info.php?id=".$blog_id;
	 _e( 'To manage other parameters go to: ','wblg-series-blogs-details'); ?><a href="<?php echo $network_blog_admin_url; ?>"><?php 
	echo $network_blog_admin_url; ?></a></p>
	
	<div class="postbox">
		<div class="handlediv" title="<?php __('Click to toggle'); ?>"><br></div>
		<h3 class="hndle"><?php _e('Set the creation date for this blog','wblg-series-blogs-details'); ?></h3>
		<div class="inside">
			
			<?php
			$details = get_blog_details( $blog_id );
			/* this eventualy can be use for prevent forbbiden action, but I don't see the risk...
			if ( !can_edit_network( $details->site_id ) )
				_e( 'You do not have permission to access this page.' );
			*/	
			?>
			<form method="post">
				<table class="form-table">
					<tr class="form-field">
						<th scope="row"><?php _ex( 'Registered', 'site' ) ?></th>
						<td><input name="date-registered" type="text" id="date_registered" value="<?php echo esc_attr( $details->registered ) ?>" /><span class="suggest">YYYY-MM-DD HR:MN:SC</span></td>
					</tr>
					<tr>
						<td><input type="hidden" name="action" value="wblg_series_blogs_details-setdatecreation" />
						<input type="hidden" name="blogid_local" value="<?php echo esc_attr( $blog_id ) ?>" />
					<?php if ( function_exists('wp_nonce_field') ) { wp_nonce_field('set_datecreation'); } ?></td>
					<td><input type="submit" name="submit" id="submit_creadate" class="button-primary" value="<?php _e("Save"); ?>" /></td>
					</tr>
				</table>
			</form>
		</div>			
	</div>
	<!-- // end if multisite -->
	<?php
		}
	?>
	<!-- / Set creation date -->
			
	<?php 
		if( !is_main_site($blog_id) && ( class_exists('cnCategoryObjects') || function_exists('wppa_album_select') ) ) {
		?>
		<div class="postbox">
			<div class="handlediv" title="<?php __('Click to toggle'); ?>"><br></div>
			<h3 class="hndle"><?php _e('Set the parent category of some major multisite extensions for this blog','wblg-series-blogs-details'); ?></h3>
			<div class="inside">
				<form method="post">
				<table class="form-table">
					<?php
					if( function_exists('wblg_series_sitewide_settings_init') ) {
						$not_set=__('Not set','wblg-series-blogs-details'); 
						$prefix_css='';
						$prefix_alt=get_site_option('blog_name_prefix_alt');
						$prefix_alt=apply_filters('blog-name-prefix-alt',$prefix_alt);
						if( $prefix_alt=='' ) { $prefix_alt=$not_set; $prefix_css='net-set'; }
						//
						$suffix_css='';
						$suffix_alt=get_site_option('blog_name_suffix_alt');
						$suffix_alt=apply_filters('blog-name-suffix-alt',$suffix_alt);
						if( $suffix_alt=='' ) { $suffix_alt=$not_set; $suffix_css='net-set'; }
						?>
						<tr class="form-field">
							<th scope="row"><?php _e( 'Filter on blogs names','wblg-series-blogs-details' ); ?></th>
							<td>
							<?php _e('Prefix: ','wblg-series-blogs-details'); ?>
								<input type="text" disabled="disabled" readonly="readonly" 
								class="shorted <?php echo $prefix_css; ?>"
								value="<?php echo $prefix_alt; ?>" />
							
							<?php _e('Suffix: ','wblg-series-blogs-details'); ?>
								<input type="text" disabled="disabled" readonly="readonly" 
								class="shorted <?php echo $suffix_css; ?>"
								value="<?php echo $suffix_alt;  ?>" />
														
							<span><?php _e('Prefix and Suffix can be set with','wblg-series-blogs-details'); ?>
							<a href="<?php echo admin_url('admin.php?page=wblg-series-sitewide-settings'); ?>">Sitewide Settings</a></span>					
							</td>
						<tr>
						<?php
					}
					if ( class_exists('cnCategoryObjects') ) {
						$plugn=$plugns_extra_opts[0];
						$extra_opt='blog_plgn_'.$plugn;
						$selected=get_option($extra_opt);
						/* specific */
						global $connections; $level=0; $CN_options="";						
						$CN_cats = new cnCategoryObjects;
						$CN_catlist=$CN_cats->buildCategoryRow('option', $connections->retrieve->categories(), $level, $selected);
						$CN_options=wblg_series_FilterOptions($CN_catlist,$prefix_alt,$suffix_alt,$not_set,$selected,$blog_name);
						//
						?>
						<tr class="form-field">
							<th scope="row"><?php _e( 'Address Book','wblg-series-blogs-details' ); ?> [<?php echo $plugn; ?>]</th>
							<td>
								<select name="<?php echo $extra_opt; ?>" id="blog_set_<?php echo $plugn; ?>">
									<option value="">--- <?php _e('Select'); ?> ---</option>
									<?php echo $CN_options; ?>
								</select>
								<span><?php _e('If the desired category doesn’t exist, please create it.','wblg-series-blogs-details'); ?>
								<a href="<?php echo admin_url('admin.php?page=connections_categories'); ?>"><?php _e('Add New Category'); ?></a></span>
							</td>
						</tr>
						<?php
					}
					if( function_exists('wppa_album_select') ) {
						$plugn=$plugns_extra_opts[1];
						$extra_opt='blog_plgn_'.$plugn;
						$selected=get_option($extra_opt);
						/* specific */
						$WPPA_catlist=wppa_album_select('', $selected, false, false, false, false, false, true);
						$WPPA_options=wblg_series_FilterOptions($WPPA_catlist,$prefix_alt,$suffix_alt,$not_set,$selected,$blog_name);
						//
						?>
						<tr class="form-field">
							<th scope="row"><?php _e( 'Photo Album','wblg-series-blogs-details' ) ?> [<?php echo $plugn; ?>]</th>
							<td>
								<select name="<?php echo $extra_opt; ?>" id="blog_set_<?php echo $plugn; ?>">
									<option value="">--- <?php _e('Select'); ?> ---</option>
									<?php echo $WPPA_options; ?>
								</select>
								<span><?php _e('If the desired category doesn’t exist, please create it.','wblg-series-blogs-details'); ?>
								<a href="<?php echo admin_url('admin.php?page=wppa_admin_menu'); ?>"><?php _e('Add New Category'); ?></a></span>
							</td>
						</tr>
						<?php
					}
					?>
					<tr>
						<td><input type="hidden" name="action" value="wblg_series_blogs_details-setcategplugin" />
						<input type="hidden" name="blogid_local" value="<?php echo esc_attr( $blog_id ) ?>" />
					<?php if ( function_exists('wp_nonce_field') ) { wp_nonce_field('set_categplugin'); } ?></td>
					<td><input type="submit" name="submit" id="submit_catplugn" class="button-primary" value="<?php _e("Save"); ?>" /></td>
					</tr>
				</table>
				</form>
			</div>			
		</div>
		<?php
 	} // not for main site
	?>
	<!-- / Set plugin linked -->
</div>
<?php
}
function wblg_series_FilterOptions($catlist,$prefix,$suffix,$not_set,$selected,$blog_name) {
	$Clist=explode('</option>',$catlist);	
	foreach($Clist as $list) {
		$transform=false;
		$optext=strstr($list,'>');
		$optext=trim(substr($optext,1));
		if($selected == '') {
			// attempt to find blog value			
			$blog_name_vars = apply_filters('blog_name_variante',$blog_name,$prefix,$suffix);
			if($blog_name == $optext) {
				$transform=true;
			} else if($blog_name_vars == $optext) {
				$transform=true;
			}
			if($transform) {
				$list=str_replace('<option ','<option selected="selected" ',$list)." (".__('suggestion, please save','wblg-series-blogs-details').")";
			}
		}
		
		if($prefix==$not_set && $suffix==$not_set) {
			$options.=$list."</option>";
		} else {
			// with strpos ie search string anywhere
			/* 
			$pos_prefix = strpos($list, $prefix);
			$pos_suffix = strpos($list, $suffix);
			if ( ($pos_prefix !== false) || ($pos_suffix !== false) ) {
				$options.=$list."</option>";
			}
			*/
			// with subtsr ie serach string in right place
			$len_prefix=strlen($prefix);
			$len_suffix=strlen($suffix)*-1;
			if( substr($optext,0,$len_prefix) == $prefix || substr($optext,$len_suffix) == $suffix ) {
				$options.=$list."</option>";
			}
		}
	}
	return $options;
}
?>