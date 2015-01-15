<?php

/*
 * This file is part of the limonetik-php-api package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace winzou\Limonetik\Exception;

class InvalidArgumentException extends \InvalidArgumentException
{
    public function __construct($argument)
    {
        parent::__construct(sprintf('Argument "%s" is missing or invalid.', $argument));
    }
}
