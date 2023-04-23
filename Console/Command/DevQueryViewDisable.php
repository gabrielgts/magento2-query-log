<?php

/**
 * Copyright © GTStudio All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Gtstudio\QueryLog\Console\Command;

use Magento\Framework\App\DeploymentConfig\Writer;
use Magento\Framework\Config\File\ConfigFilePool;
use Magento\Framework\DB\Logger\LoggerProxy as LoggerProxyAlias;
use Magento\Framework\Exception\FileSystemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DevQueryViewDisable extends Command
{
    private const SUCCESS_MESSAGE = "DB query logging disabled.";

    private Writer $deployConfigWriter;

    public function __construct(
        Writer $deployConfigWriter,
        $name = null
    ) {
        $this->deployConfigWriter = $deployConfigWriter;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     * @throws FileSystemException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $data = [LoggerProxyAlias::PARAM_ALIAS => LoggerProxyAlias::LOGGER_ALIAS_DISABLED];
        $this->deployConfigWriter->saveConfig(
            [ConfigFilePool::APP_ENV => [LoggerProxyAlias::CONF_GROUP_NAME => $data]]
        );

        $output->writeln("<info>" . self::SUCCESS_MESSAGE . "</info>");
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName("dev:query-view:disable");
        $this->setDescription("Disable Query Log on store front");
        parent::configure();
    }
}
