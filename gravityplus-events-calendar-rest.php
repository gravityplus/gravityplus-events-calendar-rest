<?php

/**
 * @wordpress-plugin
 * Plugin Name: The Events Calendar + WP REST API
 * Plugin URI: https://gravityplus.pro
 * Description: Enable the WP REST API for The Events Calendar
 * Version: 1.0.0.dev1
 * Author: gravity+
 * Author URI: https://gravityplus.pro
 *
 * @package   GFP_Events_Calendar_REST
 * @version   1.0.0
 * @author    gravity+ <support@gravityplus.pro>
 * @link      https://gravityplus.pro
 * @copyright 2017 gravity+
 *
 * last updated: January 2017
 */
class GFP_Events_Calendar_REST {

	public $post_types = array(
		'tribe_events',
		'tribe_venue',
		'tribe_organizer',
		'tribe_rsvp_tickets',
		'tribe_rsvp_attendees',
		'tribe-ea-record'
	);

	public $taxonomies = array( 'tribe_events_cat' );

	public $metas = array(
		'_EventAllDay',
		'_EventStartDate',
		'_EventEndDate',
		'_EventStartDateUTC',
		'_EventEndDateUTC',
		'_EventDuration',
		'_EventVenueID',
		'_EventShowMapLink',
		'_EventShowMap',
		'_EventCurrencySymbol',
		'_EventCurrencyPosition',
		'_EventCost',
		'_EventCostMin',
		'_EventCostMax',
		'_EventURL',
		'_EventOrganizerID',
		'_EventPhone',
		'_EventHideFromUpcoming',
		'_EventTimezone',
		'_EventTimezoneAbbr',
		'_EventOrigin',
		'_VenueVenue',
		'_VenueCountry',
		'_VenueAddress',
		'_VenueCity',
		'_VenueStateProvince',
		'_VenueState',
		'_VenueProvince',
		'_VenueZip',
		'_VenuePhone',
		'_VenueURL',
		'_VenueShowMap',
		'_VenueShowMapLink',
		'_VenueOrigin',
		'_OrganizerOrganizer',
		'_OrganizerEmail',
		'_OrganizerWebsite',
		'_OrganizerPhone',
		'_OrganizerOrigin'
	);

	private $registering_meta = false;


	public function __construct() {

		add_action( 'init', array( $this, 'init' ), 25 );

		add_filter( 'register_meta_args', array( $this, 'register_meta_args' ), 10, 4 );
		
		add_filter( 'is_protected_meta', array($this,'is_protected_meta' ), 10, 2 );

	}

	public function init() {

		$this->add_cpt_rest_support();

		$this->add_custom_taxonomy_rest_support();
		
		$this->register_meta();

	}

	/**
	 * Add REST API support to an already registered post type.
	 */
	function add_cpt_rest_support() {

		global $wp_post_types;

		foreach( $this->post_types as $post_type_name ) {

			if ( isset( $wp_post_types[ $post_type_name ] ) ) {

				$wp_post_types[ $post_type_name ]->show_in_rest          = true;
				$wp_post_types[ $post_type_name ]->rest_base             = $post_type_name;
				$wp_post_types[ $post_type_name ]->rest_controller_class = 'WP_REST_Posts_Controller';

			}

		}

	}

	/**
	 * Add REST API support to an already registered taxonomy.
	 */
	function add_custom_taxonomy_rest_support() {

		global $wp_taxonomies;

		foreach( $this->taxonomies as $taxonomy_name ) {

			if ( isset( $wp_taxonomies[ $taxonomy_name ] ) ) {

				$wp_taxonomies[ $taxonomy_name ]->show_in_rest          = true;
				$wp_taxonomies[ $taxonomy_name ]->rest_base             = $taxonomy_name;
				$wp_taxonomies[ $taxonomy_name ]->rest_controller_class = 'WP_REST_Terms_Controller';

			}

		}

	}
	
	public function register_meta() {
		
		foreach( $this->metas as $meta_key_name ) {

			register_meta( 'post', $meta_key_name, array( 'show_in_rest' => true, 'single' => true ) );
			
		}
	}

	public function register_meta_args( $args, $defaults, $object_type, $meta_key ) {

		//if ( in_array( $meta_key, $this->metas )

		$this->registering_meta = true;


		return $args;
	}

	/**
	 *  THe Events Calendar - Remove Protected Custom Meta for REST API
	 */
	public function is_protected_meta( $protected, $meta_key ) {

		if ( in_array( $meta_key, $this->metas ) && ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || $this->registering_meta ) ) {

			$protected = false;

		}

		$this->registering_meta = false;

		return $protected;

	}

}

new GFP_Events_Calendar_REST();