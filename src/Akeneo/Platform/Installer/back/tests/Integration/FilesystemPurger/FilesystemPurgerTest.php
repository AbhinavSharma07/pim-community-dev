<?php

declare(strict_types=1);

namespace Akeneo\Platform\Installer\Test\Integration\FilesystemPurger;

use Akeneo\Platform\Installer\Domain\Service\FilesystemPurgerInterface;
use Akeneo\Test\Integration\Configuration;
use Akeneo\Test\Integration\TestCase;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use Webmozart\Assert\Assert;

/**
 * @copyright 2023 Akeneo SAS (https://www.akeneo.com)
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
class FilesystemPurgerTest extends TestCase
{
    /**
     * @test
     */
    public function it_remove_all_files_in_the_filesystem()
    {
        $filesystem = $this->getFilesystem();
        $filesystem->createDirectory('test');
        $filesystem->write('test/file_test.txt', 'file content');

        $this->assertFilesystemNotEmpty($filesystem);
        $this->getPurger()->execute($filesystem);
        $this->assertFilesystemEmpty($filesystem);
    }

    protected function getConfiguration(): Configuration
    {
        return $this->catalog->useTechnicalCatalog();
    }

    private function getFilesystem(): FilesystemOperator
    {
        return new Filesystem(new InMemoryFilesystemAdapter());
    }

    private function getPurger(): FilesystemPurgerInterface
    {
        return $this->get('Akeneo\Platform\Installer\Infrastructure\FilesystemPurger\FilesystemPurger');
    }

    private function assertFilesystemEmpty(FilesystemOperator $filesystem): void
    {
        Assert::isEmpty($filesystem->listContents('.')->toArray());
    }

    private function assertFilesystemNotEmpty(FilesystemOperator $filesystem): void
    {
        Assert::notEmpty($filesystem->listContents('.')->toArray());
    }
}
