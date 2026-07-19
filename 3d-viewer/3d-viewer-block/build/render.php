<?php
if ( !defined( 'ABSPATH' ) ) { exit; }

$bp3dbSrcType		= $attributes['sourceType'] ?? 'upload';
$bp3dbModelUrl		= 'upload' === $bp3dbSrcType ? ( $attributes['model']['url'] ?? '' ) : ( $attributes['modelLink'] ?? '' );
$bp3dbModelTitle		= 'upload' === $bp3dbSrcType ? ( $attributes['model']['title'] ?? '' ) : '';
$bp3dbIsTouchMove	= $attributes['isTouchMove'] ?? true;
$bp3dbIsZoom			= $attributes['isZoom'] ?? true;
$bp3dbWidth			= $attributes['width'] ?? '100%';
$bp3dbHeight			= $attributes['height'] ?? '350px';

$bp3dbWidthStr		= intval( $bp3dbWidth ) ? $bp3dbWidth : 'auto';
$bp3dbHeightStr		= intval( $bp3dbHeight ) ? $bp3dbHeight : '350px';

$bp3dbModelStyles	= sprintf( 'width: %s; height: %s;', esc_attr( $bp3dbWidthStr ), esc_attr( $bp3dbHeightStr ) );
$bp3dbWrapperStyles	= sprintf( 'text-align: %s;', esc_attr( $attributes['alignment'] ?? 'center' ) );

$bp3dbWrapperAttributes = get_block_wrapper_attributes( [
	'style' => $bp3dbWrapperStyles,
] );
?>
<div <?php echo $bp3dbWrapperAttributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
	<div class='tdvb3DViewerBlock'>
		<model-viewer
			src='<?php echo esc_url( $bp3dbModelUrl ); ?>'
			alt='<?php echo esc_attr( $bp3dbModelTitle ); ?>'
			<?php if ( $bp3dbIsTouchMove ) echo 'camera-controls'; ?>
			<?php if ( ! $bp3dbIsZoom ) echo 'disable-zoom'; ?>
			loading='<?php echo esc_attr( $attributes['loadingType'] ?? 'auto' ); ?>'
			auto-rotate
			style='<?php echo esc_attr( $bp3dbModelStyles ); ?>'
		></model-viewer>
	</div>
</div>