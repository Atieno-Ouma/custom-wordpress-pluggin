<?php
/**
 * Custom Checkout Page Template
 */

// Get the WooCommerce checkout object
$checkout = WC()->checkout();

// Start the checkout process
$checkout->process_checkout();

// Get the checkout fields
$checkout_fields = $checkout->get_checkout_fields();

// Display the checkout form
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo get_bloginfo('name'); ?> - Checkout</title>
    <?php wp_head(); ?>
    <style>
        /* Center everything on the screen */
        .content-area {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* Centering the components */
        .col2-set, #order_review, .bank-transfer-details {
            transform: scale(0.75);
            transform-origin: top left;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body <?php body_class(); ?>>
<div id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="entry-content">
                <form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url(wc_get_checkout_url()); ?>" enctype="multipart/form-data">
                    <?php if ($checkout->get_checkout_fields()) : ?>
                        <?php do_action('woocommerce_checkout_before_customer_details'); ?>
                        <div class="col2-set" id="customer_details">
                            <div class="col-1">
                                <?php do_action('woocommerce_checkout_billing'); ?>
                            </div>
                            <div class="col-2">
                                <h3 id="order_review_heading"><?php esc_html_e('Your order', 'woocommerce'); ?></h3>
                                <?php do_action('woocommerce_checkout_before_order_review'); ?>
                                <div id="order_review" class="woocommerce-checkout-review-order">
                                    <?php do_action('woocommerce_checkout_order_review'); ?>
                                </div>
                                <?php do_action('woocommerce_checkout_after_order_review'); ?>

                                <!-- Bank Transfer Details -->
                                <div class="bank-transfer-details">
                                    <h3><?php esc_html_e('Bank Transfer Details', 'woocommerce'); ?></h3>
                                    <p>
                                        <?php esc_html_e('Please use the following instructions to initiate a secure bank transfer for your order:', 'woocommerce'); ?>
                                    </p>
                                    <form id="bank-transfer-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                                        <?php wp_nonce_field( 'bank_transfer_verification', 'bank_transfer_nonce' ); ?>
                                        <p>
                                            <label for="iban"><?php esc_html_e('IBAN:', 'woocommerce'); ?></label>
                                            <input type="text" id="iban" name="iban" placeholder="<?php esc_html_e('Enter your IBAN number', 'woocommerce'); ?>" required style="width: 500px; height: 50px; padding: 5px;">
                                        </p>
                                        <p>
                                            <input type="submit" value="<?php esc_html_e('Verify IBAN', 'woocommerce'); ?>" style="background-color: #4CAF50; color: white; padding: 20px 50px; font-size: 16px; border: none; cursor: pointer;" disabled>
                                        </p>
                                    </form>
                                    <p id="iban-verification-message"></p>
                                    <script>
                                        (function($) {
                                            $(document).ready(function() {
                                                $("#iban").keyup(function() {
                                                    // Enable verification button only if IBAN is not empty
                                                    if ($(this).val().length > 0) {
                                                        $("#bank-transfer-form input[type='submit']").prop("disabled", false);
                                                    } else {
                                                        $("#bank-transfer-form input[type='submit']").prop("disabled", true);
                                                        $("#iban-verification-message").text("");
                                                    }
                                                });

                                                $("#bank-transfer-form").submit(function(event) {
                                                    event.preventDefault(); // Prevent default form submission
                                                    var iban = $("#iban").val();
                                                    var nonce = $("#bank_transfer_nonce").val();

                                                    $("#bank-transfer-form input[type='submit']").click(function(event) {
                                                        event.preventDefault(); // Prevent default form submission
                                                        // Your existing code for submitting the AJAX request
                                                    });

                                                    // Disable the button and show a loading message
                                                    $("#bank-transfer-form input[type='submit']").prop("disabled", true).val("Verifying...");

                                                    // Send AJAX request to your server-side script for IBAN verification
                                                    $.ajax({
                                                        url: ajaxUrl + "?action=verify_iban", // Replace ajaxUrl with the actual URL to the verify-iban.php file
                                                        type: "POST",
                                                        data: {
                                                            iban: iban,
                                                            nonce: nonce
                                                        },
                                                        success: function(response) {
                                                            var result = JSON.parse(response);
                                                            if (result.success) {
                                                                $("#iban-verification-message").text("IBAN is valid!");
                                                            } else {
                                                                $("#iban-verification-message").text("IBAN is invalid.");
                                                            }

                                                            // Re-enable the button
                                                            $("#bank-transfer-form input[type='submit']").prop("disabled", false).val("Verify IBAN");
                                                        },
                                                        error: function(jqXHR, textStatus, errorThrown) {
                                                            console.error("Error verifying IBAN:", textStatus, errorThrown);
                                                            $("#iban-verification-message").text("An error occurred. Please try again later.");

                                                            // Re-enable the button
                                                            $("#bank-transfer-form input[type='submit']").prop("disabled", false).val("Verify IBAN");
                                                        }
                                                    });
                                                });
                                            });
                                        })(jQuery);
                                    </script>                            </div>                            </div>
                        </div>
                        <?php do_action('woocommerce_checkout_after_customer_details'); ?>
                    <?php endif; ?>
                </form>
            </div>
        </article>
    </main>
</div>
<?php wp_footer(); ?>
</body>
</html>