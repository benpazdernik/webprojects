<?php global $post; ?>
<div class="event-content">
<?php if ( apply_filters( 'FHEE__content_espresso_events_details_template__display_entry_meta', TRUE )): ?>
	<div class="entry-meta">
		<span class="tags-links"><?php espresso_event_categories( $post->ID, TRUE, TRUE ); ?></span>
	<?php
		if ( ! post_password_required() && ( comments_open() || get_comments_number() ) ) :
	?>
	<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'event_espresso' ), __( '1 Comment', 'event_espresso' ), __( '% Comments', 'event_espresso' ) ); ?></span>
	<?php
		endif;
		edit_post_link( __( 'Edit', 'event_espresso' ), '<span class="edit-link">', '</span>' );
	?>
	</div>
<?php endif; ?>

	<h3 class="about-event-h3 ee-event-h3">
		<span class="ee-icon ee-icon-event"></span><?php _e( 'Veranstaltungsdetails', 'event_espresso' ); ?>
	</h3>
<?php if ( espresso_event_phone( $post->ID, FALSE ) != '' ) : ?>
	<p>
		<span class="small-text"><strong><?php _e( 'Event Phone:', 'event_espresso' ); ?> </strong></span> <?php espresso_event_phone( $post->ID ); ?>
	</p>
<?php endif; ?>
<?php 
	do_action( 'AHEE_event_details_before_the_content', $post ); 
	if (( is_archive() && has_excerpt( $post->ID )) || apply_filters( 'FHEE__EES_Espresso_Events__process_shortcode__true', FALSE )) {
		the_excerpt();
	} else {
		the_content();
	}
	do_action( 'AHEE_event_details_after_the_content', $post ); 
?>
</div>
<!-- .event-content -->
