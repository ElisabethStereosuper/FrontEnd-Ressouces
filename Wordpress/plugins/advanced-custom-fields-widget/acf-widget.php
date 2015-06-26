<?php
/*
Plugin Name: Advanced Custom Fields: Widget
Plugin URI: https://www.directbasing.com/
Description: A widget that is able to use content from an ACF field group.
Version: 1.0
Author: Alex van der Vegt (Direct Basing)
Author URI: https://www.directbasing.com/
License: GPL
Copyright: Alex van der Vegt
*/

$GLOBALS["acf_widget_types"] = array("acf_widget");

class ACF_Widget extends WP_Widget
{
	public $available_acfs  = array();
	public $metabox_ids     = array();
	public $acf_group_id 	= false;
	public $title_field_key = false;

	function __construct()
	{
		$widget_options = array(
			"classname"     => "acf_widget", 
			"description"   => "Easily create custom widgets using ACF"
			);

		parent::WP_Widget("ACF_Widget", "ACF Widget", $widget_options);
	}

	function form($instance)
	{
		// Set metabox ids and search for available ACF groups
		$this->set_metabox_ids();
		$this->set_available_acfs();

		// Variables
		$group_id   		= ($this->acf_group_id === false) ? esc_attr($instance["acf_group"]) : $this->acf_group_id;
		$acf_groups 		= $this->available_acfs;
		$widget_id_base 	= $this->id_base; 
		$widget_id 			= $this->number;
		$in_widget_title 	= ($this->title_field_key !== false) ? get_field($this->title_field_key, "widget_" . $widget_id_base . "_" . $widget_id) : false;

		// Set group metaboxes
		$this->set_metaboxes($widget_id_base, $widget_id);

		require("acf-widget-html.php");
	}

	function update($new_instance, $old_instance) 
	{
        // Variables
		$instance               = $old_instance;
		$instance["acf_group"]  = strip_tags($new_instance["acf_group"]);
		$instance["fields"]     = array();

		if(isset($new_instance["fields"]))
		{
			foreach($new_instance["fields"] as $key => $value )
			{
				$instance["fields"][$key] = $value;
				update_field($key, $value, "widget_" . $this->id_base . "_" . $this->number);
			}
		}

		return $instance;
	}

	function widget($args, $instance)
	{
		$title = apply_filters( 'widget_title', "hallo", $instance, $this->id_base );


		// Variables
		$acf_key = "widget_" . $this->id_base . "_" . $this->number;

		// Debug information
		echo "<h3>ACF Key:</h3>";
		echo $acf_key . "<br />";

		echo "<br />";

		echo "<h3>Fields:</h3>";
		echo "<pre>";
		print_r($instance["fields"]);
		echo "</pre>";

		echo "<br />";

		echo "Use get_field(\"fieldname\", \$acf_key); to get the fields in the widget function.";

		echo "<br />";
		echo "<br />";
	}

	function set_metaboxes($widget_id_base, $widget_id)
	{
        // Get ACF field groups
		$acfs = apply_filters("acf/get_field_groups", array());

		if($acfs)
		{
			if(empty($this->metabox_ids))
			{
				$this->data["no_fields"] = true;

				return false;   
			}

			foreach($acfs as $acf)
			{
                // Get ACF options
				$acf["options"] = apply_filters("acf/field_group/get_options", array(), $acf["id"]);

                // Need to show this ACF field group?
				$show = in_array($acf["id"], $this->metabox_ids) ? 1 : 0;

				if(!$show)
				{
					continue;
				}

                // Add meta box
				add_meta_box(
					"acf_" . $acf["id"], 
					$acf["title"], 
					array($this, "meta_box_input"), 
					"acf_widget",
					"widget_" . $acf["id"],
					"high",
					array("field_group" => $acf, "show" => $show, "post_id" => "widget_" . $widget_id_base . "_" . $widget_id)
					);
			}
		}
	}

	function meta_box_input($post, $args)
	{
        // Additional variables
		$options = $args["args"];

		echo "<div class=\"options\" data-layout=\"" . $options["field_group"]["options"]["layout"] . "\" data-show=\"" . $options["show"] . "\" style=\"display: none\"></div>";

		$fields = apply_filters("acf/field_group/get_fields", array(), $options["field_group"]["id"]);

		do_action("acf/create_fields", $fields, $options["post_id"]);

	}

	function set_available_acfs()
	{
        // Get ACF field groups
		$acfs = apply_filters("acf/get_field_groups", array());

		if($acfs)
		{
			foreach($acfs as $acf)
			{
                // Get ACF options
				$acf["options"] = apply_filters("acf/field_group/get_options", array(), $acf["id"]);

                // Need to show this ACF field group?
				$show = in_array($acf["id"], $this->metabox_ids) ? 1 : 0;

				if(!$show)
				{
					continue;
				}

				if(!isset($this->available_acfs[$acf["id"]]))
				{
					$this->available_acfs[$acf["id"]] = array(
						"id"    => $acf["id"],
						"title" => $acf["title"]
						);
				}
			}
		}
	}

	function set_metabox_ids($metabox_id = false)
	{
        // Get ACF field groups
		$acfs = apply_filters("acf/get_field_groups", array());

		if($acfs)
		{
            // Variables
			$metabox_ids = array();

			foreach($acfs as $acf)
			{
                // Get ACF location
				$locations = apply_filters("acf/field_group/get_location", array(), $acf["id"]);

				foreach($locations as $location)
				{
					if($location[0]["param"] == "widget")
					{
						$metabox_ids[] = $acf["id"];
					}
				}
			}

			$this->metabox_ids = $metabox_ids;
		}
	}
}

function acf_widget_init()
{
	//
	// TODO
	// Use for future update so default ACF Widget can access data immediately 
	// and not after saving first (AJAX)
	//

	//wp_register_script("acf-widget", plugins_url("acf-widget.js", __FILE__ ));
	//wp_enqueue_script("acf-widget");

	register_widget("ACF_Widget");
}

add_action("widgets_init", "acf_widget_init");

function observe_deleted_widgets()
{
	global $wp_registered_widgets;

	if(strtolower($_SERVER["REQUEST_METHOD"]) == "post")
	{
		// Get widget ids
		$widget_raw_id 	= $_POST["widget-id"];
		$widget 		= explode("-", $widget_raw_id);
		$widget_id_base = $widget[0];
		$widget_id 		= $widget[1];

		if(isset($_POST["delete_widget"]) AND in_array($widget_id_base, $GLOBALS["acf_widget_types"]))
		{
			if((int)$_POST["delete_widget"] === 1)
			{
				// Get widget by raw id
				$option_name 	= $wp_registered_widgets[$widget_raw_id]['callback'][0]->option_name;
				$key 			= $wp_registered_widgets[$widget_raw_id]['params'][0]['number'];
				$widget_data 	= get_option($option_name);
				$output 		= (object) $widget_data[$key];

				// Empty ACF fields
				if(isset($output["fields"]))
				{
					foreach($output["fields"] as $key => $value )
					{
						$output["fields"][$key] = $value;
						update_field($key, "", "widget_" . $widget_id_base . "_" . $widget_id);
					}
				}
			}
		}
	}
}

add_action("sidebar_admin_setup", "observe_deleted_widgets");

class ACF_Widget_Plugin
{
	public $metabox_ids = array();

	function __construct()
	{
        // Hook ACF JS and CSS to widget page
		add_action("sidebar_admin_setup", array($this, "admin_load"));
	}

	function admin_load()
	{
		add_action("admin_enqueue_scripts", array($this, "admin_enqueue_scripts"));
		add_action("admin_head", array($this, "admin_head"));
		add_action("admin_footer", array($this, "admin_footer"));
	}

	function admin_enqueue_scripts()
	{
		do_action("acf/input/admin_enqueue_scripts");
	}

	function admin_head()
	{   
		if(isset($_POST["acf_nonce"]) && wp_verify_nonce($_POST["acf_nonce"], "input"))
		{
			do_action("acf/save_post", "options");

			$this->data["admin_message"] = __("Widget Updated", "acf");
		}

        // Styles
		echo "<style type=\"text/css\">#side-sortables.empty-container { border: 0 none; }</style>";

        // Add JS and CSS
		do_action("acf/input/admin_head");
	}

	function admin_footer()
	{
        // Add toggle open / close postbox
		?>
		<script type="text/javascript">
			(function($)
			{
				$(".postbox .handlediv").live("click", function()
				{
					var postbox = $(this).closest(".postbox");

					if(postbox.hasClass("closed"))
					{
						postbox.removeClass("closed");
					}
					else
					{
						postbox.addClass("closed");
					}
				});
			})(jQuery);
		</script>
		<?php
	}
}

$GLOBALS["ACF_Widget_Plugin"] = new ACF_Widget_Plugin();

class ACF_Widget_Plugin_Init
{
	public $title   = "Widget";
	public $slug    = "widget";

	function __construct()
	{
        // Add widget type to ACF
		add_filter("acf/location/rule_types", array($this, "acf_location_rules_types"));
		add_filter("acf/location/rule_values/widget", array($this, "acf_location_rules_values_widget"));
	}

	function acf_location_rules_types($choices)
	{
		$choices[$this->title][$this->slug] = $this->title;

		return $choices;
	}

	function acf_location_rules_values_widget($choices)
	{
		$choices = array();

		$choices[$this->slug] = $this->title;

		return $choices;
	}
}

$GLOBALS["ACF_Widget_Plugin_Init"] = new ACF_Widget_Plugin_Init();

?>