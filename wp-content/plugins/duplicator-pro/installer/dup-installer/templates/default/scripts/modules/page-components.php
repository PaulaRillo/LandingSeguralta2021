<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<script>
    DUPX.pageComponents = {
        pageContent: null,
        init: function () {
            this.pageContent = $('#main-content-wrapper');
        },
        showProgress: function (options) {
            DUPX.topMessages.empty();
            this.pageContent.hide();
            DUPX.ajaxError.hide();
            DUPX.progress.show(options);
        },
        showError: function (result, textStatus, jqXHR, tryAgainButtonCallback) {
            DUPX.ajaxError.update(result, textStatus, jqXHR, tryAgainButtonCallback);
            DUPX.progress.hide();
            this.pageContent.hide();
            DUPX.ajaxError.show();
        },
        showContent: function () {
            DUPX.progress.hide();
            DUPX.ajaxError.hide();
            this.pageContent.show();
        }
    };

    $(document).ready(function () {
        DUPX.pageComponents.init();
    });
</script>