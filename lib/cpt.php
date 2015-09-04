<?php

namespace Roots\Sage\Cpt;

/* ~~~~~~~~~~~~~~~~~~~~~~
 * MENU ORDER AND CUSTOM POST TYPES
 * ~~~~~~~~~~~~~~~~~~~~*/

class MenuOrder {
	//use only as Singleton
	private static $extra_items = array();

	public static function add_item($custom_post_type) {
		self::$extra_items[] = $custom_post_type;
	}

	public static function make_menu() {
		$menu_order = array(
			'index.php', //Dashboard
			'edit.php?post_type=page', //Pages
			'edit.php' //Posts
		);
		foreach (self::$extra_items as $item) {
			array_push($menu_order, 'edit.php?post_type='.$item);
		}
		return array_merge($menu_order, array(
			'separator1', // First separator
      'upload.php', // Media
      'users.php', // Users
      'separator2', // Second separator
      'themes.php', // Appearance
      'plugins.php', // Plugins
      'options-general.php', // Settings
      'separator-last', // Last separator
		));
	}

}
add_filter( 'custom_menu_order',  __NAMESPACE__ . '\\MenuOrder::make_menu' );
add_filter( 'menu_order', __NAMESPACE__ . '\\MenuOrder::make_menu');



/*
 * CREATE CUSTOM POST TYPE
*/
class CustomPostType {
	private $name, $singular, $plural, $capability, $supports = array(), $taxonomies = array();

  function __construct( $name, $args = array() ) {

		//assign name
		$this->name = $name;

    //establish defaults
    $defaults = array(
      'singular' => ucfirst( $name ),
      'plural' => ucfirst( $name ) . 's',
      'capability' => 'post',
      'supports' => array('title', 'editor', 'thumbnail', 'attributes', 'revisions'),
			'dashicon' => null
    );
    $args = wp_parse_args( $args, $defaults );

    //assign settings to the object
    $this->singular = $args['singular'];
    $this->plural = $args['plural'];
    $this->capability = $args['capability'];
    $this->supports = $args['supports'];
		$this->dashicon = $args['dashicon'];

    //alert the admin menu that a new CPT has been created
		MenuOrder::add_item($this->name);

    //hook to create this CPT
    add_action( 'init', array($this, 'create_cpt') );
	}

	public function attach_taxonomy($taxonomy) {
		if ( $this->taxonomies[] = $taxonomy ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	function create_cpt() {
		$labels = array(
			'name'                => $this->plural,
			'singular_name'       => $this->singular,
			'menu_name'           => $this->plural,
			'parent_item_colon'   => 'Parent '.$this->plural,
			'all_items'           => 'All '.$this->plural,
			'view_item'           => 'View '.$this->singular,
			'add_new_item'        => 'Add New '.$this->singular,
			'add_new'             => 'Add New',
			'edit_item'           => 'Edit '.$this->singular,
			'update_item'         => 'Update '.$this->singular,
			'search_items'        => 'Search '.$this->plural,
			'not_found'           => $this->plural.' not found',
			'not_found_in_trash'  => $this->plural.' not found in Trash',
		);
		$args = array(
			'labels'              => $labels,
			'supports'            => $this->supports,
			'hierarchical'        => false,
			'public'              => true,
			'taxonomies'		  		=> $this->taxonomies,
			'menu_icon'						=> $this->dashicon,
			'show_ui'             => true,
			'show_in_admin_bar'   => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => $this->capability,
		);
		register_post_type($this->name, $args);
	}
}

/*
 * CREATE CUSTOM TAXONOMY
*/
class CustomTaxonomy {
	private $post_type, $tax_key, $tax_singular, $tax_plural, $terms = array(), $style, $default_value;

	function __construct($tax_key, $post_type, $args) {
		$this->tax_key = $tax_key;
		$this->post_type = $post_type;

		$defaults = array(
			'tax_singular' => ucfirst( $tax_key ),
			'tax_plural' => ucfirst( $tax_key ) . 's',
			'hierarchical' => FALSE,
			'style' => FALSE,
			'terms' => [],
			'default_value' => FALSE
		);
		$args = wp_parse_args( $args, $defaults );

		//set defaults as object properties
		$this->tax_singular = $args['tax_singular'];
		$this->tax_plural = $args['tax_plural'];
		$this->hierarchical = $args['hierarchical'];
		$this->style = $args['style'];
		$this->terms = $args['terms'];
		$this->default_term = $args['default_value'];

		//add actions
		add_action( 'init', array($this, 'add_meta') );

		//if style is set, get rid of the default meta boxes and add a new one
		if ( $this->style ) {
			add_action( 'admin_menu', array($this, 'remove_meta_boxes') );
			add_action( 'add_meta_boxes', array($this, 'add_meta_boxes') );
			add_action( 'save_post', array($this, 'save_post') );
		}
	}

	public function add_meta() {
		register_taxonomy($this->tax_key, $this->post_type,
			array(
				'labels' => array(
        	'name' => $this->tax_plural,
        	'singular_name' => $this->tax_singular
        ),
				'public' => true,
      	'hierarchical' => $this->hierarchical,
      	'show_ui' => true,
      	'show_admin_column' => true,
      	'show_tagcloud' => false,
      	'rewrite' => array(
					'slug' => 'products',
					'hierarchical' => true
				),
	    )
		);
		$terms_arr = get_terms( $this->tax_key, array('hide_empty' => false) );
		foreach ($terms_arr as $term_obj) {
			array_push($this->terms, $term_obj->name);
		}
		//remove the default from the array of terms and place it at the beginning
		$pos = array_search($this->default_term, $this->terms);
		if ( $pos !== FALSE ) {
			unset($this->terms[$pos]);
			array_unshift($this->terms, $this->default_term);
		}
	}

	public function remove_meta_boxes() {
		remove_meta_box( 'tagsdiv-'.$this->tax_key, $this->post_type, 'side' );
	}

	public function add_meta_boxes( $post ) {
		add_meta_box($this->tax_key, $this->tax_singular, array($this, 'meta_box_html'), $this->post_type, 'side', 'default' );
	}

	public function meta_box_html( $post ) {
		wp_nonce_field( 'radio_tax_meta_box', 'radio_tax_meta_box_nonce' );
		if ( $terms_arr = wp_get_post_terms( $post->ID, $this->tax_key ) ) {
			$term_obj = array_shift($terms_arr);
			$set_value = $term_obj->name;
		} else {
			$set_value = $this->default_term;
		}
		echo sprintf('<label for="%s"><strong>%s</strong></label><br />',
			$this->tax_key,
			$this->tax_singular
		);
		foreach ($this->terms as $term) {
			echo sprintf('<input type="radio" name="%s" value="%s" %s/>%s<br />',
				$this->tax_key,
				$term,
				$term === $set_value ? 'checked' : '',
				$term
			);
		}
	}

	public function save_post( $post_id ) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
		// Check if our nonce is set.
		if ( ! isset( $_POST['radio_tax_meta_box_nonce'] ) ) return;
		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['radio_tax_meta_box_nonce'], 'radio_tax_meta_box' ) ) return;
		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		// Check the user's permissions.
		if ( ! current_user_can( 'edit_page', $post_id ) ) return;
		// Make sure that it is set.
		if ( ! isset( $_POST[$this->tax_key] ) ) return;
		$term = $_POST[$this->tax_key];
		$possible_values = $this->terms;
		if ( !in_array($term, $possible_values) ) return;
		wp_set_post_terms( $post_id, array( $term ), $this->tax_key, false );
		return true;
	}
}
