import { __ } from '@wordpress/i18n';
import { filterCategories } from './hooks/inserter';
import { whitelistBlocks } from './hooks/whitelist';
import { registerBlockStyles, unregisterBlockStyles } from './hooks/styles';

/**
 * Restricts blocks to the following list.
 */

whitelistBlocks( [
	'pcc/child-pages',
	'pcc/participants-button',
	'pcc/program-button',
	'pcc/recent-content',
	'pcc/projects',
	'pcc/social-links',
	'core/paragraph',
	'core/image',
	'core/heading',
	'core/list',
	'core/quote',
	'core/shortcode',
	'core/button',
	'core/columns',
	'core/column',
	'core/embed',
	'core-embed/twitter',
	'core-embed/youtube',
	'core-embed/facebook',
	'core-embed/instagram',
	'core-embed/soundcloud',
	'core-embed/flickr',
	'core-embed/vimeo',
	'core-embed/meetup-com',
	'core-embed/polldaddy',
	'core-embed/scribd',
	'core-embed/slideshare',
	'core-embed/ted',
	'core/group',
	'core/freeform',
	'core/media-text',
	'core/missing',
	'core/search',
	'core/block',
	'core/subhead',
	'core/text-columns',
] );

/**
 * Flattens block categories to a single "Blocks" category
 */
filterCategories( 'blocks' );

/**
 * Unregister core block default styles.
 */
unregisterBlockStyles( [
	{ block: 'core/button', styles: [ 'outline', 'fill' ] },
	{ block: 'core/image', styles: [ 'default', 'circle-mask' ] },
	{ block: 'core/pullquote', styles: [ 'default', 'solid-color' ] },
	{ block: 'core/table', styles: [ 'regular', 'stripes' ] },
	{ block: 'core/quote', styles: [ 'default', 'large' ] },
] );

/**
 * Register styles onto blocks.
 */
registerBlockStyles( [
	{
		block: 'core/button',
		styles: [
			{ name: 'solid', label: __( 'Solid', 'pcc-framework' ) },
			{ name: 'outline', label: __( 'Outline', 'pcc-framework' ) },
		],
	},
] );
