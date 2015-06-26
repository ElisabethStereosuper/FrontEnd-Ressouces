<?php if($this->acf_group_id === false): ?>
	<p>
		<label for="<?= $this->get_field_id("acf_group"); ?>"><?php _e("ACF Group:"); ?></label>
		<select class="widefat acf-widget-observer"<?php if($group_id == ""): ?> id="<?= $this->get_field_id("acf_group"); ?>" name="<?= $this->get_field_name("acf_group"); ?>"<?php endif; ?><?php if($group_id != ""): ?> disabled="disabled"<?php endif; ?>>
			<option value=""><?= esc_attr(__("Select ACF Group")); ?></option>
			<?php foreach($acf_groups as $group): ?>
				<?php $acf_group_id     = $group["id"]; ?>
				<?php $acf_group_title  = $group["title"]; ?>
				<option value="<?= $acf_group_id ?>" id="<?= $acf_group_id ?>"<?php if($group_id == $acf_group_id): ?> selected="selected"<?php endif; ?>><?= $acf_group_title ?></option>
			<?php endforeach; ?>
		</select>
		<?php if($group_id != ""): ?>
			<input id="<?= $this->get_field_id("acf_group"); ?>" name="<?= $this->get_field_name("acf_group"); ?>" type="hidden" value="<?= $group_id ?>" />
			<em><?php _e("Not possible to change afterwards (delete and re-add instead)."); ?></em>
		<?php else: ?>
			<em><?php _e("Save the widget in order to continue."); ?></em>
		<?php endif; ?>
	</p>
<?php else: ?>
	<input id="<?= $this->get_field_id("acf_group"); ?>" name="<?= $this->get_field_name("acf_group"); ?>" type="hidden" value="<?= $this->acf_group_id ?>" />
<?php endif; ?>

<?php if($in_widget_title !== false): ?>
	<input id="<?= $this->get_field_id("title"); ?>" name="<?= $this->get_field_name("title"); ?>" type="hidden" value="<?= $in_widget_title ?>" />
<?php endif; ?>

<div class="wrap no_move">

	<?php if(!isset($this->data['no_fields'])): ?>
		<div class="metabox-holder" id="poststuff" style="min-width: 0px !important;">

			<?php $meta_boxes = do_meta_boxes("acf_widget", "widget_" . $group_id, null); ?>
			<script type="text/javascript">
				(function($)
				{
					$("#poststuff .postbox[id*='acf_']").addClass("acf_postbox");

					$(document).ajaxSuccess(function(e, xhr, settings)
					{
						$("#poststuff .postbox[id*='acf_']").addClass("acf_postbox");
						$("#poststuff .postbox[id*='acf_']").addClass("no_box");
					});

					var widget_name = "widget-<?= $this->id_base ?>[<?= $this->number ?>]";

					$("#poststuff input[name*='fields'], #poststuff select[name*='fields'], #poststuff textarea[name*='fields']").each(function()
					{
						if(!$(this).hasClass("changed-name"))
						{
							var old_name = $(this).attr("name");
							var old_name = old_name.replace("fields", "[fields]");
							var new_name = widget_name + old_name;
							$(this).attr("name", new_name);
							$(this).addClass("changed-name");
						}
					});

				})(jQuery);
			</script>

		</div>

	<?php endif; ?>

</div>