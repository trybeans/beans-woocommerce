<?php

namespace BeansWoo\Server;

/**
 * ProductReview Webhook
 *
 * Send webhook on new product reviews
 *
 * @class ReviewHookController
 * @since 3.6.0
 */
class ReviewHookController
{
    /**
     * Initialize controller.
     *
     * @return void
     *
     * @since 3.6.0
     */
    public static function init()
    {
        // register action to trigger on new comment creation
        add_action('comment_post', array(__CLASS__, 'triggerWebhookAction'), 10, 3);
        add_filter('woocommerce_webhook_payload', array(__CLASS__, 'getWebhookPayload'), 10, 4);
    }

    /**
     * Trigger an action that post a webhook about the new comment created
     *
     * @param int           $comment_id The id of the comment created.
     * @param int|string    $comment_approved 1 if the comment is approved, 0 if not, 'spam' if spam.
     * @param array         $comment_data      Comment data.
     *
     * @return void
     *
     * @since 3.6.0
     */
    public static function triggerWebhookAction($comment_id, $comment_approved, $comment_data)
    {
        if ($comment_data['comment_type'] == 'review') {
            do_action('wc_beans_webhook_review_created', $comment_id);
        }
    }

    /**
     * Edit webhook payload for the action wc_beans_webhook_review_created
     * This is to load the full review object in the webhook that is being sent.
     *
     * @param array $payload
     * @param string $resource
     * @param int $resource_id The id of the review created.
     * @param int $webhook_id The id of the webhook created.
     *
     * @return array $payload
     *
     * @since 3.6.0
     */
    public static function getWebhookPayload($payload, $resource, $resource_id, $webhook_id)
    {
        if ($resource !== 'action') {
            return $payload;
        }

        $webhook = wc_get_webhook($webhook_id);
        $event_name = current($webhook->get_hooks());
        if ($event_name !== 'wc_beans_webhook_review_created') {
            return $payload;
        }

        // Build the payload with the same user context as the user who created
        // the webhook -- this avoids permission errors as background processing
        // runs with no user context.
        // cf: https://woocommerce.github.io/code-reference/files
        // /woocommerce-includes-class-wc-webhook.html#source-view.481
        $current_user = get_current_user_id();
        wp_set_current_user($webhook->get_user_id());

        $version = str_replace('wp_api_', '', $webhook->get_api_version());
        $payload = wc()->api->get_endpoint_data("/wc/{$version}/products/reviews/{$resource_id}");

        // Restore the current user.
        wp_set_current_user($current_user);

        return $payload;
    }
}
