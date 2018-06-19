<?php

namespace kristijorgji\PhpTestGenerator\Console;

use kristijorgji\PhpTestGenerator\AppInfo;
use kristijorgji\PhpTestGenerator\Config\ConfigFactory;
use kristijorgji\PhpTestGenerator\Console\Commands\GenerateTestsCommand;
use kristijorgji\PhpTestGenerator\Console\Commands\GenerateFactoriesCommand;
use kristijorgji\PhpTestGenerator\Console\Commands\InitCommand;
use kristijorgji\PhpTestGenerator\FileSystem\FileSystem;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpTestGeneratorApplication extends Application
{
    /**
     * @param string $version
     */
    public function __construct($version = AppInfo::VERSION)
    {
        $configFactory = new ConfigFactory(new FileSystem());

        parent::__construct(sprintf('%s by Kristi Jorgji - %s', AppInfo::NAME, $version));
        $this->addCommands([
           new InitCommand('Initialize the application'),
           new GenerateTestsCommand($configFactory, 'Generate tests')
        ]);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int 0 if everything went fine, or an error code
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        if ($input->hasParameterOption(['--help', '-h']) === false && $input->getFirstArgument() !== null) {
            $output->writeln(str_repeat('-', strlen($this->getLongVersion())));
            $output->writeln($this->getLongVersion());
            $output->writeln(str_repeat('-', strlen($this->getLongVersion())));
            $output->writeln('');
        }

        return parent::doRun($input, $output);
    }
}
