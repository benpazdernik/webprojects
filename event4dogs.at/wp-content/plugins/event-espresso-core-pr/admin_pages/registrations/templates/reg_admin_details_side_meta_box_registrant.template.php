<div id="admin-side-mbox-primary-registrant-dv" class="admin-side-mbox-dv">
	<p class="clearfix">
		<span class="admin-side-mbox-label-spn lt-grey-txt float-left"><?php _e('Name', 'event_espresso'); ?></span><?php echo $fname . ' ' . $lname;?>
	</p>
	<p class="clearfix">
		<span class="admin-side-mbox-label-spn lt-grey-txt float-left"><?php _e('Email', 'event_espresso'); ?></span><a href="mailto:<?php echo $email;?>"><?php echo $email;?></a>
	</p>
	<p class="clearfix">
		<span class="admin-side-mbox-label-spn lt-grey-txt float-left"><?php _e('Phone #', 'event_espresso'); ?></span><?php echo $phone;?>
	</p>
	<p class="clearfix">
		<span class="admin-side-mbox-label-spn lt-grey-txt float-left"><?php _e('Address', 'event_espresso'); ?></span>
		<div class="admin-side-mbox-text-dv">
			<?php echo $address;?>
			<?php echo $address2;?>
			<?php echo $city;?>
			<?php echo $state . $country;?>
			<?php echo $zip;?>
		</div>
	</p>
</div>


<p style="text-align:right;">
	<?php $att_link = EE_Admin_Page::add_query_args_and_nonce( array( 'action'=>'edit_attendee', 'post'=>$ATT_ID ), REG_ADMIN_URL ); ?>
	<a href="<?php echo $att_link; ?>" title="<?php _e( 'View details for this contact', 'event_espresso' );?>">
		<?php _e('View / Edit this Contact', 'event_espresso'); ?>
	</a>
</p>