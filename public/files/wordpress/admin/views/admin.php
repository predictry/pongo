<div class="wrap">

	<?php screen_icon( 'options-general' ); ?>

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php if ( $this->print_error() ) { ?>
	<div id="message" class="error">
		<p><strong><?php echo $this->print_error(); ?></strong></p>
	</div>
	<?php } ?>
	<?php if ( $this->print_message() ) { ?>
	<div id="message" class="updated">
		<p><strong><?php echo $this->print_message(); ?></strong></p>
	</div>
	<?php } ?>
	<?php
	$this->predictry_settings();
	?>

</div>
