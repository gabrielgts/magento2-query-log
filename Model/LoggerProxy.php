<?php

/**
 * @author GTStudio
 * @copyright Copyright (c) 2023 GTStudio
 */

declare(strict_types=1);

namespace Gtstudio\QueryLog\Model;

use Magento\Framework\DB\Logger\FileFactory;
use Magento\Framework\DB\Logger\QuietFactory;
use Magento\Framework\DB\LoggerInterface;
use Magento\Framework\DB\Logger\LoggerProxy as MagentoLogProxy;

class LoggerProxy extends MagentoLogProxy
{
    public const LOGGER_ALIAS_PRINT = 'print';

    private $logger;
    private FileFactory $fileFactory;
    protected $quietFactory;
    private PrintLoggerFactory $printLoggerFactory;
    private string $loggerAlias;
    private int $logAllQueries;
    private $logQueryTime;
    private int $logCallStack;

    public function __construct(
        FileFactory $fileFactory,
        QuietFactory $quietFactory,
        PrintLoggerFactory $printLoggerFactory,
        $loggerAlias = 'disabled',
        $logAllQueries = 0,
        $logQueryTime = 0.001,
        $logCallStack = 0
    ) {
        parent::__construct(
            $fileFactory,
            $quietFactory,
            $loggerAlias,
            $logAllQueries,
            $logQueryTime,
            $logCallStack
        );

        $this->fileFactory = $fileFactory;
        $this->quietFactory = $quietFactory;
        $this->printLoggerFactory = $printLoggerFactory;
        $this->loggerAlias = $loggerAlias;
        $this->logAllQueries = $logAllQueries;
        $this->logQueryTime = $logQueryTime;
        $this->logCallStack = $logCallStack;
        $this->logger = null;
    }

    /**
     * Get logger object. Initialize if needed.
     * @return LoggerInterface
     */
    private function getLogger(): LoggerInterface
    {
        if ($this->logger === null) {
            switch ($this->loggerAlias) {
                case self::LOGGER_ALIAS_FILE:
                    $this->logger = $this->fileFactory->create(
                        [
                            'logAllQueries' => $this->logAllQueries,
                            'logQueryTime' => $this->logQueryTime,
                            'logCallStack' => $this->logCallStack,
                        ]
                    );
                    break;
                case self::LOGGER_ALIAS_PRINT:
                    $this->logger = $this->printLoggerFactory->create(
                        [
                            'logAllQueries' => $this->logAllQueries,
                            'logQueryTime' => $this->logQueryTime,
                            'logCallStack' => $this->logCallStack,
                        ]
                    );
                    break;
                default:
                    $this->logger = $this->quietFactory->create();
                    break;
            }
        }
        return $this->logger;
    }

    /**
     * @param string $type
     * @param string $sql
     * @param array $bind
     * @param \Zend_Db_Statement_Pdo|null $result
     * @return void
     */
    public function logStats($type, $sql, $bind = [], $result = null): void
    {
        $this->getLogger()->logStats($type, $sql, $bind, $result);
    }
}
