<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */ /** @var OmegaCommerce\Controller\Admin\SyncController $this */ ?>
<?php
if (!$this->errorCode): ?>
    <ul>
        <li style="position: relative; ">
            <?php if ($this->operation == \OmegaCommerce\Controller\Admin\SyncController::ACTION_CLEANUP): ?>
                Reindexing <?php echo $this->nextEntity->getHumanName(); ?>...
            <?php elseif ($this->operation == \OmegaCommerce\Controller\Admin\SyncController::ACTION_REINDEX): ?>
                <?php
                $percents = 100;
                if ($this->total > 0) {
                    $percents = round($this->currentTotal / $this->total * 100);
                }
                ?>
                <div class="sync-loader" style="width: <?php echo $percents ?>%;"></div>
                    Reindexing <?php echo $this->nextEntity->getHumanName(); ?> <?php echo $this->currentTotal ?>
                    / <?php echo $this->total ?> (<?php echo $percents; ?>%)
            <?php else: ?>
                <img src="<?php echo sprintf("%ssrc/view/images/fam_bullet_success.gif", WP_OMEGA_COMMERCE_PLUGIN_URL); ?>" class="v-middle"> Indexation is completed
            <?php endif; ?>
        </li>
    </ul>

<?php else: ?>
    <ul>
        <li style="position: relative; ">
            <?php
            $isFinish = false;
            echo $this->errorMessage;
            ?>
            Trying again...
        </li>
    </ul>
<?php endif; ?>
