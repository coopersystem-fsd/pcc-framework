<?php

namespace PCCFramework\Blocks\RecentContent;

/**
 * Register the Recent Content block.
 *
 * @return null
 */
function register_block()
{
    register_block_type(
        'pcc/recent-content',
        [
            'editor_script' => 'platform-coop-blocks-js',
            'render_callback' => '\\PCCFramework\\Blocks\\RecentContent\\render_callback',
            'attributes' => [
                'className' => [ 'type' => 'string' ],
            ]
        ]
    );
}

/**
 * Render callback for Recent Content block.
 *
 * @param array $attributes The block attributes.
 *
 * @return string
 */
function render_callback($attributes)
{
    $class =
        (isset($attributes['className']) && ! empty($attributes['className'])) ?
        'recent-content cards cards--four-columns' . $attributes['className'] :
        'recent-content cards cards--four-columns';

    $recent = new \WP_Query([
        'post_type' => 'post',
        'showposts' => 6,
        'orderby' => 'date',
        'order'   => 'DESC',
    ]);

    $output = '';
    ob_start();
    if ($recent->have_posts()) {
        $i = 0; ?>
    <ul class="<?php echo $class; ?>">
        <?php while ($recent->have_posts()) {
            $i++;
            $recent->the_post(); ?>
        <li class="card <?= $recent->post->post_type ?> card--<?= $i ?>">
            <?php if ($i < 4 && has_post_thumbnail($recent->post)) { ?>
            <figure class="card__image">
                <?php
                if ($i === 1) {
                    echo get_the_post_thumbnail($recent->post, 'medium');
                } else {
                    echo get_the_post_thumbnail($recent->post, 'post-thumbnail');
                } ?>
            </figure>
            <?php } ?>
            <div class="card__details">
                <header class="text">
                    <span class="type"><?php
                    switch ($recent->post->post_type) {
                        case 'post':
                            _e('Blog', 'pcc-framework');
                            break;
                        default:
                            _e('Blog', 'pcc-framework');
                            break;
                    } ?></span>
                    <h4 class="title">
                        <a href="<?php echo get_the_permalink($recent->post) ?>"><?php echo get_the_title(); ?></a>
                    </h4>
                </header>
                <time class="published" datetime="<?php echo get_post_time('c'); ?>">
                    <?php echo get_post_time('M j, Y'); ?>
                </time>
            </div>
        </li>
        <?php }
        wp_reset_postdata();
        $newsletter_link = (function_exists('\PCCFramework\Utils\get_config_option'))
        ? \PCCFramework\Utils\get_config_option(
            'signup_link',
            'https://mailchi.mp/platform/coop'
        )
        : 'https://mailchi.mp/platform/coop';
        ?>
        <li class="card card--7">
            <div class="card__details">
                <header class="text">
                    <h4 class="title"><?= __('Never miss an update!', 'pcc-framework'); ?></h4>
                </header>
                <p>
                    <?= __('Our monthly newsletters keep you updated on news about the community.', 'pcc-framework'); ?>
                </p>
                <p class="wp-block-button is-style-secondary">
                    <a class="wp-block-button__link" href="<?= $newsletter_link ?>">
                        <?= __('Sign Up', 'pcc-framework'); ?>
                    </a>
                </p>
        </li>
        </ul>
    <?php }
    $output .= ob_get_clean();
    return $output;
}
