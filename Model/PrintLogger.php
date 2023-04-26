<?php

/**
 * @author GTStudio
 * @copyright Copyright (c) 2023 GTStudio
 */

declare(strict_types=1);

namespace Gtstudio\QueryLog\Model;

use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\Debug;

class PrintLogger implements LoggerInterface
{
    private $timer;
    private bool $logAllQueries;
    private float $logQueryTime;
    private bool $logCallStack;

    public function __construct(
        bool $logAllQueries = false,
        float $logQueryTime = 0.05,
        bool $logCallStack = false
    ) {
        $this->logAllQueries = $logAllQueries;
        $this->logQueryTime = $logQueryTime;
        $this->logCallStack = $logCallStack;
    }

    /**
     * {@inheritdoc}
     */
    public function log($str)
    {
        $str = '## ' . date('Y-m-d H:i:s') . " " . $str;

        echo var_export($str, true);
    }

    /**
     * {@inheritdoc}
     */
    public function startTimer()
    {
        $this->timer = microtime(true);
    }

    /**
     * Get formatted statistics message
     *
     * @param string $type Type of query
     * @param string $sql
     * @param array $bind
     * @param \Zend_Db_Statement_Pdo|null $result
     * @return string
     * @throws \Zend_Db_Statement_Exception
     */
    public function getStats(string $type, string $sql, array $bind = [], $result = null): string
    {
        $nl   = "\n";
        $time = sprintf('%.4f', microtime(true) - $this->timer);
        $message = <<<HTML
            <div style=""><div style="font-size:12px; color: white; background: red">## TIME: {$time}  ##
        HTML;
        if (!$this->logAllQueries && $time < $this->logQueryTime) {
            return '';
        }
        switch ($type) {
            case self::TYPE_CONNECT:
                $message .= 'CONNECT' . $nl;
                break;
            case self::TYPE_TRANSACTION:
                $message .= 'TRANSACTION ' . $sql . $nl;
                break;
            case self::TYPE_QUERY:
                $message .= 'QUERY' . $nl;
                $message .= 'SQL: ' . $sql . $nl;
                if ($bind) {
                    $message .= 'BIND: ' . var_export($bind, true) . $nl;
                }
                if ($result instanceof \Zend_Db_Statement_Pdo) {
                    $message .= 'AFF: ' . $result->rowCount() . $nl;
                }
                break;
        }

        $message .= <<<HTML
            </div>
        HTML;

        if ($this->logCallStack) {
            $trace = Debug::backtrace(true, false, false);
            $trace = substr($trace, strpos($trace, "\n") + 4);
            $trace = substr($trace, 0, 1150) . '...';
            $message .= <<<HTML
                <pre style="overflow: auto">{$trace}</pre>
                HTML;
        }

        $message .= <<<HTML
            {$nl} </div>
        HTML;

        return $message;
    }

    /**
     * @throws \Zend_Db_Statement_Exception
     */
    public function logStats($type, $sql, $bind = [], $result = null)
    {
        $stats = $this->getStats($type, $sql, $bind, $result);
        if ($stats) {
            $this->log($stats);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function critical(\Exception $e)
    {
        $this->log("EXCEPTION \n$e\n\n");
    }
}
