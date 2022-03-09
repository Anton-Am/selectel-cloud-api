<?php

namespace Tests;

use Exception;

/**
 * Class DirectoryCest
 *
 * @package Tests
 */
class ObjectCest
{
    /**
     * @param UnitTester $I
     * @throws Exception
     */
    public function directoryCreatedAndRemoved(UnitTester $I): void
    {
        $directory = '/temp';
        $I->assertTrue($I->getClient()->directory($directory)->create(), 'I created directory');
        $I->assertTrue($I->getClient()->directory($directory)->delete(), 'I deleted directory');
    }


    /**
     * @param UnitTester $I
     * @throws Exception
     */
    public function directoryCreatedWithFiles(UnitTester $I): void
    {
        $I->uploadTestData();

        $file = $I->getTestFile();

        $I->assertIsArray($I->getClient()->directory(dirname($file))->files());

        $I->assertTrue($I->getClient()->file($file)->delete());
        $I->assertTrue($I->getClient()->directory(dirname($file))->delete());

        $I->clearTestData();
    }

    /**
     * @param UnitTester $I
     * @throws Exception
     */
    public function uploadDir(UnitTester $I): void
    {
        $I->createTestData();
        $file = $I->getTestFile();
        $directory = dirname($file, 2);

        $I->assertTrue($I->getClient()->directory($directory)->upload());
        $I->assertIsArray($I->getClient()->directory($directory)->files());
    }

    /**
     * @param UnitTester $I
     * @throws Exception
     */
    public function downloadDir(UnitTester $I): void
    {
        $I->uploadTestData();
        $I->clearTestData();
        $file = $I->getTestFile();
        $directory = dirname($file, 2);

        $I->assertTrue($I->getClient()->directory($directory)->download());
        $I->assertDirectoryExists(dirname($file));
        $I->assertFileExists($file);
    }
}
