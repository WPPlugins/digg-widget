<?php
/*
Plugin Name: digg widget
Description: Adds a sidebar widget to digg links
Author: Chris Black
Version: 1.2
Author URI: http://cjbonline.org
*/

// This gets called at the plugins_loaded action
function widget_digg_init() {
	
	// Check for the required API functions
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;

	// This saves options and prints the widget's config form.
	function widget_digg_control() {
		$options = $newoptions = get_option('widget_digg');
		if ( $_POST['digg-submit'] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST['digg-title']));
			$newoptions['username'] = strip_tags(stripslashes($_POST['digg-username']));
			$newoptions['count'] = (int) $_POST['digg-count'];
			$newoptions['tags'] = explode(' ', trim(strip_tags(stripslashes($_POST['digg-tags']))));
		}
		if ( $options != $newoptions ) {
			$options = $newoptions;
			update_option('widget_digg', $options);
		}
	?>
				<div style="text-align:right">
				<label for="digg-title" style="line-height:35px;display:block;"><?php _e('Widget title:', 'widgets'); ?> <input type="text" id="digg-title" name="digg-title" value="<?php echo wp_specialchars($options['title'], true); ?>" /></label>
				<label for="digg-username" style="line-height:35px;display:block;"><?php _e('digg login:', 'widgets'); ?> <input type="text" id="digg-username" name="digg-username" value="<?php echo wp_specialchars($options['username'], true); ?>" /></label>
				<input type="hidden" name="digg-submit" id="digg-submit" value="1" />
				</div>
	<?php
	}

	// This prints the widget
	function widget_digg($args) {
		extract($args);
		$defaults = array('count' => 10, 'username' => 'wordpress');
		$options = (array) get_option('widget_digg');

		foreach ( $defaults as $key => $value )
			if ( !isset($options[$key]) )
				$options[$key] = $defaults[$key];
		?>
		<?php echo $before_widget; ?>
			<?php echo $before_title . "<a href='http://digg.com/users/{$options['username']}/news/dugg'>{$options['title']}</a>" . $after_title; ?>
			<script type="text/javascript">
				digg_id = 'digg-widget-container'; //make this id unique for each widget you put on a single page.
				digg_theme = 'digg-widget-unstyled';
				digg_title = 'Stories dugg by user <?php echo $options['username']; ?>';
				</script>
				<script type="text/javascript" src="http://digg.com/tools/widgetjs"></script>
				<script type="text/javascript" src="http://digg.com/tools/services?type=javascript&callback=diggwb&endPoint=/user/<?php echo $options['username']; ?>/dugg&count=10">
			</script>
			<?php echo $after_widget; ?>
<?php
	}

	// Tell Dynamic Sidebar about our new widget and its control
	register_sidebar_widget(array('digg', 'widgets'), 'widget_digg');
	register_widget_control(array('digg', 'widgets'), 'widget_digg_control');
	
}

// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
add_action('widgets_init', 'widget_digg_init');

?>
