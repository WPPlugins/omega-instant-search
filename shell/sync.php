<?php
/**
 * Omega Commerce
 *
 * Licence: MIT https://opensource.org/licenses/MIT
 * Copyright: 2016 Omega Commerce LLC https://omegacommerce.com
 */
// if you use wp multisite, uncomment the following line and enter your info.
// $_SERVER['HTTP_HOST'] = 'your_host.com';
// $_SERVER['REQUEST_URI'] = '/your_wp_folder/';

if (php_sapi_name() !== 'cli') {
    die("you can run this script only via shell");
}

if ( !defined('ABSPATH') ) {
    /** Set up WordPress environment */
    require_once( dirname( __FILE__ ) . '/../../../../wp-load.php' );
}

function progressBar($i, $total) {
    if ($total == 0) {
        $perc = 100;
    } else {
        $perc = floor(($i / $total) * 100);
    }
    $left = 100 - $perc;
    $write = sprintf("\033[0G\033[2K[%'={$perc}s>%-{$left}s] $perc%% - $i/$total", "", "");
    fwrite(STDERR, $write);
}

$quiet = false;
$full = false;
foreach($argv as $i => $arg) {
    if ($i == 0) {
        continue;
    }
    if ($arg == "--quiet") {
        $quiet = true;
    } elseif ($arg == "--full") {
        $full = true;
    } else {
        echo "Avaliable options:\n\n";
        echo "--quiet - hide progress\n";
        echo "--full - clean indexes and run reindex\n";
        echo "\n";
        die;
    }
}
$indexer = $omegaManager->getApiIndexer();

if ($full) {
    $tries = 1;
    while (true) {
        try {
            $indexer->clean();
            break;
        } catch (\OmegaCommerce\Api\Exception $e) {
            $write = sprintf("\033[0G\033[2K%'= A" . $e->getMessage() . " Trying again ($tries)...", "", "");
            fwrite(STDERR, $write);
            sleep(5);
            $tries++;
            if ($tries > 10) {
                exit(1);
            }
            continue;
        }
    }
}

$i = 1;
$tries = 1;

delete_option(\OmegaCommerce\Model\Config::NOTICE_FLAG_ASK_REINDEX);

$limit = get_option('omega_api_max_sync_number');
if ($limit < 1) {
    $limit = 200;
}
$limit = 100;
//http://www.termsys.demon.co.uk/vtansi.htm
foreach ($indexer->getEntities() as $entity) {
    fwrite(STDERR, "\033[31m"."Reindexing ".$entity->getHumanName()."...\n\033[0m");
    while (true) {
        try {
            $count = $indexer->removeEntity($entity, 500);
            if ($count == 0) {
                break;
            }
        } catch (\OmegaCommerce\Api\Exception $e) {
            $write = sprintf("\033[0G\033[2K%'= A" . $e->getMessage() . " Trying again ($tries)...", "", "");
            fwrite(STDERR, $write);
            sleep(5);
            $tries++;
            if ($tries > 10) {
                exit(1);
            }
            continue;
        }
        $tries = 1;
    }

    $totalNumber = $indexer->reindexQueueLength($entity);
    if ($totalNumber == 0 && $entity->getMainTable()) {
        fwrite(STDERR, "everything is updated\n");
        continue;
    }
    while (true) {
        try {
            $indexer->reindexEntity($entity, $limit);
        } catch (\OmegaCommerce\Api\Exception $e) {
            $write = sprintf("\033[0G\033[2K%'= A" . $e->getMessage() . " Trying again ($tries)...", "", "");
            fwrite(STDERR, $write);
            sleep(5);
            $tries++;
            if ($tries > 10) {
                exit(1);
            }
            continue;
        }
        $tries = 1;
        $currentTotal = $i * $limit;
        if ($currentTotal > $totalNumber) {
            $currentTotal = $totalNumber;
        }
        if (!$quiet) {
            progressBar($currentTotal, $totalNumber);
        }
        if ($currentTotal >= $totalNumber) {
            break;
        }
        $i++;
    }
    fwrite(STDERR, "\n");
}
fwrite(STDERR, "\033[32m"."Reindexing is completed!\n");