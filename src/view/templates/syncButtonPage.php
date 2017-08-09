<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */ /** @var OmegaCommerce\Controller\Admin\SyncController $this */ ?>
<div class="wrap">
    <?php if (isset($_GET["activation"])) : ?>
        <div class="notice notice-info"><p>
                To start using our plugin, please click the button below.<br>
                Plugin will syncronise data of your categories, products, posts, pages with our search servers. And you'll be able to perform the instant search.
            </p>
        </div>
    <?php endif; ?>
    <?php
    if (isset($error_message)) : ?>
        <div class="notice notice-error">
            <p><?php echo __($error_message); ?></p>
        </div>
    <?php endif; ?>
    <?php if ($this->auth->isAuthorized()): ?>
        <h2>Data Reindexing
            <?php do_action('omega_search_sync_success'); ?>
        </h2>
        <br>
        <a href="<?php menu_page_url('omega_commerce_sync', true) ?>"
           class="add-new-h2">
            <?php echo __('Run Data Reindexing') ?>
        </a>
        <br><br>
        <a href="<?php menu_page_url('omega_commerce_sync_page', true) ?>&clear=1"
           class="add-new-h2">
            <?php echo __('Clean all search indexes') ?>
        </a>
    <?php endif; ?>
</div>