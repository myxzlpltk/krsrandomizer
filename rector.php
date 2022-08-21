<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPOffice\Set\PHPOfficeSetList;

return static function (RectorConfig $rectorConfig): void {
    // define sets of rules
    $rectorConfig->sets([
        PHPOfficeSetList::PHPEXCEL_TO_PHPSPREADSHEET
    ]);
};
