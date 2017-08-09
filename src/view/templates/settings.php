<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */ /** @var OmegaCommerce\Controller\Admin\SettingController $this */ ?>
<div class="wrap">
    <?php
    if (isset($error_message)) : ?>
        <div class="notice notice-error">
            <p><?php echo __($error_message); ?></p>
        </div>
    <?php endif; ?>
    <form method="post" action="options.php">

        <?php @settings_fields('omega_search-group'); ?>
        <h2>Search Box Settings</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="omega_search_box_selector">Parent CSS selector</label></th>
                <td>
                    <input type="text" name="omega_search_box_selector" id="omega_search_box_selector"
                           value="<?php echo get_option('omega_search_box_selector'); ?>" size="50"/>

                    <p class="description">If you would like to <b>insert search box</b>, please enter a CSS selector of
                        parent block (eg. <i>#logo</i>).<br>If you need a help, please contact us <a
                            href="mailto:support@omegacommerce.com">support@omegacommerce.com</a></p>
                </td>
            </tr>
        </table>

        <h2>Additional Settings</h2>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="omega_search_custom_css">Custom CSS</label></th>
                <td>
                    <textarea name="omega_search_custom_css" id="omega_search_custom_css" rows="5"
                              cols="60"><?php echo get_option('omega_search_custom_css'); ?></textarea>

                    <p class="description">HTML markup and CSS classes may change. Don’t add complex CSS.</p>
                </td>
            </tr>
        </table>

        <h2>API Settings</h2>

        <table class="form-table">
            <?php if (get_option('omega_api_access_iuid')): ?>
                <tr valign="top">
                    <th scope="row"><label for="omega_api_access_iuid">Instance ID</label></th>
                    <td>
                        <b><?php echo get_option('omega_api_access_iuid'); ?></b>
                        <!--                        <br><br>-->
                        <!--                        <a href='#' onclick="disconnect_account(this); return false;">-->
                        <!--                            --><?php //echo __( 'Disconnect Account' ) ?>
                        <!--                        </a>-->
                    </td>
                </tr>
            <?php endif; ?>
            <tr valign="top">
                <th scope="row"><label for="omega_api_max_sync_number">Max. number of items reindexed per one request</label></th>
                <td>
                    <input type="number" name="omega_api_max_sync_number" id="omega_api_max_sync_number"
                           value="<?php echo get_option('omega_api_max_sync_number'); ?>" >

                    <p class="description">Decrease if your server can't handle data reindexing requests.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="omega_api_sync_mode">Run Data Reindexing By CRON</label></th>
                <td>
                    <input type="hidden" name="omega_api_sync_mode" value="0">
                    <input type="checkbox" name="omega_api_sync_mode" id="omega_api_sync_mode"
                           value="1" <?php checked('1', get_option('omega_api_sync_mode')); ?> >

                    <p class="description">If option is disabled, plugin will synchronize changes right after save via
                        admin panel. <br>If option is enabled, plugin will synchronize changes by cron. Make sure that cron
                        is working in your store.</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="omega_api_access_base_url">API URL</label></th>
                <td>
                    <input type="text" name="omega_api_access_base_url" id="omega_api_access_base_url"
                           value="<?php echo get_option('omega_api_access_base_url'); ?>" size="50"/>

                    <p class="description">Used for debugging. Don't change this</p>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="omega_api_access_is_validate_ssl">Enable validation of SSL
                        certificate</label></th>
                <td>
                    <input type="hidden" name="omega_api_access_is_validate_ssl" value="0">
                    <input type="checkbox" name="omega_api_access_is_validate_ssl" id="omega_api_access_is_validate_ssl"
                           value="1" <?php checked('1', get_option('omega_api_access_is_validate_ssl')); ?> >
                </td>
            </tr>
            <?php echo apply_filters('omega_commerce_core_setting_form', '') ?>
        </table>

        <?php @submit_button(); ?>
    </form>


</div>