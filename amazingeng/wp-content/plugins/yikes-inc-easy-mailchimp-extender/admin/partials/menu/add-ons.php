<?php
/**
 *	Main page to list our current add-ons
 *	@since 6.0.0
 */
?>
<div class="wrap">

	<!-- Freddie Logo -->
	<img src="<?php echo esc_url( YIKES_MC_URL . 'includes/images/MailChimp_Assets/Freddie_60px.png' ); ?>" alt="<?php esc_attr_e( 'Freddie - MailChimp Mascot' , 'yikes-inc-easy-mailchimp-extender' ); ?>" class="yikes-mc-freddie-logo" />

	<h1>YIKES Easy Forms for MailChimp | <?php echo esc_attr__( 'Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?> &nbsp; <a href="https://yikesplugins.com/plugins/?plugins=MailChimp" target="_blank" class="button-primary coming-soon-button" title="<?php esc_attr_e( 'View All Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?>"><?php esc_attr_e( 'View All Add-Ons' , 'yikes-inc-easy-mailchimp-extender' ); ?> <span class="dashicons dashicons-external"></span></a></h1>

	<!-- Addons Page Description -->
	<p class="yikes-easy-mc-about-text about-text"><?php esc_attr_e( "Below you'll find a list of add-ons available for YIKES Easy Forms for MailChimp. Each add-on extends the base functionality of the free plugin." , 'yikes-inc-easy-mailchimp-extender' ); ?></p>

	<!-- Add-On Container -->
	<section id="add-ons">

		<?php
		$add_ons = glob( YIKES_MC_PATH . 'includes/add_ons/*.php' );
		if ( $add_ons && ! empty( $add_ons ) ) {
			ob_start();
			foreach ( $add_ons as $add_on_path ) {
				include_once( $add_on_path );
			}
			$add_on_content = ob_get_contents();
			ob_get_clean();
		}
		echo wp_kses_post( $add_on_content );
		?>

	</section>

</div>