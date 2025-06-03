<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap slicewp-wrap slicewp-wrap-add-commission">

	<form action="" method="POST">

		<!-- Page Heading -->
		<h1 class="wp-heading-inline"><?php echo __( 'Add a New Commission', 'slicewp' ); ?></h1>
		<hr class="wp-header-end" />

		<div id="slicewp-content-wrapper">

			<!-- Primary Content -->
			<div id="slicewp-primary">

				<!-- Postbox -->
				<div class="slicewp-card slicewp-first">

					<div class="slicewp-card-header">
						<span class="slicewp-card-title"><?php echo __( 'Commission Details', 'slicewp' ); ?></span>
					</div>

					<!-- Form Fields -->
					<div class="slicewp-card-inner">

						<!-- Commission Affiliate ID -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">
							
							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-affiliate-id"><?php echo __( 'Affiliate', 'slicewp' ); ?> *</label>
							</div>

							<?php slicewp_output_select2_user_search( array( 'id' => 'slicewp-commission-affiliate-id', 'name' => 'affiliate_id', 'placeholder' => __( 'Select affiliate...', 'slicewp' ), 'user_type' => 'affiliate', 'value' => ( ! empty( $_POST['affiliate_id'] ) ? absint( $_POST['affiliate_id'] ) : '' ) ) ); ?>

						</div>

						<!-- Commission Amount -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-amount"><?php echo __( 'Amount', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( __( "The amount rewarded to the affiliate.", 'slicewp' ) ); ?>
							</div>

							<div class="slicewp-field-currency-amount">
								<div class="slicewp-field-currency-symbol"><?php echo slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) ); ?></div>
								<input id="slicewp-commission-amount" name="amount" type="number" step="any" min="0" value="<?php echo ( ! empty( $_POST['amount'] ) ? esc_attr( $_POST['amount'] ) : '' ); ?>" />
							</div>

						</div>

						<!-- Commission Reference -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-reference"><?php echo __( 'Reference', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( '<p>' . __( "This is the ID of the external reference that led to this commission.", 'slicewp' ) . '</p><p>' . __( "Usually the commission's reference is the ID of the referred order from the eCommerce plugin you are using.", 'slicewp' ) . '</p>' ); ?>
							</div>
							
							<input id="slicewp-commission-reference" name="reference" type="text" value="<?php echo ( ! empty( $_POST['reference'] ) ? esc_attr( $_POST['reference'] ) : '' ); ?>" />

						</div>

						<!-- Commission Reference Amount -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-tooltip-wide">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-reference-amount"><?php echo __( 'Reference Amount', 'slicewp' ); ?></label>
								<?php echo slicewp_output_tooltip( '<p>' . __( "The total amount of the external reference.", 'slicewp' ) . '</p><p>' . __( "Usually, this is the order total of the order associated with this commission.", 'slicewp' ) . '</p>' ); ?>
							</div>

							<div class="slicewp-field-currency-amount">
								<div class="slicewp-field-currency-symbol"><?php echo slicewp_get_currency_symbol( slicewp_get_setting( 'active_currency', 'USD' ) ); ?></div>
								<input id="slicewp-commission-reference-amount" name="reference_amount" type="number" step="any" min="0" value="<?php echo ( ! empty( $_POST['reference_amount'] ) ? esc_attr( $_POST['reference_amount'] ) : '' ); ?>" />
							</div>

						</div>

						<!-- Commission Origin -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-origin"><?php echo __( 'Origin', 'slicewp' ); ?></label>
							</div>
							
							<select id="slicewp-commission-origin" name="origin" class="slicewp-select2">

								<?php 
									foreach( slicewp()->integrations as $integration_slug => $integration ) {
										echo '<option value="' . esc_attr( $integration_slug ) . '">' . esc_html( $integration->get( 'name' ) ) . '</option>';
									} 
								?>

							</select>

						</div>

						<!-- Commission Date -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-date-created"><?php echo __( 'Date', 'slicewp' ); ?> *</label>
							</div>
							
							<input id="slicewp-commission-date-created" name="date_created" required type="text" class="slicewp-dtpicker" autocomplete="off" value="<?php echo ( ! empty( $_POST['date_created'] ) ? esc_attr( $_POST['date_created'] ) : '' ); ?>" />

						</div>

						<!-- Commission Type -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-type"><?php echo __( 'Type', 'slicewp' ); ?></label>
							</div>

							<select id="slicewp-commission-type" name="type" class="slicewp-select2">

								<?php 
									foreach ( slicewp_get_commission_types() as $type_slug => $type_data ) {
										echo '<option value="' . esc_attr( $type_slug ) . '">' . esc_html( $type_data['label'] ) . '</option>';
									}
								?>

							</select>

						</div>

						<!-- Commission Status -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-status"><?php echo __( 'Status', 'slicewp' ); ?> *</label>
							</div>
							
							<select id="slicewp-commission-status" name="status" class="slicewp-select2">

								<?php 
									foreach ( slicewp_get_commission_available_statuses() as $status_slug => $status_name ) {
										echo '<option value="' . esc_attr( $status_slug ) . '">' . esc_html( $status_name ) . '</option>';
									}
								?>

							</select>

						</div>

						<!-- Rejection Reason -->
						<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last" style="display: none;">

							<div class="slicewp-field-label-wrapper">
								<label for="slicewp-commission-rejection-reason"><?php echo __( 'Rejection Reason', 'slicewp' ); ?></label>
							</div>
							
							<textarea id="slicewp-commission-rejection-reason" name="rejection_reason"><?php echo ( ! empty( $_POST['rejection_reason'] ) ? esc_textarea( $_POST['rejection_reason'] ) : '' ); ?></textarea>

						</div>

						<!-- Send rejection email notification -->
						<?php $notification_settings = slicewp_get_email_notification_settings( 'affiliate_commission_rejected' ); ?>

						<?php if ( ! empty( $notification_settings['enabled'] ) ): ?>

							<div class="slicewp-field-wrapper slicewp-field-wrapper-inline slicewp-last" style="display: none;">

								<div class="slicewp-field-label-wrapper">
									<label for="slicewp-send-rejection-email-notification"><?php echo __( 'Send Email Notification', 'slicewp' ); ?></label>
								</div>

								<div class="slicewp-switch">
									<input id="slicewp-send-rejection-email-notification" class="slicewp-toggle slicewp-toggle-round" name="send_rejection_email_notification" type="checkbox" value="1" checked />
									<label for="slicewp-send-rejection-email-notification"></label>
								</div>

								<label for="slicewp-send-rejection-email-notification"><?php echo __( 'Send commission rejected email notification to the affiliate', 'slicewp' ); ?></label>

							</div>

						<?php endif; ?>
					
					</div>

				</div>

				<?php 

					/**
					 * Hook to add extra cards if needed
					 *
					 */
					do_action( 'slicewp_view_commissions_add_commission_bottom' );

				?>

			</div>

			<!-- Sidebar Content -->
			<div id="slicewp-secondary">

				<?php 

					/**
					 * Hook to add extra cards if needed in the sidebar
					 *
					 */
					do_action( 'slicewp_view_commissions_add_commission_secondary' );

				?>

			</div><!-- / Sidebar Content -->

		</div>

		<!-- Action and nonce -->
		<input type="hidden" name="slicewp_action" value="add_commission" />
		<?php wp_nonce_field( 'slicewp_add_commission', 'slicewp_token', false ); ?>

		<!-- Submit -->
		<input type="submit" class="slicewp-form-submit slicewp-button-primary" value="<?php echo __( 'Add Commission', 'slicewp' ); ?>" />
		
	</form>

</div>