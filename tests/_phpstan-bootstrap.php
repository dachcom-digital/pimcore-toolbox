<?php

# prevent php warnings in phpstan.
if (!defined('PIMCORE_PRIVATE_VAR')) {
    define('PIMCORE_PRIVATE_VAR', '');
}

/** hide class alias in IDE code completion */
$multiHrefClass = '\\Pimcore\\Model\\Document\\Tag\\Multihref';
class_alias(\Pimcore\Model\Document\Tag\Relations::class, $multiHrefClass, true);