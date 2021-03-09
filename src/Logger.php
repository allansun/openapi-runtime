<?php
/*
 * This file is part of OpenApi Runtime.
 *
 * (c) Allan Sun <allan.sun@bricre.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenApiRuntime;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Logger
{
    static private ?Logger $instance;

    private LoggerInterface $logger;

    private function __construct()
    {
        $this->logger = new NullLogger();
    }

    /**
     * This function is only intended to be used in unit test
     * You do not normally need to call it
     */
    static public function reInitiate(): Logger
    {
        static::$instance = null;

        return self::getInstance();
    }

    static public function getInstance(): Logger
    {
        if (!isset(static::$instance)) {
            static::$instance = new Logger();
        }

        return static::$instance;
    }

    public function debug($message, $extra = []): Logger
    {
        if (getenv('DEBUG')) {
            $this->logger->debug($message, $extra);
        }

        return $this;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function setLogger(LoggerInterface $logger): Logger
    {
        $this->logger = $logger;

        return $this;
    }

    public function info($message, $extra = [])
    {
        $this->logger->info($message, $extra);
    }
}