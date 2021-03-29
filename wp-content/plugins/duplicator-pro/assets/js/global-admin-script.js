jQuery(document).ready(function($) {
    $('.duplicator-pro-plugin-activation-admin-notice .notice-dismiss').on('click', function (event) {
        event.preventDefault();
        $.post(ajaxurl, {
            action: 'duplicator_pro_dismiss_plugin_activation_admin_notice',
            nonce: dup_pro_global_script_data.dismiss_plugin_activation_admin_notice_nonce
        });
    });
});
