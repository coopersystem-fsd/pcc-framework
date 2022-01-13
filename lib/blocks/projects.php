<?php

namespace PCCFramework\Blocks\Projects;
use App\Controllers\Projects;

/**
 * Register the Recent Content block.
 *
 * @return null
 */
function register_block()
{
    register_block_type(
        'pcc/projects',
        [
            'editor_script' => 'platform-coop-blocks-js',
            'render_callback' => '\\PCCFramework\\Blocks\\Projects\\render_callback',
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
    $output = '';
    ob_start();

    if(!empty(Projects::projects())) { ?>
        <div class="projects-container">
            <div class="wp-block-columns has-2-columns">
                <div class="wp-block-column">
                <h2>Projects</h2>
                <p>The institute orchestrates various research projects around the world.</p>
                </div>
                <div class="wp-block-column">
                <ul class="cards projects cards--two-columns">
                    <?php foreach(Projects::projects() as $project) { ?>
                    <article class="card post format-standard status-publish has-post-thumbnail">
                        <div class="project__details">
                        <header class="text">
                            <h2 class="title">
                            <a href="{{ $project['page_link_id'] }}">{{ $project['title'] }}</a>
                            </h2>
                            <p class="desc">{{ $project['content'] }}</p>
                        </header>
                        </div>
                        <figure>{{ wp_get_attachment_image($project['image'], 'medium') }}</figure>
                    </article>
                    <?php } ?>
                </ul>
                </div>
            </div>
        </div>
    <?php }

    $output .= ob_get_clean();
    return $output;
}