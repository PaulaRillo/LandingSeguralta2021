<?php
/**
 *
 * @package templates/default
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

$paramsManager = DUPX_Paramas_Manager::getInstance();
?>
<script>
    const validateAction = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::ACTION_VALIDATE); ?>;
    const validateToken = <?php echo DupProSnapJsonU::wp_json_encode(DUPX_Ctrl_ajax::generateToken(DUPX_Ctrl_ajax::ACTION_VALIDATE)); ?>;

    DUPX.initialValidateAction = function (validateArea, validateNoResult, isValidCallback, showContentOnResult) {
        DUPX.pageComponents.showProgress({
            'title': 'System validation',
            'bottomText':
                    '<i>Keep this window open during the validation process.</i><br/>' +
                    '<i>This can take several minutes.</i>'
        });

        DUPX.StandarJsonAjaxWrapper(
                validateAction,
                validateToken,
                {},
                function (data) {
                    if (showContentOnResult) {
                        DUPX.pageComponents.showContent();
                    }
                    validateNoResult.detach();
                    validateArea.empty().append(data.actionData.html);
                    validateArea.find("*[data-type='toggle']").click(DUPX.toggleClick);
                    $('#validate-global-badge-status').removeClass('wait');

                    if (data.actionData.validateData.arcCheck === 'Fail') {
                        $('#s1-area-archive-file-link').trigger('click');
                    }

                    if (data.actionData.validateData.all_success !== true) {
                        $('#validate-area-link').trigger('click');
                    }

                    if (data.actionData.validateData.req_success !== true) {
                        stepActions.addClass('no-display')
                                .filter("#error_action").removeClass('no-display')
                                .find('.s1-accept-check').each(function () {
                            if (data.actionData.validateData.is_only_permission_issue) {
                                $('#validate-global-badge-status').addClass('warn');
                                $(this).removeClass('no-display');
                            } else {
                                $('#validate-global-badge-status').addClass('fail');
                                $(this).addClass('no-display');
                            }
                        });
                    } else {
                        $('#validate-global-badge-status').addClass('pass');
                        if (typeof isValidCallback === "function") {
                            isValidCallback();
                        }
                    }
                },
                DUPX.ajaxErrorDisplayRestart
                );
    }
</script>