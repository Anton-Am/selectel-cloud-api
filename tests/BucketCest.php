<?php

namespace Tests;

use Exception;

/**
 * Class BucketCest
 *
 * @package Tests
 */
class BucketCest
{
    /**
     * @param UnitTester $I
     * @throws Exception
     */
    public function exists(UnitTester $I): void
    {
        $I->assertTrue($I->getClient()->bucket()->exist());
    }

    /**
     * @param UnitTester $I
     * @throws Exception
     */
    public function list(UnitTester $I): void
    {
        $I->assertIsArray($I->getClient()->bucket()->list());
    }

    /**
     * @param UnitTester $I
     * @throws Exception
     */
    public function downloaded(UnitTester $I): void
    {
        // Upload file
        $file = $I->getComposerFile();

        $I->getClient()->file($file)->setFileData($file)->create();
        $I->assertTrue($I->getClient()->file($file)->exist());

        // Upload dir
        $directory = realpath(__DIR__ . '/_output');
        $I->deleteDir($I->getTestDirectory());
        $I->assertDirectoryDoesNotExist($I->getTestDirectory());

        $I->getClient()->bucket()->download($directory);
        $I->assertFileExists($directory . $file);
    }


    /**
     * @param UnitTester $I
     * @throws Exception
     */
    public function fileListGot(UnitTester $I): void
    {
        $I->assertIsArray($I->getClient()->bucket()->files());
    }


    /**
     * @param UnitTester $I
     * @throws Exception
     */
    public function location(UnitTester $I): void
    {
        $I->assertEquals('ru-1', $I->getClient()->bucket()->location());
    }


    /**
     * @param UnitTester $I
     * @throws Exception
     */
    public function notExists(UnitTester $I): void
    {
        $I->assertFalse($I->getClient()->bucket()->set('test2')->exist());
    }
}
