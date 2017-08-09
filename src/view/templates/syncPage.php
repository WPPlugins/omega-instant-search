<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */ /** @var OmegaCommerce\Controller\Admin\SyncController $this */ ?>
<script type="text/javascript">
    var omegaSyncAjaxUrl = ajaxurl;

    if (omegaSyncAjaxUrl.search(/\?/) == -1) {
        omegaSyncAjaxUrl += "?";
    } else {
        omegaSyncAjaxUrl += "&";
    }
    omegaSyncAjaxUrl += "action=omega_data_sync&step_nonce=<?php echo wp_create_nonce( 'omega-commerce-sync-nonce' ) ?>";
</script>
<div class="wrap">
    <ul id="sync-message-block">
        <li>
            <div class="notice notice-info"><p>
                    <?php echo __("Data Reindexing, please wait "); ?><span class="spinner active is-active"
                                                                                 style="float: none; margin-left: 15px;"></span><br>
                </p>
            </div>
        </li>
        <li>
            <div class="notice notice-info"><p>
                    <?php echo __("Please do not close the window during reindexing. "); ?><br>
                    <?php echo __("You need to run reindexing only once. "); ?><br>
                </p></div>
        </li>
    </ul>

    <div class="notice notice-success" id="sync-message-block-done" style="display: none;">
        <p>
            <?php echo __("Finished execution."); ?>
        </p>
    </div>


    <div id="sync-process"></div>
</div>