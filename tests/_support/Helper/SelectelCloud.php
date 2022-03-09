<?php

namespace Tests\Helper;

use Codeception\Module\Filesystem;
use AntonAm\Selectel\Cloud\Manager;

class SelectelCloud extends Filesystem
{
    /** @var Manager */
    private $client;
    protected $requiredFields = ['name', 'key', 'secret'];
    private $testDir = '/test-dir';
    private $testFile = '/composer.json';

    private function initClient(): void
    {
        if (empty($this->client)) {
            $this->client = new Manager($this->config['key'], $this->config['secret'], $this->config['name']);
        }
    }

    public function getClient(): Manager
    {
        $this->initClient();
        return $this->client;
    }

    public function getTestDirectory(): string
    {
        return realpath(__DIR__ . '/../../_output') . $this->testDir;
    }

    public function getTestFile(): string
    {
        return $this->getTestDirectory() . $this->testFile;
    }

    public function getComposerFile()
    {
        return realpath(__DIR__ . '/../../../' . $this->testFile);
    }

    public function createTestData(): void
    {
        $composerFrom = $this->getComposerFile();
        $to = $this->getTestFile();
        $toDir = $this->getTestDirectory();

        if (!is_dir($toDir)) {
            mkdir($toDir);
            $this->assertDirectoryExists($toDir);
        }

        if (!file_exists($to)) {
            copy($composerFrom, $to);
            $this->assertFileExists($to);

            copy($composerFrom, $to . '2');
            $this->assertFileExists($to . '2');
        }
    }

    public function uploadTestData(): void
    {
        $this->createTestData();
        $file = $this->getTestFile();

        $this->assertFileExists($file);
        $this->getClient()->file($file)->setFileData($file)->create();
        $this->assertTrue($this->getClient()->file($file)->exist());
    }

    public function clearTestData(): void
    {
        $directory = $this->getTestDirectory();
        if (is_dir($directory)) {
            $this->deleteDir($directory);
        }

        $this->assertDirectoryDoesNotExist($directory);
    }
}