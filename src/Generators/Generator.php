<?php

namespace kristijorgji\PhpTestGenerator\Generators;

use DirectoryIterator;
use kristijorgji\PhpTestGenerator\Config\Config;
use kristijorgji\PhpTestGenerator\Config\SuiteConfig;
use kristijorgji\PhpTestGenerator\FileSystem\FileSystemInterface;
use kristijorgji\PhpTestGenerator\Generators\Exceptions\GenerateException;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;

class Generator extends GeneratorContract
{
    /**
     * @var FileSystemInterface
     */
    private $fileSystem;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var array
     */
    private $forAccessModifiers = [
        Class_::MODIFIER_PUBLIC,
        Class_::MODIFIER_PROTECTED
    ];

    /**
     * @param FileSystemInterface $fileSystem
     * @param Config $config
     */
    public function __construct(FileSystemInterface $fileSystem, Config $config)
    {
        $this->fileSystem = $fileSystem;
        $this->config = $config;
    }

    /**
     * @return GenerateResponse
     * @throws GenerateException
     */
    public function generate() : GenerateResponse
    {
        $response = new GenerateResponse();

        try {
            foreach ($this->config->getSuiteConfigs() as $suiteConfig) {
                $generateSuiteResponse = new GenerateSuiteResponse($suiteConfig->getName());
                $suiteRealDirectoryPath = realpath($suiteConfig->getSourcePath());
                if ($suiteRealDirectoryPath == false) {
                    throw new GenerateException(
                        sprintf(
                            'Entry directory [%s] for suite [%s] does not exist.',
                            $suiteConfig->getSourcePath(),
                            $suiteConfig->getName()
                        ),
                        null,
                        $response
                    );
                }
                foreach ($this->processSuite(realpath($suiteConfig->getSourcePath()), $suiteConfig) as $writtenPath) {
                    $generateSuiteResponse->addPath($writtenPath);
                }
                $response->addGenerateSuiteResponse($generateSuiteResponse);
            }
        } catch (\Exception $e) {
            throw new GenerateException(
                $e->getMessage(),
                $e,
                $response
            );
        }

        return $response;
    }

    /**
     * @param string $realPath
     * @param SuiteConfig $config
     * @return \Generator
     */
    protected function processSuite(string $realPath, SuiteConfig $config) : \Generator
    {
        $prettyPrinter = new Standard();
        $shouldExclude = function (string $path) use ($config) {
            foreach ($config->getExcludePatterns()->all() as $excludePattern) {
                if ($excludePattern[0] === '#') {
                    return preg_match($excludePattern, $path) == 1;
                } else {
                    $excludePath = realpath($config->getSourcePath() . '/' . $excludePattern);
                    if ($excludePath === $path) {
                        return true;
                    }
                }
            }

            return false;
        };

        foreach (new DirectoryIterator($realPath) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            if ($shouldExclude($fileInfo->getRealPath())) {
                continue;
            }

            if ($fileInfo->isFile()) {
                if ($fileInfo->getExtension() !== 'php') {
                    continue;
                }

                $filenameWithoutExtension = str_replace(
                    '.' . $fileInfo->getExtension(),
                    '',
                    $fileInfo->getFilename()
                );

                $testFileDirectory = $config->getTestPath()
                    . str_replace(
                        realpath($config->getSourcePath()),
                        '',
                        $realPath
                    );

                $testFilePath = $testFileDirectory
                    . '/'
                    . $filenameWithoutExtension
                    . 'Test.'
                    . $fileInfo->getExtension();

                if (! file_exists($testFilePath)) {
                    $rootNode = $this->processClass(
                        $filenameWithoutExtension,
                        $this->fileSystem->readFile($fileInfo->getRealPath()),
                        $config->getExtends()
                    );

                    $content = $prettyPrinter->prettyPrintFile([$rootNode]);

                    if (!file_exists($testFileDirectory)) {
                        mkdir($testFileDirectory, 0777, true);
                    }

                    file_put_contents($testFilePath, $content);
                    yield $testFilePath;
                }
            } else {
                yield from $this->processSuite($fileInfo->getRealPath(), $config);
            }
        }
    }

    /**
     * @param string $className
     * @param string $content
     * @param string $extends
     * @return Node\Stmt\Namespace_
     * @throws \Exception
     */
    protected function processClass(string $className, string $content, string $extends) : Node\Stmt\Namespace_
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);

        $methods = [];
        $nodes = $parser->parse($content);
        $this->collectMethodNames($nodes, $methods);

        if (! property_exists($nodes[0], 'name')) {
            throw new \Exception(
                sprintf(
                    'Class %s root node has no property name',
                    $className
                )
            );
        }

        $namespace = 'UnitTests\\' . $nodes[0]->name->toString();
        $className = $className . 'Test';

        return $this->formTestClass(
            $namespace,
            $className,
            $extends,
            $methods
        );
    }

    /**
     * @param string $namespace
     * @param string $className
     * @param string $extends
     * @param array $methods
     * @return Node\Stmt\Namespace_
     */
    protected function formTestClass(
        string $namespace,
        string $className,
        string $extends,
        array $methods
    ) : Node\Stmt\Namespace_
    {
        $uses = ltrim($extends, '\\');
        preg_match('#[^\\x5c]*\\x5c([^\\x5c]+)$#', $extends, $matches);
        $extendsAlias = $matches[1];

        return new Node\Stmt\Namespace_(
            new Node\Name($namespace),
            [
                new Node\Stmt\Use_(
                    [
                        new Node\Stmt\UseUse(
                            new Node\Name($uses),
                            null,
                            Node\Stmt\Use_::TYPE_NORMAL
                        )
                    ]
                ),
                new Class_(
                    $className,
                    [
                        'stmts' => array_merge(
                            [
                                $this->formSetupMethod()
                            ],
                            $this->formTestMethods($methods)
                        ),
                        'extends' => new Node\Name($extendsAlias)
                    ]
                )
            ]
        );
    }

    /**
     * @return Node\Stmt\ClassMethod
     */
    protected function formSetupMethod() : Node\Stmt\ClassMethod
    {
        return new Node\Stmt\ClassMethod('setUp');
    }

    /**
     * @param string[] $methods
     * @return Node\Stmt\ClassMethod[]
     */
    protected function formTestMethods(array $methods) : array
    {
        return array_map(function (string $methodName) {
            return new Node\Stmt\ClassMethod(
                'test' . ucfirst($methodName),
                [
                    'stmts' => [
                        new Node\Expr\MethodCall(
                            new Node\Expr\Variable('this'),
                            'markTestIncomplete'
                        )
                    ]
                ]
            );
        }, $methods);
    }

    /**
     * @param Node[] $nodes
     * @param array $methods
     * @return void
     */
    protected function collectMethodNames(array $nodes, array &$methods)
    {
        if (empty($nodes)) {
            return;
        }

        $length = count($nodes);
        for ($i = 0; $i < $length; $i++) {
            if ($nodes[$i] instanceof Node\Stmt\ClassMethod) {
                if (in_array($nodes[$i]->flags, $this->forAccessModifiers)
                    && $nodes[$i]->name !== '__construct') {
                    $methods[] = $nodes[$i]->name;
                }
            } else {
                if (! property_exists($nodes[$i], 'stmts')) {
                    continue;
                }

                $this->collectMethodNames($nodes[$i]->stmts, $methods);
            }
        }
    }
}
