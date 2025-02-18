<?php
	if ( ! class_exists( 'Open_Workshop_Network' ) ) {

		class Open_Workshop_Network {
			var $tax_cap = array(
				'assign_terms'			=> 'own_assign_terms',
				'manage_terms'			=> 'own_manage_terms',
				'edit_terms'			=> 'own_edit_terms',
				'delete_terms'			=> 'own_delete_terms'
			);

			var $post_cap = array(
				'read_post'             => 'read_workshop',
				'delete_post'           => 'delete_workshop',

				'edit_post'             => 'edit_workshop',
				'edit_posts'            => 'edit_workshops',
				'edit_others_posts'     => 'edit_others_workshops',

				'publish_posts'         => 'publish_workshops',
				'read_private_posts'    => 'read_private_workshops',
			);

			public function __construct() {
				add_action( 'init', array( &$this, 'create_workshop_post_type' ) );
				add_action( 'init', array( &$this, 'create_materials_taxonomy' ) );
				add_action( 'init', array( &$this, 'create_disciplines_taxonomy' ) );
				add_action( 'init', array( &$this, 'create_services_taxonomy' ) );
				add_action( 'init', array( &$this, 'create_tools_taxonomy' ) );
				add_action( 'acf/init', array( &$this, 'add_advanced_custom_fields' ) );
				add_action( 'admin_init', array( &$this, 'add_role_and_capabilities' ) );
				// add_action( 'switch_theme', array( &$this, 'add_role_and_capabilities' ) );

				add_action( 'after_setup_theme', array( &$this, 'setup' ) );
				add_action( 'wp_enqueue_scripts', array( &$this, 'styles' ) );
				add_action( 'wp_enqueue_scripts', array( &$this, 'scripts' ) );
				add_filter( 'pre_get_posts' , array( &$this, 'archive_order') );
				add_filter( 'bbp_before_get_breadcrumb_parse_args', array( &$this, 'breadcrump_options' ) );
				add_filter( 'private_title_format', array( &$this, 'private_title_format' ) );
				add_filter( 'excerpt_more', array( &$this, 'new_excerpt_more' ) );

				// AJAX
				add_action( 'wp_ajax_nopriv_own_workshop', array( &$this, 'ajax' ) );
				add_action( 'wp_ajax_own_workshop', array( &$this, 'ajax' ) );
			}

			function new_excerpt_more( $more ) {
				return '...';
			}

			public function setup() {
				add_theme_support( 'post-thumbnails' );
				remove_theme_support( 'post-formats' );
				remove_theme_support( 'custom-background' );
				remove_theme_support( 'custom-header' );

				update_option( 'thumbnail_size_w', 128 );
				update_option( 'thumbnail_size_h', 128 );
				update_option( 'medium_size_w', 512 );
				update_option( 'medium_size_h', 512 );
				update_option( 'large_size_w', 1024 );
				update_option( 'large_size_h', 1024 );
			}

			public function styles() {
				wp_register_style( 'mapbox', 'https://api.tiles.mapbox.com/mapbox.js/v3.3.1/mapbox.css', array(), '1', 'all' );
				wp_register_style( 'normalize', get_stylesheet_directory_uri() . '/css/normalize.min.css', array(), '1', 'all' );
				wp_register_style( 'nprogress', get_stylesheet_directory_uri() . '/css/nprogress.css', array(), '1', 'all' );
				wp_register_style( 'own-main-style', get_stylesheet_directory_uri() . '/css/main.css', array(), '1', 'all' );
				wp_register_style( 'own-desktop-style', get_stylesheet_directory_uri() . '/css/desktop.css', array(), '1', 'all' );
				wp_register_style( 'nouislider', get_stylesheet_directory_uri() . '/css/jquery.nouislider.min.css', array(), '1', 'all' );

				wp_enqueue_style( 'mapbox' );
				wp_enqueue_style( 'normalize' );
				wp_enqueue_style( 'nprogress' );
				wp_enqueue_style( 'own-main-style' );
				wp_enqueue_style( 'own-desktop-style' );
				wp_enqueue_style( 'nouislider' );
			}

			public function scripts() {
				wp_register_script( 'mapbox', 'https://api.tiles.mapbox.com/mapbox.js/v3.3.1/mapbox.js' );
				wp_register_script( 'own-main', get_stylesheet_directory_uri() . '/js/main.js' );
				wp_register_script( 'fitvids', get_stylesheet_directory_uri() . '/js/jquery.fitvids.js' );
				wp_register_script( 'nprogress', get_stylesheet_directory_uri() . '/js/nprogress.js' );
				wp_register_script( 'nouislider', get_stylesheet_directory_uri() . '/js/jquery.nouislider.min.js' );

				$home_id = get_option( 'page_on_front' );

				$variables = array(
					'ajax_url' => admin_url( 'admin-ajax.php' ) . '?action=own_workshop',
					'map' => array(
						'center' => array(
							'lat' => get_field( 'map_center', $home_id )['lat'],
							'lng' => get_field( 'map_center', $home_id )['lng']
						),
						'zoom' => get_field( 'map_zoom_level', $home_id )
					)
				);

				wp_localize_script( 'own-main', 'OWN', $variables );

				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'mapbox' );
				wp_enqueue_script( 'own-main' );
				wp_enqueue_script( 'fitvids' );
				wp_enqueue_script( 'nprogress' );
				wp_enqueue_script( 'nouislider' );
			}

			public function archive_order( $query ) {
				if ( $query->get( 'post_type' ) == 'own_workshop' ) {
					$query->set( 'orderby' , 'title' );
					$query->set( 'order' , 'asc' );
				}
				return $query;
			}

			public function breadcrump_options() {
				$args['include_home']    = false;
				$args['include_root']    = false;
				$args['include_current'] = true;
				return $args;
			}

			public function private_title_format() {
				return '%s';
			}

			public function ajax() {
				header( "Content-Type: application/json" );

				$query = new WP_Query( "post_type=OWN_Workshop&posts_per_page=-1&post_status=publish" );
				$output = array(
					"type" => "FeatureCollection",
					"features" => [],
				);

				while( $query->have_posts() ) {
					$query->the_post();
					$feature['type'] = "Feature";

					// Geometry
					$geometry['type'] = "Point";
					$geometry['coordinates'] = array( floatval( get_field( 'location' )['lng'] ), floatval( get_field( 'location' )['lat'] ) );
					$feature['geometry'] = $geometry;

					// Properties
					$properties = array();
					$properties['name'] = get_the_title();
					$properties['icon'] = wp_get_attachment_thumb_url( get_field( 'logo' ) );
					$properties['text'] = get_field( 'description' );
					$properties['url'] = get_field( 'url' );

					$properties['opened'] = get_field( 'opened' );

					if ( get_field( 'images' )[0]['image'] != "" )
						$properties['photo'] = wp_get_attachment_image_src( get_field( 'images' )[0]['image'], 'medium' )[0];

					$properties['permalink'] = get_permalink();
					$properties['colour'] = get_field( 'arrow_colour' );
					$properties['location'] = get_field( 'arrow_location' );

					$properties['disciplines'] = "";
					$disc_array = wp_get_post_terms( get_the_ID(), 'own_disciplines' );
					$disc_count = count( $disc_array );

					for ( $i = 0; $i < $disc_count; $i++ ) {
						$properties['disciplines'] .= '<label for="disciplines_' . $disc_array[$i]->slug . '">' . $disc_array[$i]->name . '</label>';
						if ( $disc_count > 1 && $i < $disc_count - 2 ) $properties['disciplines'] .= ", ";
						if ( $disc_count > 1 && $i == $disc_count - 2 ) $properties['disciplines'] .= " and ";
					}

					// Taxonomies
					$taxonomies = array();

						// Services
						foreach ( wp_get_post_terms( get_the_ID(), 'own_services' ) as $service )
							$taxonomies['services'][] = $service->slug;

						// Disciplines get_the_ID(),
						foreach ( wp_get_post_terms( get_the_ID(), 'own_disciplines' ) as $service )
							$taxonomies['disciplines'][] = $service->slug;

						// Materials
						foreach ( wp_get_post_terms( get_the_ID(), 'own_materials' ) as $service )
							$taxonomies['materials'][] = $service->slug;

						// Tools
						foreach ( wp_get_post_terms( get_the_ID(), 'own_tools' ) as $service )
							$taxonomies['tools'][] = $service->slug;

					$properties['taxonomies'] = $taxonomies;

					if ( current_user_can( 'edit_post', get_the_ID() ) )
						$properties['admin'] = get_edit_post_link( get_the_ID() );

					$feature['properties'] = $properties;

					// Add to features
					$output['features'][] = $feature;
				}

				echo json_encode( $output );
				exit;
			}

			public function add_role_and_capabilities() {
				add_role( 'owner', 'Workshop Owner', array(
					'own_assign_terms' => true,
					'edit_workshop' => true,
					'edit_workshops' => true,
					'read_workshop' => true,
					'read' => true,
					'upload_files' => true
				) );

				$administrator = get_role( 'administrator' );
				$administrator->add_cap( 'own_assign_terms' );
				$administrator->add_cap( 'own_manage_terms' );
				$administrator->add_cap( 'own_edit_terms' );
				$administrator->add_cap( 'own_delete_terms' );
				$administrator->add_cap( 'read_workshop' );
				$administrator->add_cap( 'delete_workshop' );
				$administrator->add_cap( 'edit_workshop' );
				$administrator->add_cap( 'edit_workshops' );
				$administrator->add_cap( 'edit_others_workshops' );
				$administrator->add_cap( 'publish_workshops' );
				$administrator->add_cap( 'read_private_workshops' );
			}

			public function create_workshop_post_type() {
				$labels = array(
					'name'                => _x( 'Workshops', 'Post Type General Name', 'text_domain' ),
					'singular_name'       => _x( 'Workshop', 'Post Type Singular Name', 'text_domain' ),
					'menu_name'           => __( 'Workshops', 'text_domain' ),
					'parent_item_colon'   => __( 'Parent:', 'text_domain' ),
					'all_items'           => __( 'All Workshops', 'text_domain' ),
					'view_item'           => __( 'View Workshop', 'text_domain' ),
					'add_new_item'        => __( 'Add New Workshop', 'text_domain' ),
					'add_new'             => __( 'Add New', 'text_domain' ),
					'edit_item'           => __( 'Edit Workshop', 'text_domain' ),
					'update_item'         => __( 'Update Workshop', 'text_domain' ),
					'search_items'        => __( 'Search Workshops', 'text_domain' ),
					'not_found'           => __( 'Not found', 'text_domain' ),
					'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
				);
				$rewrite = array(
					'slug'                => 'workshop',
					'with_front'          => true,
					'pages'               => true,
					'feeds'               => false,
				);
				$args = array(
					'label'               => __( 'own_workshop', 'text_domain' ),
					'description'         => __( 'An open workshop location', 'text_domain' ),
					'labels'              => $labels,
					'supports'            => array( 'title', 'author' ),
					'taxonomies'          => array( 'own_materials', 'own_disciplines' ),
					'hierarchical'        => false,
					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'show_in_nav_menus'   => true,
					'show_in_admin_bar'   => true,
					'menu_position'       => 5,
					'can_export'          => true,
					'has_archive'         => true,
					'exclude_from_search' => false,
					'publicly_queryable'  => true,
					'rewrite'             => $rewrite,
					'capabilities'        => $this->post_cap,
					'menu_icon'           => 'dashicons-location-alt'
				);
				register_post_type( 'own_workshop', $args );
			}

			public function create_materials_taxonomy() {
				$labels = array(
					'name'                       => _x( 'Materials', 'Taxonomy General Name', 'text_domain' ),
					'singular_name'              => _x( 'Material', 'Taxonomy Singular Name', 'text_domain' ),
					'menu_name'                  => __( 'Materials', 'text_domain' ),
					'all_items'                  => __( 'All Materials', 'text_domain' ),
					'parent_item'                => __( 'Parent Material', 'text_domain' ),
					'parent_item_colon'          => __( 'Parent Material:', 'text_domain' ),
					'new_item_name'              => __( 'New Material Name', 'text_domain' ),
					'add_new_item'               => __( 'Add New Material', 'text_domain' ),
					'edit_item'                  => __( 'Edit Material', 'text_domain' ),
					'update_item'                => __( 'Update Material', 'text_domain' ),
					'separate_items_with_commas' => __( 'Separate materials with commas', 'text_domain' ),
					'search_items'               => __( 'Search Materials', 'text_domain' ),
					'add_or_remove_items'        => __( 'Add or remove material', 'text_domain' ),
					'choose_from_most_used'      => __( 'Choose from the most used material', 'text_domain' ),
					'not_found'                  => __( 'Not Found', 'text_domain' ),
				);
				$rewrite = array(
					'slug'                       => 'material',
					'with_front'                 => true,
					'hierarchical'               => false,
				);
				$args = array(
					'labels'                     => $labels,
					'hierarchical'               => true,
					'public'                     => true,
					'show_ui'                    => true,
					'show_admin_column'          => true,
					'show_in_nav_menus'          => true,
					'show_tagcloud'              => false,
					'query_var'                  => 'material',
					'rewrite'                    => $rewrite,
					'capabilities'               => $this->tax_cap
				);
				register_taxonomy( 'own_materials', array( 'own_workshop' ), $args );
			}

			public function create_disciplines_taxonomy() {
				$labels = array(
					'name'                       => _x( 'Discipline', 'Taxonomy General Name', 'text_domain' ),
					'singular_name'              => _x( 'Discipline', 'Taxonomy Singular Name', 'text_domain' ),
					'menu_name'                  => __( 'Disciplines', 'text_domain' ),
					'all_items'                  => __( 'All Disciplines', 'text_domain' ),
					'parent_item'                => __( 'Parent Discipline', 'text_domain' ),
					'parent_item_colon'          => __( 'Parent Discipline:', 'text_domain' ),
					'new_item_name'              => __( 'New Discipline Name', 'text_domain' ),
					'add_new_item'               => __( 'Add New Discipline', 'text_domain' ),
					'edit_item'                  => __( 'Edit Discipline', 'text_domain' ),
					'update_item'                => __( 'Update Discipline', 'text_domain' ),
					'separate_items_with_commas' => __( 'Separate disciplines with commas', 'text_domain' ),
					'search_items'               => __( 'Search Disciplines', 'text_domain' ),
					'add_or_remove_items'        => __( 'Add or remove discipline', 'text_domain' ),
					'choose_from_most_used'      => __( 'Choose from the most used discipline', 'text_domain' ),
					'not_found'                  => __( 'Not Found', 'text_domain' ),
				);
				$rewrite = array(
					'slug'                       => 'discipline',
					'with_front'                 => true,
					'hierarchical'               => false,
				);
				$args = array(
					'labels'                     => $labels,
					'hierarchical'               => true,
					'public'                     => true,
					'show_ui'                    => true,
					'show_admin_column'          => true,
					'show_in_nav_menus'          => true,
					'show_tagcloud'              => false,
					'query_var'                  => 'discipline',
					'rewrite'                    => $rewrite,
					'capabilities'               => $this->tax_cap
				);
				register_taxonomy( 'own_disciplines', array( 'own_workshop' ), $args );
			}

			public function create_services_taxonomy() {
				$labels = array(
					'name'                       => _x( 'Service', 'Taxonomy General Name', 'text_domain' ),
					'singular_name'              => _x( 'Service', 'Taxonomy Singular Name', 'text_domain' ),
					'menu_name'                  => __( 'Services', 'text_domain' ),
					'all_items'                  => __( 'All Services', 'text_domain' ),
					'parent_item'                => __( 'Parent Service', 'text_domain' ),
					'parent_item_colon'          => __( 'Parent Service:', 'text_domain' ),
					'new_item_name'              => __( 'New Service Name', 'text_domain' ),
					'add_new_item'               => __( 'Add New Service', 'text_domain' ),
					'edit_item'                  => __( 'Edit Service', 'text_domain' ),
					'update_item'                => __( 'Update Service', 'text_domain' ),
					'separate_items_with_commas' => __( 'Separate services with commas', 'text_domain' ),
					'search_items'               => __( 'Search Services', 'text_domain' ),
					'add_or_remove_items'        => __( 'Add or remove service', 'text_domain' ),
					'choose_from_most_used'      => __( 'Choose from the most used service', 'text_domain' ),
					'not_found'                  => __( 'Not Found', 'text_domain' ),
				);
				$rewrite = array(
					'slug'                       => 'service',
					'with_front'                 => true,
					'hierarchical'               => false,
				);
				$args = array(
					'labels'                     => $labels,
					'hierarchical'               => true,
					'public'                     => true,
					'show_ui'                    => true,
					'show_admin_column'          => true,
					'show_in_nav_menus'          => true,
					'show_tagcloud'              => false,
					'query_var'                  => 'service',
					'rewrite'                    => $rewrite,
					'capabilities'               => $this->tax_cap
				);

				register_taxonomy( 'own_services', array( 'own_workshop' ), $args );
			}

			public function create_tools_taxonomy() {
				$labels = array(
					'name'                       => _x( 'Tool', 'Taxonomy General Name', 'text_domain' ),
					'singular_name'              => _x( 'Tool', 'Taxonomy Singular Name', 'text_domain' ),
					'menu_name'                  => __( 'Tools', 'text_domain' ),
					'all_items'                  => __( 'All Tools', 'text_domain' ),
					'parent_item'                => __( 'Parent Tool', 'text_domain' ),
					'parent_item_colon'          => __( 'Parent Tool:', 'text_domain' ),
					'new_item_name'              => __( 'New Tool Name', 'text_domain' ),
					'add_new_item'               => __( 'Add New Tool', 'text_domain' ),
					'edit_item'                  => __( 'Edit Tool', 'text_domain' ),
					'update_item'                => __( 'Update Tool', 'text_domain' ),
					'separate_items_with_commas' => __( 'Separate tools with commas', 'text_domain' ),
					'search_items'               => __( 'Search Tools', 'text_domain' ),
					'add_or_remove_items'        => __( 'Add or remove tool', 'text_domain' ),
					'choose_from_most_used'      => __( 'Choose from the most used tool', 'text_domain' ),
					'not_found'                  => __( 'Not Found', 'text_domain' ),
				);
				$rewrite = array(
					'slug'                       => 'tool',
					'with_front'                 => true,
					'hierarchical'               => false,
				);
				$args = array(
					'labels'                     => $labels,
					'hierarchical'               => true,
					'public'                     => true,
					'show_ui'                    => true,
					'show_admin_column'          => true,
					'show_in_nav_menus'          => true,
					'show_tagcloud'              => false,
					'query_var'                  => 'tool',
					'rewrite'                    => $rewrite,
					'capabilities'               => $this->tax_cap
				);
				register_taxonomy( 'own_tools', array( 'own_workshop' ), $args );
			}

			public function add_advanced_custom_fields() {
				if( function_exists('acf_add_local_field_group') ):

					acf_add_local_field_group(array(
						'key' => 'group_5508d3d59fb6d',
						'title' => 'Homepage',
						'fields' => array(
							array(
								'key' => 'field_5508d3db4c7d1',
								'label' => 'Links',
								'name' => 'links',
								'type' => 'repeater',
								'instructions' => '',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'min' => 1,
								'max' => 5,
								'layout' => 'table',
								'button_label' => 'Add Link',
								'sub_fields' => array(
									array(
										'key' => 'field_5508d3e74c7d2',
										'label' => 'Title',
										'name' => 'title',
										'type' => 'text',
										'instructions' => '',
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array(
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
										'readonly' => 0,
										'disabled' => 0,
									),
									array(
										'key' => 'field_5508d3fe4c7d3',
										'label' => 'URI',
										'name' => 'uri',
										'type' => 'text',
										'instructions' => '',
										'required' => 1,
										'conditional_logic' => 0,
										'wrapper' => array(
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'default_value' => '',
										'placeholder' => '',
										'prepend' => '',
										'append' => '',
										'maxlength' => '',
										'readonly' => 0,
										'disabled' => 0,
									),
								),
								'collapsed' => '',
							),
							array(
								'key' => 'field_5508d4204c7d4',
								'label' => 'Map Center',
								'name' => 'map_center',
								'type' => 'google_map',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'center_lat' => '51.5075',
								'center_lng' => '-0.1243',
								'zoom' => 5,
								'height' => 200,
							),
							array(
								'key' => 'field_5508d44c4c7d5',
								'label' => 'Map Zoom Level',
								'name' => 'map_zoom_level',
								'type' => 'number',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => 11,
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'min' => 1,
								'max' => 19,
								'step' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
						),
						'location' => array(
							array(
								array(
									'param' => 'page',
									'operator' => '==',
									'value' => '70',
								),
							),
						),
						'menu_order' => 0,
						'position' => 'acf_after_title',
						'style' => 'seamless',
						'label_placement' => 'top',
						'instruction_placement' => 'label',
						'hide_on_screen' => array(
							0 => 'custom_fields',
						),
						'active' => 1,
						'description' => '',
					));

					acf_add_local_field_group(array(
						'key' => 'group_54fa366764808',
						'title' => 'Workshops',
						'fields' => array(
							array(
								'key' => 'field_55567a4226b7c',
								'label' => 'Opened',
								'name' => 'opened',
								'type' => 'date_picker',
								'instructions' => 'Date workshop first opened.',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'display_format' => 'd/m/Y',
								'return_format' => 'Y',
								'first_day' => 1,
							),
							array(
								'key' => 'field_54fa380bffd73',
								'label' => 'Short Description',
								'name' => 'description',
								'type' => 'textarea',
								'instructions' => 'Write a short 140 character description that charactarises your workshop.',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'maxlength' => 140,
								'rows' => 2,
								'new_lines' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
							array(
								'key' => 'field_5523ebc696b5b',
								'label' => 'Phone Number',
								'name' => 'phone_number',
								'type' => 'text',
								'instructions' => 'Provide your public telephone number (if you have one).',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
								'maxlength' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
							array(
								'key' => 'field_54fa31015c5aa',
								'label' => 'URL',
								'name' => 'url',
								'type' => 'url',
								'instructions' => 'Provide the URL of the workshop\'s website.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
							),
							array(
								'key' => 'field_54fa3082be7cb',
								'label' => 'Pin Location',
								'name' => 'location',
								'type' => 'google_map',
								'instructions' => 'Mark the exact location of the workshop\'s entrance.',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'center_lat' => '',
								'center_lng' => '',
								'zoom' => 16,
								'height' => 200,
							),
							array(
								'key' => 'field_5523ec59d4747',
								'label' => 'Address',
								'name' => 'address',
								'type' => 'textarea',
								'instructions' => 'Enter the address of your workshop, without the organisation\'s name.',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'maxlength' => '',
								'rows' => '',
								'new_lines' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
							array(
								'key' => 'field_54fa37ffffd72',
								'label' => 'Logo',
								'name' => 'logo',
								'type' => 'image',
								'instructions' => 'Upload a square crop of the workshops logo. Ensure a 10% border of empty space to prevent the logo touching the edge of it\'s container.',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'return_format' => 'id',
								'preview_size' => 'thumbnail',
								'library' => 'uploadedTo',
								'min_width' => 150,
								'min_height' => 150,
								'min_size' => '',
								'max_width' => 1024,
								'max_height' => 1024,
								'max_size' => '',
								'mime_types' => '',
							),
							array(
								'key' => 'field_54fb8a40d739f',
								'label' => 'Arrow Colour',
								'name' => 'arrow_colour',
								'type' => 'color_picker',
								'instructions' => 'Select the primary colour of your brand as an accent colour on the map.',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '#000000',
							),
							array(
								'key' => 'field_5569939e0886c',
								'label' => 'Arrow Location',
								'name' => 'arrow_location',
								'type' => 'select',
								'instructions' => '',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'choices' => array(
									'top' => 'Top',
									'right' => 'Right',
									'bottom' => 'Bottom',
									'left' => 'Left',
								),
								'default_value' => array(
									'top' => 'top',
								),
								'allow_null' => 0,
								'multiple' => 0,
								'ui' => 0,
								'ajax' => 0,
								'placeholder' => '',
								'disabled' => 0,
								'readonly' => 0,
								'return_format' => 'value',
							),
							array(
								'key' => 'field_5523ebf896b5c',
								'label' => 'Email Address',
								'name' => 'email_address',
								'type' => 'email',
								'instructions' => 'Provide your public email address (if you have one).',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => '',
								'prepend' => '',
								'append' => '',
							),
							array(
								'key' => 'field_67b4f937d394b',
								'label' => 'Bluesky',
								'name' => 'bluesky',
								'type' => 'text',
								'instructions' => 'Provide the handle of the workshop\'s Bluesky account, if one exists.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => 'username',
								'prepend' => '@',
								'append' => '',
								'maxlength' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
							array(
								'key' => 'field_67b4f91bd394a',
								'label' => 'TikTok',
								'name' => 'tiktok',
								'type' => 'text',
								'instructions' => 'Provide the handle of the workshop\'s TikTok account, if one exists.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => 'username',
								'prepend' => '@',
								'append' => '',
								'maxlength' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
							array(
								'key' => 'field_552566f5d6eb2',
								'label' => 'Instagram',
								'name' => 'instagram',
								'type' => 'text',
								'instructions' => 'Provide the handle of the workshop\'s Instagram account, if one exists.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => 'workshopname',
								'prepend' => 'instagram.com/',
								'append' => '',
								'maxlength' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
							array(
								'key' => 'field_55256c88d6eb6',
								'label' => 'YouTube',
								'name' => 'youtube',
								'type' => 'text',
								'instructions' => 'Provide the handle of the workshop\'s Facebook account, if one exists.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => 'workshopname',
								'prepend' => 'youtube.com/',
								'append' => '',
								'maxlength' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
							array(
								'key' => 'field_54fa31a3878a8',
								'label' => 'Facebook',
								'name' => 'facebook',
								'type' => 'text',
								'instructions' => 'Provide the handle of the workshop\'s Facebook account, if one exists.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => 'workshopname',
								'prepend' => 'http://facebook.com/',
								'append' => '',
								'maxlength' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
							array(
								'key' => 'field_55256c40d6eb5',
								'label' => 'Google Group',
								'name' => 'google_group',
								'type' => 'text',
								'instructions' => 'Provide the handle of the workshop\'s Google Group email address, if one exists.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => 'workshop-name',
								'prepend' => '',
								'append' => '@googlegroups.com',
								'maxlength' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
							array(
								'key' => 'field_54fa31355c5ab',
								'label' => 'Twitter',
								'name' => 'twitter',
								'type' => 'text',
								'instructions' => 'Provide the handle of the workshop\'s Twitter account, if one exists.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'placeholder' => 'username',
								'prepend' => '@',
								'append' => '',
								'maxlength' => '',
								'readonly' => 0,
								'disabled' => 0,
							),
							array(
								'key' => 'field_5523ec1296b5d',
								'label' => 'Long Description',
								'name' => 'long_description',
								'type' => 'wysiwyg',
								'instructions' => 'Write a longer description about your space.',
								'required' => 0,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'default_value' => '',
								'tabs' => 'all',
								'toolbar' => 'basic',
								'media_upload' => 0,
								'delay' => 0,
							),
							array(
								'key' => 'field_54fa36a34bb7b',
								'label' => 'Images',
								'name' => 'images',
								'type' => 'repeater',
								'instructions' => 'Upload one of more images of the inside of the workshop.',
								'required' => 1,
								'conditional_logic' => 0,
								'wrapper' => array(
									'width' => '',
									'class' => '',
									'id' => '',
								),
								'min' => 1,
								'max' => 5,
								'layout' => 'table',
								'button_label' => 'Add Image',
								'sub_fields' => array(
									array(
										'key' => 'field_54fa36ac4bb7c',
										'label' => 'Image',
										'name' => 'image',
										'type' => 'image',
										'instructions' => '',
										'required' => 0,
										'conditional_logic' => 0,
										'wrapper' => array(
											'width' => '',
											'class' => '',
											'id' => '',
										),
										'return_format' => 'id',
										'preview_size' => 'medium',
										'library' => 'uploadedTo',
										'min_width' => '',
										'min_height' => '',
										'min_size' => '',
										'max_width' => '',
										'max_height' => '',
										'max_size' => '',
										'mime_types' => '',
									),
								),
								'collapsed' => '',
							),
						),
						'location' => array(
							array(
								array(
									'param' => 'post_type',
									'operator' => '==',
									'value' => 'own_workshop',
								),
							),
						),
						'menu_order' => 0,
						'position' => 'normal',
						'style' => 'seamless',
						'label_placement' => 'left',
						'instruction_placement' => 'label',
						'hide_on_screen' => array(
							0 => 'the_content',
							1 => 'excerpt',
							2 => 'custom_fields',
							3 => 'discussion',
							4 => 'comments',
							5 => 'slug',
							6 => 'format',
							7 => 'page_attributes',
							8 => 'featured_image',
							9 => 'categories',
							10 => 'tags',
							11 => 'send-trackbacks',
						),
						'active' => 1,
						'description' => '',
					));

				endif;
			}
		}

		$OWN = new Open_Workshop_Network();
	}

	function OWN_Theme_Tax_List( $tax ) {
		$list = "";
		$list_array = wp_get_post_terms( get_the_ID(), $tax );
		$list_count = count( $list_array );

		for ( $i = 0; $i < $list_count; $i++ ) {
			$url = get_term_link( $list_array[$i] );
			$list .= '<a href="' . $url. '">' . $list_array[$i]->name . '</a>';
			if ( $list_count > 1 && $i < $list_count - 2 ) $list .= ", ";
			if ( $list_count > 1 && $i == $list_count - 2 ) $list .= " and ";
		}
		echo $list;
	}

	function OWN_Google_API_key() {
		acf_update_setting( 'google_api_key', 'AIzaSyBF7_KFh38Hn1bhVAr32DLryzeNaOqch80' );
	}
	add_action( 'acf/init', 'OWN_Google_API_key' );


?>
