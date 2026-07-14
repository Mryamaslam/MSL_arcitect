<?php
/**
 * Architecture Projects custom post type.
 *
 * @package Blocksy
 */

if (! defined('ABSPATH')) {
	exit;
}

add_action('init', 'blocksy_arch_register_project_cpt');

function blocksy_arch_register_project_cpt() {
	$labels = [
		'name'               => __('Projects', 'blocksy'),
		'singular_name'      => __('Project', 'blocksy'),
		'add_new'            => __('Add Project', 'blocksy'),
		'add_new_item'       => __('Add New Project', 'blocksy'),
		'edit_item'          => __('Edit Project', 'blocksy'),
		'new_item'           => __('New Project', 'blocksy'),
		'view_item'          => __('View Project', 'blocksy'),
		'search_items'       => __('Search Projects', 'blocksy'),
		'not_found'          => __('No projects found', 'blocksy'),
		'not_found_in_trash' => __('No projects found in Trash', 'blocksy'),
		'menu_name'          => __('Projects', 'blocksy'),
		'all_items'          => __('All Projects', 'blocksy'),
	];

	register_post_type('project', [
		'labels'              => $labels,
		'public'              => true,
		'has_archive'         => true,
		'rewrite'             => ['slug' => 'projects', 'with_front' => false],
		'menu_icon'           => 'dashicons-building',
		'menu_position'       => 5,
		'supports'            => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
		'show_in_rest'        => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => true,
	]);

	register_taxonomy('project_category', 'project', [
		'labels' => [
			'name'          => __('Project Categories', 'blocksy'),
			'singular_name' => __('Project Category', 'blocksy'),
			'search_items'  => __('Search Categories', 'blocksy'),
			'all_items'     => __('All Categories', 'blocksy'),
			'edit_item'     => __('Edit Category', 'blocksy'),
			'update_item'   => __('Update Category', 'blocksy'),
			'add_new_item'  => __('Add New Category', 'blocksy'),
			'new_item_name' => __('New Category Name', 'blocksy'),
			'menu_name'     => __('Categories', 'blocksy'),
		],
		'hierarchical'      => true,
		'public'            => true,
		'show_ui'           => true,
		'show_admin_column' => true,
		'show_in_rest'      => true,
		'rewrite'           => ['slug' => 'project-category'],
	]);
}

add_action('add_meta_boxes', 'blocksy_arch_project_meta_boxes');
add_action('save_post_project', 'blocksy_arch_save_project_meta');

function blocksy_arch_project_meta_boxes() {
	add_meta_box(
		'blocksy_arch_project_details',
		__('Project Details', 'blocksy'),
		'blocksy_arch_render_project_meta_box',
		'project',
		'normal',
		'high'
	);

	add_meta_box(
		'blocksy_arch_project_gallery',
		__('Project Gallery', 'blocksy'),
		'blocksy_arch_render_gallery_meta_box',
		'project',
		'normal',
		'default'
	);
}

function blocksy_arch_project_meta_fields() {
	return [
		'location' => __('Location', 'blocksy'),
		'year'     => __('Year', 'blocksy'),
		'client'   => __('Client', 'blocksy'),
		'area'     => __('Area / Scale', 'blocksy'),
		'status'   => __('Status', 'blocksy'),
		'role'     => __('Role', 'blocksy'),
	];
}

function blocksy_arch_render_project_meta_box($post) {
	wp_nonce_field('blocksy_arch_project_meta', 'blocksy_arch_project_meta_nonce');

	echo '<table class="form-table"><tbody>';
	foreach (blocksy_arch_project_meta_fields() as $key => $label) {
		$value = get_post_meta($post->ID, '_arch_' . $key, true);
		echo '<tr>';
		echo '<th><label for="arch_' . esc_attr($key) . '">' . esc_html($label) . '</label></th>';
		echo '<td><input type="text" class="widefat" id="arch_' . esc_attr($key) . '" name="arch_' . esc_attr($key) . '" value="' . esc_attr($value) . '" /></td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
}

function blocksy_arch_render_gallery_meta_box($post) {
	$ids = get_post_meta($post->ID, '_arch_gallery', true);
	$ids = is_array($ids) ? $ids : [];
	$ids_csv = implode(',', array_map('absint', $ids));

	echo '<p>' . esc_html__('Select multiple images for the project gallery shown on the detail page.', 'blocksy') . '</p>';
	echo '<input type="hidden" id="arch_gallery" name="arch_gallery" value="' . esc_attr($ids_csv) . '" />';
	echo '<div id="arch-gallery-preview" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;">';

	foreach ($ids as $id) {
		$url = wp_get_attachment_image_url($id, 'thumbnail');
		if ($url) {
			echo '<img src="' . esc_url($url) . '" style="width:80px;height:80px;object-fit:cover;border-radius:4px;" />';
		}
	}

	echo '</div>';
	echo '<button type="button" class="button" id="arch-gallery-select">' . esc_html__('Select Gallery Images', 'blocksy') . '</button> ';
	echo '<button type="button" class="button" id="arch-gallery-clear">' . esc_html__('Clear', 'blocksy') . '</button>';
}

function blocksy_arch_save_project_meta($post_id) {
	if (! isset($_POST['blocksy_arch_project_meta_nonce']) || ! wp_verify_nonce($_POST['blocksy_arch_project_meta_nonce'], 'blocksy_arch_project_meta')) {
		return;
	}

	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
		return;
	}

	if (! current_user_can('edit_post', $post_id)) {
		return;
	}

	foreach (array_keys(blocksy_arch_project_meta_fields()) as $key) {
		if (isset($_POST['arch_' . $key])) {
			update_post_meta($post_id, '_arch_' . $key, sanitize_text_field(wp_unslash($_POST['arch_' . $key])));
		}
	}

	if (isset($_POST['arch_gallery'])) {
		$raw = sanitize_text_field(wp_unslash($_POST['arch_gallery']));
		$ids = array_filter(array_map('absint', explode(',', $raw)));
		update_post_meta($post_id, '_arch_gallery', $ids);
	}
}

add_action('admin_enqueue_scripts', 'blocksy_arch_admin_scripts');

function blocksy_arch_admin_scripts($hook) {
	global $post_type;

	if (! in_array($hook, ['post.php', 'post-new.php'], true) || $post_type !== 'project') {
		return;
	}

	wp_enqueue_media();
	wp_add_inline_script('jquery', <<<'JS'
jQuery(function ($) {
	var frame;
	$('#arch-gallery-select').on('click', function (e) {
		e.preventDefault();
		if (frame) { frame.open(); return; }
		frame = wp.media({
			title: 'Select Gallery Images',
			button: { text: 'Use images' },
			multiple: true
		});
		frame.on('select', function () {
			var selection = frame.state().get('selection');
			var ids = [];
			var html = '';
			selection.each(function (attachment) {
				attachment = attachment.toJSON();
				ids.push(attachment.id);
				var url = (attachment.sizes && attachment.sizes.thumbnail) ? attachment.sizes.thumbnail.url : attachment.url;
				html += '<img src="' + url + '" style="width:80px;height:80px;object-fit:cover;border-radius:4px;" />';
			});
			$('#arch_gallery').val(ids.join(','));
			$('#arch-gallery-preview').html(html);
		});
		frame.open();
	});
	$('#arch-gallery-clear').on('click', function (e) {
		e.preventDefault();
		$('#arch_gallery').val('');
		$('#arch-gallery-preview').empty();
	});
});
JS
	);
}

function blocksy_arch_get_project_meta($post_id = null) {
	$post_id = $post_id ?: get_the_ID();
	$data = [];

	foreach (array_keys(blocksy_arch_project_meta_fields()) as $key) {
		$data[$key] = get_post_meta($post_id, '_arch_' . $key, true);
	}

	$data['gallery'] = get_post_meta($post_id, '_arch_gallery', true);
	if (! is_array($data['gallery'])) {
		$data['gallery'] = [];
	}

	return $data;
}
