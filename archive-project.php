<?php
/**
 * Projects archive template.
 *
 * @package Blocksy
 */

add_filter('body_class', function ($classes) {
	$classes[] = 'arch-portfolio-page';
	return $classes;
});

get_header();
?>

<main class="arch-portfolio" id="primary">
	<section class="arch-section" style="padding-top: clamp(4rem, 8vw, 6rem);">
		<div class="arch-section__head">
			<div>
				<p class="arch-section__eyebrow"><?php esc_html_e('Portfolio', 'blocksy'); ?></p>
				<h1 class="arch-section__title"><?php esc_html_e('All Projects', 'blocksy'); ?></h1>
			</div>
			<p class="arch-section__desc">
				<?php esc_html_e('Browse the full project list. Select any item for complete details and gallery.', 'blocksy'); ?>
			</p>
		</div>

		<?php if (have_posts()) : ?>
			<div class="arch-projects-grid">
				<?php
				while (have_posts()) :
					the_post();
					$meta = blocksy_arch_get_project_meta();
					$cats = get_the_terms(get_the_ID(), 'project_category');
					$cat_name = ($cats && ! is_wp_error($cats)) ? $cats[0]->name : '';
					?>
					<a class="arch-project-card" href="<?php the_permalink(); ?>">
						<div class="arch-project-card__media">
							<?php
							if (has_post_thumbnail()) {
								the_post_thumbnail('large');
							} else {
								echo '<div style="width:100%;height:100%;background:#252a33;"></div>';
							}
							?>
						</div>
						<div class="arch-project-card__overlay"></div>
						<div class="arch-project-card__body">
							<div class="arch-project-card__meta">
								<?php if ($cat_name) : ?><span><?php echo esc_html($cat_name); ?></span><?php endif; ?>
								<?php if (! empty($meta['year'])) : ?><span><?php echo esc_html($meta['year']); ?></span><?php endif; ?>
								<?php if (! empty($meta['location'])) : ?><span><?php echo esc_html($meta['location']); ?></span><?php endif; ?>
							</div>
							<h2 class="arch-project-card__title"><?php the_title(); ?></h2>
							<?php if (has_excerpt()) : ?>
								<p class="arch-project-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
							<?php endif; ?>
							<span class="arch-project-card__cta"><?php esc_html_e('View details', 'blocksy'); ?> →</span>
						</div>
					</a>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<div class="arch-empty"><p><?php esc_html_e('No projects found.', 'blocksy'); ?></p></div>
		<?php endif; ?>
	</section>
</main>

<?php
get_footer();
