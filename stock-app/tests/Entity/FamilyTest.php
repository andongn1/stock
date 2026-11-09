<?php

namespace App\Tests\Entity;

use App\Entity\Family;
use PHPUnit\Framework\TestCase;

class FamilyTest extends TestCase
{
    public function testFamilyEntity(): void
    {
        $family = new Family();
        $family->setName('Test Family');
        
        $this->assertEquals('Test Family', $family->getName());
        $this->assertNotNull($family->getCreatedAt());
        $this->assertNotNull($family->getUpdatedAt());
    }
}
