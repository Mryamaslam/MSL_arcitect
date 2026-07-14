<?php
/**
 * Single Project — readable detail page.
 *
 * @package Blocksy
 */

get_header();

while (have_posts()) :
	the_post();
	$meta = blocksy_arch_get_project_meta();
	$cats = get_the_terms(get_the_ID(), 'project_category');
	$cat_name = ($cats && ! is_wp_error($cats)) ? $cats[0]->name : '';
	?>

	<main class="arch-site arch-project-single" id="primary">
		<section class="arch-project-hero">
			<div class="arch-project-hero__media">
				<?php
				if (has_post_thumbnail()) {
					the_post_thumbnail('full');
				}
				?>
			</div>
			<div class="arch-project-hero__shade"></div>
			<div class="arch-project-hero__content">
				<a class="arch-project-hero__back" href="<?php echo esc_url(home_url('/#projects')); ?>">← <?php esc_html_e('All Projects', 'blocksy'); ?></a>
				<?php if ($cat_name) : ?>
					<p class="arch-kicker arch-kicker--on-dark"><?php echo esc_html($cat_name); ?></p>
				<?php endif; ?>
				<h1 class="arch-project-hero__title"><?php the_title(); ?></h1>
				<?php if (has_excerpt()) : ?>
					<p class="arch-project-hero__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
				<?php endif; ?>

				<div class="arch-project-meta">
					<?php
					$fields = [
						'location' => __('Location', 'blocksy'),
						'year'     => __('Year', 'blocksy'),
						'client'   => __('Client', 'blocksy'),
						'area'     => __('Area', 'blocksy'),
						'status'   => __('Status', 'blocksy'),
						'role'     => __('Role', 'blocksy'),
					];
					foreach ($fields as $key => $label) :
						if (empty($meta[$key])) {
							continue;
						}
						?>
						<div class="arch-project-meta__item">
							<span><?php echo esc_html($label); ?></span>
							<strong><?php echo esc_html($meta[$key]); ?></strong>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<article class="arch-project-body entry-content">
			<?php the_content(); ?>
		</article>

		<?php if (! empty($meta['gallery'])) : ?>
			<section class="arch-gallery">
				<h2 class="arch-gallery__title"><?php esc_html_e('Project Gallery', 'blocksy'); ?></h2>
				<div class="arch-gallery__grid">
					<?php foreach ($meta['gallery'] as $image_id) : ?>
						<figure class="arch-gallery__item">
							<?php echo wp_get_attachment_image($image_id, 'large'); ?>
						</figure>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<section class="arch-section arch-section--mist">
			<div class="arch-wrap" style="text-align:center;">
				<p class="arch-kicker"><?php esc_html_e('Next step', 'blocksy'); ?></p>
				<h2 class="arch-h2"><?php esc_html_e('Have a similar project in mind?', 'blocksy'); ?></h2>
				<p class="arch-lead" style="max-width:36rem;margin:1rem auto 1.5rem;"><?php esc_html_e('Tell us about your site and goals — we will respond with a clear next step.', 'blocksy'); ?></p>
				<a class="arch-btn arch-btn--solid" href="<?php echo esc_url(home_url('/#contact')); ?>"><?php esc_html_e('Contact us', 'blocksy'); ?></a>
			</div>
		</section>

		<footer class="arch-footer">
			<div class="arch-wrap arch-footer__inner">
				<div>
					<strong class="arch-footer__brand"><?php echo esc_html(get_bloginfo('name')); ?></strong>
					<p><?php esc_html_e('Architecture engineering portfolio.', 'blocksy'); ?></p>
				</div>
				<nav class="arch-footer__nav">
					<a href="<?php echo esc_url(home_url('/#projects')); ?>"><?php esc_html_e('Projects', 'blocksy'); ?></a>
					<a href="<?php echo esc_url(home_url('/#services')); ?>"><?php esc_html_e('Services', 'blocksy'); ?></a>
					<a href="<?php echo esc_url(home_url('/#contact')); ?>"><?php esc_html_e('Contact', 'blocksy'); ?></a>
				</nav>
			</div>
		</footer>
	</main>

	<?php
endwhile;

get_footer();
