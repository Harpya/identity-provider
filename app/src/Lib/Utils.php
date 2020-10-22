<?php
declare(strict_types=1);

namespace Harpya\IP\Lib;

/**
 * Simply checks if last character of a given URL is '/'.
 * If not, just add it.
 */
function addSlashAtEnd(&$url) : void
{
    if (!$url || (is_string($url) && substr($url, -1) !== '/')) {
        $url .= '/';
    }
}
