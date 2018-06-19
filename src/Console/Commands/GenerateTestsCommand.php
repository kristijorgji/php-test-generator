<?php

namespace kristijorgji\PhpTestGenerator\Console\Commands;

use kristijorgji\PhpTestGenerator\Generators\Exceptions\GenerateException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateTestsCommand extends AbstractCommand
{
    /**
     * @return void
     */
    protected function configure()
    {
        parent::configure();

        $this->setName('generate:tests')
            ->setDescription('Generate test skeletons')
            ->setHelp(sprintf(
                '%sGenerates tests boilerplate for all the specified files%s',
                PHP_EOL,
                PHP_EOL
            ));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->bootstrap($input, $output);

        try {
            $this->outputGenerationResult($output, $this->getGenerator()->generate());
        } catch (GenerateException $e) {
            $this->outputGenerationResult($output, $e->getPartialResponse());
            throw $e->getPrevious();
        }
    }
}
