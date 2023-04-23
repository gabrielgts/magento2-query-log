<?php

/**
 * Copyright © GTStudio All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Gtstudio\QueryLog\Console\Command;

use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\DB\Logger\LoggerProxy as LoggerProxyAlias;
use Magento\Framework\Exception\FileSystemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\DeploymentConfig\Writer;
use Gtstudio\QueryLog\Model\LoggerProxy;

class DevQueryViewEnable extends Command
{
    /**
     * input parameter log-all-queries
     */
    private const INPUT_ARG_LOG_ALL_QUERIES = 'include-all-queries';

    /**
     * input parameter log-query-time
     */
    private const INPUT_ARG_LOG_QUERY_TIME = 'query-time-threshold';

    /**
     * input parameter log-call-stack
     */
    private const INPUT_ARG_LOG_CALL_STACK = 'include-call-stack';

    /**
     * Success message
     */
    private const SUCCESS_MESSAGE = 'DB query log enabled on store frontend.';

    private Writer $deployConfigWriter;

    public function __construct(
        Writer $deployConfigWriter,
        $name = null
    ) {
        parent::__construct($name);
        $this->deployConfigWriter = $deployConfigWriter;
    }

    /**
     * {@inheritdoc}
     * @throws FileSystemException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $data = [LoggerProxyAlias::PARAM_ALIAS => LoggerProxy::LOGGER_ALIAS_PRINT];

        $logAllQueries = $input->getOption(self::INPUT_ARG_LOG_ALL_QUERIES);
        $logQueryTime = $input->getOption(self::INPUT_ARG_LOG_QUERY_TIME);
        $logCallStack = $input->getOption(self::INPUT_ARG_LOG_CALL_STACK);

        $data[LoggerProxyAlias::PARAM_LOG_ALL] = (int)($logAllQueries != 'false');
        $data[LoggerProxyAlias::PARAM_QUERY_TIME] = number_format((float) $logQueryTime, 3);
        $data[LoggerProxyAlias::PARAM_CALL_STACK] = (int)($logCallStack != 'false');

        $configGroup[LoggerProxyAlias::CONF_GROUP_NAME] = $data;

        $this->deployConfigWriter->saveConfig([ConfigFilePool::APP_ENV => $configGroup]);

        $output->writeln("<info>" . self::SUCCESS_MESSAGE . "</info>");
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("dev:query-view:enable");
        $this->setDescription("Enable query log on store front.");
        $this->setDefinition(
            [
                new InputOption(
                    self::INPUT_ARG_LOG_ALL_QUERIES,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Log all queries. [true|false]',
                    "true"
                ),
                new InputOption(
                    self::INPUT_ARG_LOG_QUERY_TIME,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Query time thresholds.',
                    "0.001"
                ),
                new InputOption(
                    self::INPUT_ARG_LOG_CALL_STACK,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Include call stack. [true|false]',
                    "true"
                ),
            ]
        );
        parent::configure();
    }
}
