<?php
/*
 Plugin Name: Ajaxify For Wordpress
 GitHub Plugin URI: https://github.com/lupetalo/ajaxify-for-wordpress
 Version: 1.2
*/

// add main js file
add_action( 'wp_enqueue_scripts', function () {
	wp_register_script( 'ajaxify', plugins_url( '/assets/ajaxify.js' , __FILE__ ) );
	if (get_option('ajaxify_enabled')=="true"){
		wp_enqueue_script( 'ajaxify' );
	}
});


// add inline code to wp_head
add_action('wp_head', function (){ 
	// handle blanks and get options
	$requestDelay=get_option('requestDelay');
	$refresh=get_option('refresh');
	$elements=get_option('elements');
	$pronto_beforeload=get_option('pronto_beforeload');
	$pronto_render=get_option('pronto_render');
	$pronto_load=get_option('pronto_load');

	if (empty($elements)) $elements='body';
	if (empty($requestDelay)) $requestDelay='0';
	if (empty($refresh)) $refresh='false';

if (get_option('ajaxify_enabled')=="true"){?>
<script>
jQuery( document ).ready(function() {
	console.log( "ready!" );
	let ajaxify = new Ajaxify({
		selector: 'a[href^="<?php echo site_url();?>"]',
		elements: '<?php echo $elements;?>',
		requestDelay : '<?php echo $requestDelay;?>',
		refresh : '<?php echo $refresh;?>',
		
	});
	<?php if (!empty($pronto_beforeload)){?>
window.addEventListener('pronto.beforeload', function(event){
	<?php echo $pronto_beforeload;?>
	event.stopPropagation();
})
	<?php }?>
	<?php if (!empty($pronto_render)){?>
window.addEventListener('pronto.render', function(event){
	<?php echo $pronto_render;?>
	event.stopPropagation();
})
	<?php }?>
	<?php if (!empty($pronto_load)){?>
window.addEventListener('pronto.load', function(event){
	<?php echo $pronto_load;?>
	event.stopPropagation();
})
	<?php }?>
});
window.addEventListener('pronto.render', function(event){
	if (window.elementorFrontend && window.elementorFrontend!='undefined'){
		window.elementorFrontend.init();
		event.stopPropagation();
	}
	
})
</script>
<?php }});

// register settings
add_action( 'admin_init', function () {
	register_setting( 'ajaxify_for_wp', 'ajaxify_enabled', ['default'=>'false']);
	register_setting( 'ajaxify_for_wp', 'elements', ['default'=>'#content,#sidebar,#wpadminbar']);
	register_setting( 'ajaxify_for_wp', 'triggres', ['default'=>'a:not(.no-ajaxy)']);
	register_setting( 'ajaxify_for_wp', 'requestDelay', ['default'=>'0']);
	register_setting( 'ajaxify_for_wp', 'refresh', ['default'=>'false']);
	register_setting( 'ajaxify_for_wp', 'pronto_beforeload', ['default'=>null]);
	register_setting( 'ajaxify_for_wp', 'pronto_render', ['default'=>null]);
	register_setting( 'ajaxify_for_wp', 'pronto_load', ['default'=>null]);
});

// register options page
add_action('admin_menu', function () {
	add_options_page('Ajaxify for WordPress', 'Ajaxify for WP', 'manage_options', 'ajaxify_for_wp', 'ajaxify_for_wp_options_page');
});



function ajaxify_for_wp_options_page(){?>
<div class="wrap">
  <?php screen_icon(); ?>
  <h1>Ajaxify for WordPress</h1>
	<hr>
	<a href="https://4nf.org/" target="_blank">Visit Ajaxify website</a>
	<div>
		<p>Most websites have the following page structure:</p>
		<ul>
			<li>Header</li>
			<li>Sidebar(s)</li>
			<li>Content</li>
			<li>Footer</li>
			<li></li>
		</ul>
		<p>…where only few elements vary significantly.</p>
		<p>In the next sections you’ll find out how to load the varying elements only with this plugin,</p>
		<p>avoiding a full page refresh and the associated full round-trip and creating awesome page load time.<br>
		When ajaxifying a site, quite a few things have to be taken into account which this plugin takes care of… So it’s an entire Ajax framework!</p>
		<p>With Ajaxify you turn almost any standard website into a shiny new single page application (SPA) effortlessly in a few minutes.</p>
	</div>
	<hr>
  <form method="post" action="options.php">
		<?php settings_fields( 'ajaxify_for_wp' );?>
		
		<h3 class="title">Main Settings</h3>
		<a href="https://4nf.org/interface/#idselection" target="_blank">Reference</a>
		<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="elements">Ajaxify For WordPress enabled</label></th>
				<td>
					<select name="ajaxify_enabled">
						<option value="true" <?php selected(get_option('ajaxify_enabled'), "true"); ?>>Yes</option>
						<option value="false" <?php selected(get_option('ajaxify_enabled'), "false"); ?>>No</option>
					</select>
				</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="elements">Elements to refresh</label></th>
			<td>
				<input class="regular-text" type="text" id="elements" name="elements" value="<?php echo get_option('elements');?>" />
				<p class="description">Use CSS Selectors, for example: <code>#elementID</code> or <code>.elementClass</code><br>
				separate elements with comma <code>,</code> for example: <i>#elementID,.elementClass,footer</i></p>
			</td>
		</tr>
			
		<tr valign="top">
			<th scope="row"><label for="triggres">Elements that trigger ajax</label></th>
			<td>
				<input class="regular-text" type="text" id="triggres" name="triggres" value="<?php echo get_option('triggres');?>" />
				<p class="description">Use CSS Selectors, for example: <code>#elementID</code> or <code>.elementClass</code><br>
				separate elements with comma <code>,</code> for example: <i>#elementID,.elementClass,footer</i></p>
			</td>
		</tr>
		</table>
		
		<h3 class="title">Ajaxify Options</h3>
		<a href="https://4nf.org/interface/#options" target="_blank">Reference</a>
		<table class="form-table">	
			<tr valign="top">
				<th scope="row"><label for="refresh">Refresh</label></th>
				<td>
					<select name="refresh">
						<option value="true" <?php selected(get_option('refresh'), "true"); ?>>Yes</option>
						<option value="false" <?php selected(get_option('refresh'), "false"); ?>>No</option>
					</select>
					<p class="description">Refresh the page even if link clicked is current page</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="requestDelay">requestDelay</label></th>
				<td>
					<input class="regular-text" type="number" id="requestDelay" name="requestDelay" value="<?php echo get_option('requestDelay');?>" />
					<p class="description">in msec - Delay of Pronto request</p>
				</td>
			</tr>
		</table>
		
		<h3 class="title">Event's</h3>
		<a href="https://4nf.org/events/" target="_blank">Reference</a>
		<table class="form-table">	
			<tr valign="top">
				<th scope="row"><label for="pronto_beforeload"><code>pronto.beforeload</code></label></th>
				<td>
					<textarea name="pronto_beforeload" id="pronto_beforeload" class="large-text code" rows="5"><?php echo get_option('pronto_beforeload');?></textarea>
					<p class="description">Fired before new Pronto request is loaded</p>
				</td>
			</tr>	
			<tr valign="top">
				<th scope="row"><label for="pronto_render"><code>pronto.render</code></label></th>
				<td>
					<textarea name="pronto_render" id="pronto_render" class="large-text code" rows="5"><?php echo get_option('pronto_render');?></textarea>
					<p class="description">Fired after new Pronto request is rendered. <b>This is where you would reinitialise JS elements</b></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="pronto_load"><code>pronto.load</code></label></th>
				<td>
					<textarea name="pronto_load" id="pronto_load" class="large-text code" rows="5"><?php echo get_option('pronto_load');?></textarea>
					<p class="description">Fired after new Pronto request is loaded</p>
				</td>
			</tr>
		</table>
  <?php submit_button(); ?>
  </form>
  </div>
<?php
}