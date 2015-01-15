<?php

/*
 * This file is part of the limonetik-php-api package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace winzou\Limonetik\Buzz;

use Buzz\Exception\LogicException;
use Buzz\Message\Response;

class JsonResponse extends Response
{
    /**
     * @throws LogicException
     * 
     * @return array
     */
    public function getContentJson()
    {
        $content = $this->getContent();

        // Remove unwanted utf8 BOM
        if(substr($content, 0, 3) == pack('CCC', 239, 187, 191)) {
            $content = substr($content, 3);
        }

        $json = json_decode($content, true);
        if (null === $json) {
            throw new LogicException("Response content is not valid json: \n\n".$content);
        }

        return $json;
    }
}
