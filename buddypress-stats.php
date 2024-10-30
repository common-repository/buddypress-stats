<?php
/*
Plugin Name: BuddyPress Stats
Version: 1.1
Plugin URI: 
Description: Generates some bar graphs based on data from your BuddyPress database.
Author: Jobj&ouml;rn Folkesson
Author URI:
*/


/*  Copyright 2009  Jobj&ouml;rn Folkesson  (email : jobjorn@gmail.com)

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


// Add admin menu (under "BuddyPress")
function buddypress_stats_add_admin_menu() {
	if ( is_site_admin() ) {
		add_submenu_page( "bp-core.php", __("BuddyPress Stats", 'buddypress_stats'), __("BuddyPress Stats", 'buddypress_stats'), 1, "buddypress_stats_wrap", "buddypress_stats_wrap" );
	}
}
add_action( 'admin_menu', 'buddypress_stats_add_admin_menu' );

function buddypress_stats_wrap() {
	?>
	<div class="wrap">
		<div>
			<?php
				if($_GET['subpage'] != "xprofile" && $_GET['subpage'] != "friends" && $_GET['subpage'] != "groups" && $_GET['subpage'] != "activity"){
					echo "<b>";
					_e( 'Main', 'buddypress-stats' );
					echo "</b>, ";
				}
				else{
					echo '<a href="?page=buddypress_stats_wrap">';
					_e( 'Main', 'buddypress-stats' );
					echo "</a>, ";
				}
				
				if($_GET['subpage'] == "xprofile"){
					echo "<b>";
					_e( 'Xprofile Stats', 'buddypress-stats' );
					echo "</b>, ";
				}
				else{
					echo '<a href="?page=buddypress_stats_wrap&amp;subpage=xprofile">';
					_e( 'Xprofile Stats', 'buddypress-stats' );
					echo "</a>, ";
				}
				
				if($_GET['subpage'] == "friends"){
					echo "<b>";
					_e( 'Friends Stats', 'buddypress-stats' );
					echo "</b>, ";
				}
				else{
					echo '<a href="?page=buddypress_stats_wrap&amp;subpage=friends">';
					_e( 'Friends Stats', 'buddypress-stats' );
					echo "</a>, ";
				}
				
				if($_GET['subpage'] == "groups"){
					echo "<b>";
					_e( 'Groups Stats', 'buddypress-stats' );
					echo "</b>, ";
				}
				else{
					echo '<a href="?page=buddypress_stats_wrap&amp;subpage=groups">';
					_e( 'Groups Stats', 'buddypress-stats' );
					echo "</a>, ";
				}
				
				if($_GET['subpage'] == "activity"){
					echo "<b>";
					_e( 'Activity Stats', 'buddypress-stats' );
					echo "</b>";
				}
				else{
					echo '<a href="?page=buddypress_stats_wrap&amp;subpage=activity">';
					_e( 'Activity Stats', 'buddypress-stats' );
					echo "</a>";
				}
			?>
		<div>
		<?php
			if($_GET['subpage'] == "xprofile"){
				buddypress_stats_xprofile();
			}
			elseif($_GET['subpage'] == "friends"){
				buddypress_stats_friends();
			}
			elseif($_GET['subpage'] == "groups"){
				buddypress_stats_groups();
			}
			elseif($_GET['subpage'] == "activity"){
				buddypress_stats_activity();
			}
			else{
				buddypress_stats_main();
			}
		?>
	</div>
	<?php
}

function buddypress_stats_main() {
	?>
	<h2><?php _e( 'BuddyPress Stats', 'buddypress-stats' ); ?></h2>
	<h3><?php _e( 'At a glance', 'buddypress-stats' ); ?></h3>
	<?
		global $wpdb;
		
		$member_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->users};"));
		$group_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->base_prefix}bp_groups;"));
		$activity_count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$wpdb->base_prefix}bp_activity_user_activity;"));
	?>
	<p>
		<?php printf( __( 'You have %1$d members, %2$d groups, and %3$d logged activity posts.', 'buddypress-stats' ), $member_count, $group_count, $activity_count ); ?>
	</p>
	<?php
}

function buddypress_stats_xprofile() {
	?>
	<h2><?php _e( 'Xprofile Stats', 'buddypress-stats' ) ?></h2>
	<?php
	_e('These diagrams are based on Xprofile data, more specifically all fields using "multiselectbox", "selectbox", "checkbox", or "radio" as field type.', 'buddypress-stats');
	$compatible_field_types = array("multiselectbox", "selectbox", "checkbox", "radio");
	$compatible_fields = array();
	$field_types = array();
	
	global $wpdb;
	
	$sql = "SELECT id, type FROM {$wpdb->base_prefix}bp_xprofile_fields WHERE parent_id = 0 ORDER BY id DESC";
	$result = $wpdb->get_results($wpdb->prepare($sql));
	foreach($result as $row){
		if(in_array($row->type, $compatible_field_types)){
			$compatible_fields[] = $row->id;
			$field_types[$row->id] = $row->type;
		}
	}
	foreach($compatible_fields as $field_id){
		$diagram_values = array();
		$sql = "SELECT value FROM {$wpdb->base_prefix}bp_xprofile_data WHERE field_id = {$field_id} ORDER BY id DESC";
		$result = $wpdb->get_col($wpdb->prepare($sql));
		foreach($result as $row){
			if($field_types[$field_id] == "selectbox" || $field_types[$field_id] == "radio"){
				if(strlen($row) > 0){
					$diagram_values[$row]++;
				}
			}
			elseif($field_types[$field_id] == "multiselectbox" || $field_types[$field_id] == "checkbox"){
				$values = unserialize($row);
				if(is_array($values)){
					foreach($values as $value){
						if(strlen($value) > 0){
							$diagram_values[$value]++;
						}
					}
				}
			}
			else{
				echo $field_types[$field_id] . "<br />";
			}
		}
		$sql = "SELECT name FROM {$wpdb->base_prefix}bp_xprofile_fields WHERE id = {$field_id}";
		$name = $wpdb->get_var($wpdb->prepare($sql));
		echo "<h3>" . $name . "</h3>";
		echo buddypress_stats_array_to_diagram($diagram_values);
	}
}
function buddypress_stats_friends() {
	?>
	<h2><?php _e( 'Friends Stats', 'buddypress-stats' ) ?></h2>
	<?php
	_e('This diagram is based on Friends data.', 'buddypress-stats');
	$values = array();
	$diagram_values = array();
	
	global $wpdb;
	
	
	$sql = "SELECT initiator_user_id, friend_user_id FROM {$wpdb->base_prefix}bp_friends WHERE is_confirmed = 1 ORDER BY id DESC";
	$result = $wpdb->get_results($wpdb->prepare($sql));
	foreach($result as $row){
		$values[$row->initiator_user_id]++;
		$values[$row->friend_user_id]++;
	}
	foreach($values as $key => $value){
		$diagram_values[bp_core_get_userlink($key)] = $value;
	}
	
	echo "<h3>";
	_e('Most friends', 'buddypress-stats');
	echo "</h3>";
	echo buddypress_stats_array_to_diagram($diagram_values);
}
function buddypress_stats_groups() {
	?>
	<h2><?php _e( 'Groups Stats', 'buddypress-stats' ) ?></h2>
	<?php
	_e('This diagram is based on Groups data.', 'buddypress-stats');
	$values = array();
	$diagram_values = array();
	
	global $wpdb;
	
	
	$sql = "SELECT group_id FROM {$wpdb->base_prefix}bp_groups_members ORDER BY id DESC";
	$result = $wpdb->get_col($wpdb->prepare($sql));
	foreach($result as $row){
		$values[$row]++;
	}
	foreach($values as $key => $value){
		$sql = "SELECT name FROM {$wpdb->base_prefix}bp_groups WHERE id = {$key}";
		$groupname = $wpdb->get_var($wpdb->prepare($sql));
		if(strlen($groupname) > 0){
			$diagram_values[$groupname] = $value;
		}
	}
	
	echo "<h3>";
	_e('Most group members', 'buddypress-stats');
	echo "</h3>";
	echo buddypress_stats_array_to_diagram($diagram_values);
}
function buddypress_stats_activity() {
	?>
	<h2><?php _e( 'Activity Stats', 'buddypress-stats' ) ?></h2>
	<?php
	_e('These diagrams are based on data from the activity feeds around your site.', 'buddypress-stats');
	
	global $wpdb;
	
	$values = array();
	$diagram_values = array();
	$sql = "SELECT user_id FROM {$wpdb->base_prefix}bp_activity_user_activity ORDER BY id DESC";
	$result = $wpdb->get_col($wpdb->prepare($sql));
	foreach($result as $row){
		$values[$row]++;
	}
	foreach($values as $key => $value){
		$diagram_values[bp_core_get_userlink($key)] = $value;
	}
	echo "<h3>";
	_e('Most actions', 'buddypress-stats');
	echo "</h3>";
	echo buddypress_stats_array_to_diagram($diagram_values);
	
	$diagram_values = array();
	$sql = "SELECT component_action FROM {$wpdb->base_prefix}bp_activity_user_activity ORDER BY id DESC";
	$result = $wpdb->get_col($wpdb->prepare($sql));
	foreach($result as $row){
		$diagram_values[$row]++;
	}
	echo "<h3>";
	_e('Most popular component_action', 'buddypress-stats');
	echo "</h3>";
	echo buddypress_stats_array_to_diagram($diagram_values);
	
	$diagram_values_weeks = array();
	$diagram_values_months = array();
	$sql = "SELECT date_recorded FROM {$wpdb->base_prefix}bp_activity_user_activity ORDER BY id DESC";
	$result = $wpdb->get_col($wpdb->prepare($sql));
	foreach($result as $row){
	//	echo $row . "<br />";
		if(date("W", strtotime($row)) == "01" && date("m", strtotime($row)) == "12"){
			$diagram_values_weeks[(intval(date("Y", strtotime($row))) + 1) . "-01"]++;
		}
		else{
			$diagram_values_weeks[date("Y-W", strtotime($row))]++;
		}
		$diagram_values_months[date("Y-m", strtotime($row))]++;
	}
	echo "<h3>";
	_e('Recorded activities per week', 'buddypress-stats');
	echo "</h3>";
	echo buddypress_stats_array_to_diagram($diagram_values_weeks, "krsort");
	echo "<h3>";
	_e('Recorded activities per month', 'buddypress-stats');
	echo "</h3>";
	echo buddypress_stats_array_to_diagram($diagram_values_months, "krsort");

}

function buddypress_stats_array_to_diagram($array, $order = "arsort"){
	$color_scheme = get_user_option('admin_color', get_current_user_id());
	if($color_scheme == "classic"){
		$color = "#1d507d";
	}
	elseif($color_scheme == "fresh"){
		$color = "#464646";
	}
	else{
		$color = "#808080";
	}
	
	if(!is_array($array)){
		return FALSE;
	}
	if($order == "ksort"){
		ksort($array);
	}
	elseif($order == "krsort"){
		krsort($array);
	}
	elseif($order == "arsort"){
		arsort($array);
	}
	$arrayclone = $array;
	arsort($arrayclone);
	foreach($arrayclone as $val){
		$total = $val;
		break;
	}
	
	$counter = 0;
	foreach($array as $key => $value){
		$counter++;
		if($counter == 1){
			$string .= "<table>";
		}
		
		$string .= "<tr>";
			$string .= "<td style=\"padding-right: 3px;\">";
				$string .= $key;
			$string .= "</td>";
			$string .= "<td style=\"width: 250px; background-color: #aaa; padding-right: 3px;\">\n";
				$string .= "<div style=\"width: " . ($value / $total) * 100 . "%; color: white; background-color: " . $color . "; padding: 3px 0px 3px 3px;\">" . $value . "</div>";
			$string .= "</td>";
		$string .= "</tr>";
	}
	if($counter > 0){
		$string .= "</table>";
	}
	
	return $string;
}

?>