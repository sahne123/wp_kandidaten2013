<?php
/*
Plugin Name: Kandidaten2013
Plugin URI: 
Description: Wordpress Plugin zum Einbinden von Kandidaten-Daten aus kandidaten2013.de
Version: 0.9 BETA
Author: @sahne123
Author URI: 
*/

// Uncomment for assistance from WordPress in debugging.
define('WP_DEBUG', true);

class wp_kandidaten {

    /**
     * Constructor.
     */
    function wp_kandidaten () {
        // empty for now
    }
	
	// Add Styles of Plugin
	function kandidaten_styles() {
		$url = get_option('siteurl');
		$url_css = $url . '/wp-content/plugins/wp_kandidaten/style.css';
		echo "<link rel='stylesheet' type='text/css' href='" . $url_css . "' />\n";	
	}
	
   
    function displayShortcode ($atts, $content = null) {
		
        if( $websitecontent = @file("http://www.kandidaten2013.de/kandidaten/w".$content) )
        {
			return join("", $websitecontent);
		}	
    }
	
}

$wp_kandidaten = new wp_kandidaten();
	add_shortcode('kandidat', array($wp_kandidaten, 'displayShortcode'));

if (function_exists('add_action'))
	add_action('wp_print_styles', array($wp_kandidaten, 'kandidaten_styles'));

class kandidaten_Widget extends WP_Widget {
	function kandidaten_Widget() {
		// Konstruktor
		$wp_options = array('description'=>'Widget zum anzeigen von Kandidaten');
		parent::WP_Widget(false, $name = 'Kandidaten-Widget');
	}

	function widget($args, $instance) {
		// Ausgabefunktion
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;
			
			if( $websitecontent = @file("http://www.kandidaten2013.de/kandidaten/ws".$instance['ID']) )
			{
				echo join("", $websitecontent);
			}	

		echo $after_widget;
	}

	function update($new_instance, $old_instance) {
		// Speichern der Optionen
		
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['ID'] = strip_tags( $new_instance['ID'] );

		return $instance;
	}

	function form($instance) {
		// Formular des Widgets im Backend
		$default_settings = array( 'title' => 'Unsere Kandidaten', 'ID' => '0' );
		$instance = wp_parse_args( (array) $instance, $default_settings ); 

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Titel:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:100%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'ID' ); ?>">Kandidaten-ID (0=zufällig):</label>
			<input id="<?php echo $this->get_field_id( 'ID' ); ?>" name="<?php echo $this->get_field_name( 'ID' ); ?>" value="<?php echo $instance['ID']; ?>" style="width:100%;" />
		</p>
		<?php
		
	}
}

function register_widgets() {
	register_widget('kandidaten_Widget');
}

add_action( 'widgets_init', 'register_widgets' );


?>