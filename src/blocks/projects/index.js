const { __ } = wp.i18n;
const { registerBlockType } = wp.blocks;
const ServerSideRender = wp.serverSideRender;

registerBlockType( 'pcc/projects', {
	title: __( 'Projects', 'pcc-framework' ),
	description: __(
		'Generate a content grid of available projects',
		'pcc-framework'
	),
	icon: 'screenoptions',
	category: 'blocks',
	edit: () => {
		return <ServerSideRender block="pcc/projects" />;
	},
	save: () => {
		return null;
	},
} );
