<?php
/**
 * Plugin Name:     The Events Calendar Extension: Day View Plain Language URLs
 * Description:     The Events Calendar's Day View already supports the `/events/today/` URL. This extension extends this to add "plain language" URLs like `/events/today/1` and `/events/today/-1`.
 * Version:         1.0.0
 * Extension Class: Tribe__Extension__Day_View_Plain_Lang_URLs
 * Author:          Modern Tribe, Inc.
 * Author URI:      http://m.tri.be/1971
 * License:         GPL version 3 or any later version
 * License URI:     https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:     tribe-ext-day-view-plain-lang-urls
 *
 *     This plugin is free software: you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License as published by
 *     the Free Software Foundation, either version 3 of the License, or
 *     any later version.
 *
 *     This plugin is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *     GNU General Public License for more details.
 */

// Do not load unless Tribe Common is fully loaded and our class does not yet exist.
if (
	class_exists( 'Tribe__Extension' )
	&& ! class_exists( 'Tribe__Extension__Day_View_Plain_Lang_URLs' )
) {
	/**
	 * Extension main class, class begins loading on init() function.
	 */
	class Tribe__Extension__Day_View_Plain_Lang_URLs extends Tribe__Extension {

		/**
		 * Setup the Extension's properties.
		 *
		 * This always executes even if the required plugins are not present.
		 */
		public function construct() {
			$this->add_required_plugin( 'Tribe__Events__Main' );
			// TODO: $this->set_url( 'https://theeventscalendar.com/extensions/example/' );
		}

		/**
		 * Extension initialization and hooks.
		 */
		public function init() {
			$this->setup_plain_language_redirect();
		}

		/**
		 * TODO
		 */
		private function setup_plain_language_redirect() {
			add_filter( 'query_vars', function ( $vars ) {
				return array_merge( $vars, array( 'eventDateModified' ), array( 'eventWeekModified' ), array( 'eventMonthModified' ) );
			}, 10, 1 );

			$plain_language = array(
				__( 'events/tomorrow', 'tribe-ext-day-view-plain-lang-urls' )                => 'index.php?post_type=tribe_events&eventDateModified=1',
				__( 'events/yesterday', 'tribe-ext-day-view-plain-lang-urls' )               => 'index.php?post_type=tribe_events&eventDateModified=-1',
				__( 'events/nextweek', 'tribe-ext-day-view-plain-lang-urls' )                => 'index.php?post_type=tribe_events&eventWeekModified=1&eventDisplay=week',
				__( 'events/lastweek', 'tribe-ext-day-view-plain-lang-urls' )                => 'index.php?post_type=tribe_events&eventWeekModified=-1&eventDisplay=week',
				__( 'events/nextmonth', 'tribe-ext-day-view-plain-lang-urls' )               => 'index.php?post_type=tribe_events&eventMonthModified=1&eventDisplay=month',
				__( 'events/lastmonth', 'tribe-ext-day-view-plain-lang-urls' )               => 'index.php?post_type=tribe_events&eventMonthModified=-1&eventDisplay=month',
				__( 'events/today', 'tribe-ext-day-view-plain-lang-urls' ) . '/(\-?[0-9])/?' => 'index.php?post_type=tribe_events&eventDateModified=$matches[1]',
				__( 'events/week', 'tribe-ext-day-view-plain-lang-urls' ) . '/(\-?[0-9])/?'  => 'index.php?post_type=tribe_events&eventWeekModified=$matches[1]&eventDisplay=week',
				__( 'events/month', 'tribe-ext-day-view-plain-lang-urls' ) . '/(\-?[0-9])/?' => 'index.php?post_type=tribe_events&eventMonthModified=$matches[1]&eventDisplay=month',
			);

			add_filter( 'rewrite_rules_array', function ( $rules ) use ( $plain_language ) {
				$new_rules = array_merge(
					$plain_language,
					$rules
				);

				return $new_rules;
			}, 10, 1 );

			add_filter( 'pre_get_posts', function ( $query ) {
				if ( get_query_var( 'eventDateModified' ) ) {
					$offset = date( 'Y-m-d', time() + ( DAY_IN_SECONDS * get_query_var( 'eventDateModified' ) ) );
					$query->set( 'eventDate', $offset );
				}

				if ( get_query_var( 'eventWeekModified' ) ) {
					$offset = date( 'Y-m-d', time() + ( WEEK_IN_SECONDS * get_query_var( 'eventWeekModified' ) ) );
					$query->set( 'eventDate', $offset );
				}

				if ( get_query_var( 'eventMonthModified' ) ) {
					$offset = date( 'Y-m-d', time() + ( MONTH_IN_SECONDS * get_query_var( 'eventMonthModified' ) ) );
					$query->set( 'eventDate', $offset );
				}

				return $query;
			} );
		}
	} // end class
} // end if class_exists check