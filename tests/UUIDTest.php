<?php
/*
 * This file is part of the Crypto package.
 *
 * (c) Unit6 <team@unit6websites.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Unit6\Crypto\UUID;

class UUIDTest extends PHPUnit_Framework_TestCase
{
    const UUID_REGEX = '/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/';

    public function testStaticGeneration()
    {
        $uuid = UUID::generate(1);
        $this->assertInstanceOf('Unit6\Crypto\UUID', $uuid);

        $uuid = UUID::generate(3, 'example.com', UUID::NS_DNS);
        $this->assertInstanceOf('Unit6\Crypto\UUID', $uuid);

        $uuid = UUID::generate(4);
        $this->assertInstanceOf('Unit6\Crypto\UUID', $uuid);

        $uuid = UUID::generate(5, 'example.com', UUID::NS_DNS);
        $this->assertInstanceOf('Unit6\Crypto\UUID', $uuid);
    }

    public function testGenerationOfValidUUID()
    {
        $uuid = UUID::generate(1);
        $this->assertRegExp(self::UUID_REGEX, $uuid->getValue());

        $uuid = UUID::generate(3, 'example.com', UUID::NS_DNS);
        $this->assertRegExp(self::UUID_REGEX, $uuid->getValue());

        $uuid = UUID::generate(4);
        $this->assertRegExp(self::UUID_REGEX, $uuid->getValue());

        $uuid = UUID::generate(5, 'example.com', UUID::NS_DNS);
        $this->assertRegExp(self::UUID_REGEX, $uuid->getValue());
    }

    public function testCorrectVersionUUID()
    {
        $uuidOne = UUID::generate(1);
        $this->assertEquals(1, $uuidOne->getVersion());

        $uuidThree = UUID::generate(3,'example.com', UUID::NS_DNS);;
        $this->assertEquals(3, $uuidThree->getVersion());

        $uuidFour = UUID::generate(4);
        $this->assertEquals(4, $uuidFour->getVersion());

        $uuidFive = UUID::generate(5,'example.com', UUID::NS_DNS);;
        $this->assertEquals(5, $uuidFive->getVersion());
    }

    public function testCorrectVariantUUID()
    {
        $uuidOne = UUID::generate(1);
        $this->assertEquals(1, $uuidOne->getVariant());

        $uuidThree = UUID::generate(3,'example.com', UUID::NS_DNS);;
        $this->assertEquals(1, $uuidThree->getVariant());

        $uuidFour = UUID::generate(4);
        $this->assertEquals(1, $uuidFour->getVariant());

        $uuidFive = UUID::generate(5,'example.com', UUID::NS_DNS);;
        $this->assertEquals(1, $uuidFive->getVariant());
    }

    public function testCorrectVersionOfImportedUUID()
    {
        $uuidOne = UUID::generate(1);
        $importedOne = UUID::import((string) $uuidOne);
        $this->assertEquals($uuidOne->getVersion(), $importedOne->getVersion());

        $uuidThree = UUID::generate(3,'example.com', UUID::NS_DNS);;
        $importedThree = UUID::import((string) $uuidThree);
        $this->assertEquals($uuidThree->getVersion(), $importedThree->getVersion());

        $uuidFour = UUID::generate(4);
        $importedFour = UUID::import((string) $uuidFour);
        $this->assertEquals($uuidFour->getVersion(), $importedFour->getVersion());

        $uuidFive = UUID::generate(5,'example.com', UUID::NS_DNS);;
        $importedFive = UUID::import((string) $uuidFive);
        $this->assertEquals($uuidFive->getVersion(), $importedFive->getVersion());
    }

    public function testCorrectNodeOfGeneratedUUID()
    {
        $macAdress = macAddress();
        $uuidThree = UUID::generate(1, $macAdress);
        $this->assertEquals(strtolower(str_replace(':', '', $macAdress)), $uuidThree->getNode());

        $uuidThree = UUID::generate(3, $macAdress, UUID::NS_DNS);
        $this->assertNull($uuidThree->getNode());

        $uuidThree = UUID::generate(4, $macAdress);
        $this->assertNull($uuidThree->getNode());

        $uuidThree = UUID::generate(5, $macAdress, UUID::NS_DNS);
        $this->assertNull($uuidThree->getNode());
    }

    public function testCorrectTimeOfImportedUUID()
    {
        $uuidOne = UUID::generate(1);
        $importedOne = UUID::import((string) $uuidOne);
        $this->assertEquals($uuidOne->getTime(), $importedOne->getTime());

        $uuidThree = UUID::generate(3,'example.com', UUID::NS_DNS);;
        $importedThree = UUID::import((string) $uuidThree);
        $this->assertEmpty($importedThree->getTime());

        $uuidFour = UUID::generate(4);
        $importedFour = UUID::import((string) $uuidFour);
        $this->assertEmpty($importedFour->getTime());

        $uuidFive = UUID::generate(5,'example.com', UUID::NS_DNS);;
        $importedFive = UUID::import((string) $uuidFive);
        $this->assertEmpty($importedFive->getTime());
    }

    public function testUUIDComparison()
    {
        $uuid1 = (string) UUID::generate(1);
        $uuid2 = (string) UUID::generate(1);

        $this->assertTrue(UUID::compare($uuid1, $uuid1));
        $this->assertFalse(UUID::compare($uuid1, $uuid2));
    }
}
