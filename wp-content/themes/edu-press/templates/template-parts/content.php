<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 */
$theme_options_data = get_theme_mods();
$column            = ! empty( $theme_options_data['thim_archive_columns_grid'] ) ? $theme_options_data['thim_archive_columns_grid'] : '2';

if ( isset( $_GET['column'] ) ) {
	$column = $_GET['column'];
}
$class = 'column-' . $column . ' col-md-' . ( 12 / $column );


if ( has_post_format( 'link' ) && thim_meta( 'thim_link_url' ) && thim_meta( 'thim_link_text' ) ) {
	$url   = thim_meta( 'thim_link_url' );
	$title = thim_meta( 'thim_link_text' );
} else {
	$url   = get_permalink();
	$title = get_the_title();
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>
	<div class="content-inner">
		<?php
			do_action( 'thim_entry_top', 'gallery_thumbnails' );
		?>
		<div class="entry-content">
			<header class="entry-header">
				<?php
				echo sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">%s</a></h2>', esc_url( $url ), esc_attr( $title ) );
				?>
			</header>
			<div class="entry-summary">
				<?php
				the_excerpt();
				// thim_entry_meta_date();
				// thim_entry_meta_author();
				thim_entry_meta();
				?>
			</div>
		</div>
	</div>
</article><!-- #post-## -->
