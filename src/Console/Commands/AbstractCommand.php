<?php

namespace kristijorgji\PhpTestGenerator\Console\Commands;

use kristijorgji\PhpTestGenerator\AppInfo;
use kristijorgji\PhpTestGenerator\Config\ConfigFactory;
use kristijorgji\PhpTestGenerator\Generators\GenerateResponse;
use kristijorgji\PhpTestGenerator\Generators\GeneratorContract;
use kristijorgji\PhpTestGenerator\Generators\GeneratorFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var GeneratorContract
     */
    private $generator;

    /**
     * AbstractCommand constructor.
     * @param ConfigFactory $configFactory
     * @param string $name
     */
    public function __construct(ConfigFactory $configFactory, string $name)
    {
        $this->configFactory = $configFactory;
        parent::__construct($name);
    }

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            '--configuration', '-c', InputOption::VALUE_REQUIRED,
            'The configuration file to load'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    public function bootstrap(InputInterface $input, OutputInterface $output)
    {
        if ($this->getConfig() === null) {
            $this->loadConfig($input, $output);
        }

        if ($this->generator === null) {
            $this->generator = $this->loadGenerator();
        }
    }

    /**
     * @return GeneratorContract
     */
    protected function getGenerator() : GeneratorContract
    {
        return $this->generator;
    }

    /**
     * @param array $config
     */
    protected function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return array|null
     */
    protected function getConfig() : ?array
    {
        return $this->config;
    }

    /**
     * @return GeneratorContract
     */
    protected function loadGenerator() : GeneratorContract
    {
        return (new GeneratorFactory())->get($this->getConfig());
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function loadConfig(InputInterface $input, OutputInterface $output)
    {
        $configFilePath = $this->locateConfigFile($input);
        $output->writeln('<info>using config file</info> .' . str_replace(getcwd(), '', realpath($configFilePath)));
        $this->setConfig($this->configFactory->get($configFilePath));
    }

    /**
     * Returns config file path
     *
     * @param InputInterface $input
     * @return string
     */
    protected function locateConfigFile(InputInterface $input) : string
    {
        $configFile = $input->getOption('configuration');
        if (null === $configFile || false === $configFile) {
            return $this->locateDefaultConfigFile();
        }

        return getcwd() . DIRECTORY_SEPARATOR . $configFile;
    }

    /**
     * @return string
     */
    protected function locateDefaultConfigFile() : string
    {
        $cwd = getcwd();
        return $cwd . DIRECTORY_SEPARATOR . AppInfo::DEFAULT_CONFIG_FILENAME;
    }

    /**
     * @param OutputInterface $output
     * @param GenerateResponse $generateResponse
     */
    protected function outputGenerationResult(OutputInterface $output, GenerateResponse $generateResponse)
    {
        $output->writeln('');
        foreach ($generateResponse->getResponses() as $generateSuiteResponse) {
            $output->writeln(
                sprintf(
                    '[%s]: %s test files generated%s',
                    $generateSuiteResponse->getSuiteName(),
                    count($generateSuiteResponse->getPaths()),
                    PHP_EOL
                )
            );
            foreach ($generateSuiteResponse->getPaths() as $path) {
                $output->writeln(
                    sprintf('Created: %s', $path)
                );
            }

            $output->writeln(str_repeat('-', 41) . PHP_EOL);
        }
    }
}
