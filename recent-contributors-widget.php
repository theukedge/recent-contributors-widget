<?php
/*
Plugin Name: Recent Contributors Widget
Plugin URI: http://www.theukedge.com
Description: Displays a list of everyone that has contributed to your site recently (time period can be defined)
Version: 1.0
Author: Dave Clements
Author URI: http://www.davidclements.me
License: GPLv2
*/

/*  Copyright 2013  Dave Clements  (email : http://www.theukedge.com/contact/)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, A  02110-1301  USA
*/

	// Start class recent_contributors_widget //
 
class recent_contributors_widget extends WP_Widget {
 
	// Constructor //
	
	function recent_contributors_widget() {
		load_plugin_textdomain('recent_contributors', false, dirname(plugin_basename(__FILE__)) . '/languages/');
		parent::WP_Widget(false, $name = __('Recent Contributors Widget', 'recent_contributors'), array('description' => __('Displays a list of recent contributors to your site', 'recent_contributors')) );	
	}

	// Extract Args //

	function widget($args, $instance) {
		extract( $args );
		$title = apply_filters('widget_title', $instance['title']); // the widget title
		$timestring = $instance['timestring']; // How recent to pull contributors from
		$linkdestination = $instance['linkdestination']; // Where to link author name to
		$postcount = $instance['postcount']; // Whether to show number of posts by each author

	// Before widget //
		
		echo $before_widget;
		
	// Title of widget //
		
		if ( $title ) { echo $before_title . $title . $after_title; }
		
	// Widget output //
		
		// get all contributors and their display names
		$allauthors = get_users();
		$i = 0;
		foreach( $allauthors as $author ) {
			$authorlist[$i]['id'] = $author->data->ID;
			$authorlist[$i]['name'] = $author->data->display_name;
			$i++;
		} ?>

		<ul>
		<?php
		// check whether a user has contributed in the time specified
		foreach( $authorlist as $author ) {
			$args = array(
					'author' => $author['id'],
					'date_query' => array(
						'after' => $timestring
					)
				 );
			$query = new WP_Query( $args );

			if( $query->have_posts() ) { ?>
					<li>
						<?php if( $linkdestination == 'posts_list' ) {
							echo '<a href="'. get_author_posts_url( $author['id'] ) .'">' . $author['name'] . '</a>';
						} elseif( $linkdestination == 'website' ) { 
							echo '<a href="'. get_the_author_meta( 'user_url', $author['id'] ) .'">' . $author['name'] . '</a>';
						} else {
							echo $author['name'];
						}
						if( $postcount == 1 ) { ?>
						 (<?php echo $query->post_count; ?>)
						<?php } ?>
					</li>
				<?php wp_reset_postdata();
			}
		} ?>
		</ul>
		<?php
				
	// After widget //
		
		echo $after_widget;
	}
		
	// Update Settings //
 
	function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = strip_tags($new_instance['title']);
			$instance['timestring'] = strip_tags($new_instance['timestring']);
			$instance['linkdestination'] = strip_tags($new_instance['linkdestination']);
			$instance['postcount'] = strip_tags($new_instance['postcount']);
		return $instance;
	}
 
	// Widget Control Panel //
	
	function form($instance) {

		$defaults = array( 'title' => 'Recent Contributors', 'timestring' => '30 days ago', 'linkdestination' => 'none', 'postcount' => 1 );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', 'recent_contributors'); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('timestring'); ?>"><?php _e('Since when', 'recent_contributors'); ?>:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('timestring'); ?>" name="<?php echo $this->get_field_name('timestring'); ?>'" type="text" value="<?php echo $instance['timestring']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('linkdestination'); ?>"><?php _e('Link destination', 'recent_contributors'); ?>:</label>
			<select id="<?php echo $this->get_field_id('linkdestination'); ?>" name="<?php echo $this->get_field_name('linkdestination'); ?>">
				<option value="none" <?php selected( 'none', $instance['linkdestination'] ); ?>><?php _e( 'No link', 'recent_contributors' ); ?></option>
				<option value="posts_list" <?php selected( 'posts_list', $instance['linkdestination'] ); ?>><?php _e( 'Posts list', 'recent_contributors' ); ?></option>
				<option value="website" <?php selected( 'website', $instance['linkdestination'] ); ?>><?php _e( 'Author\'s website' , 'recent_contributors' ); ?></option>
			</select>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('postcount'); ?>" name="<?php echo $this->get_field_name('postcount'); ?>" type="checkbox" value="1" <?php checked( '1', $instance['postcount'] ); ?>/>
			<label for="<?php echo $this->get_field_id('postcount'); ?>"><?php _e('Display Post Count?'); ?></label>
        </p>

	<?php }
 
}

// End class recent_contributors_widget

add_action('widgets_init', create_function('', 'return register_widget("recent_contributors_widget");'));