<?php

declare(strict_types=1);

namespace Shared\Tests\Behat;

use Behat\Behat\Context\Context;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;

final readonly class FeatureContext implements Context
{
    public function __construct(private KernelInterface $kernel)
    {
    }

    /** @Given /^empty database$/ */
    public function emptyDatabase(): void
    {
        $this->executeSymfonyCommand('database:postgres:truncate');
    }

    /** @Given /^database with fixtures$/ */
    public function databaseWithFixtures(): void
    {
        /*
        $this->emptyDatabase();

        $phinx = new PhinxApplication();
        $command = $phinx->find('seed:run');

        if (0 !== $command->run(new ArrayInput([]), new NullOutput())) {
            throw new ContextException();
        }
        */
    }

    private function executeSymfonyCommand(string $cmd): void
    {
        $app = new Application($this->kernel);
        $app->setAutoExit(false);

        $arg = new ArrayInput(
            [
                'command' => $cmd,
            ],
        );

        $app->run($arg);
    }
}
