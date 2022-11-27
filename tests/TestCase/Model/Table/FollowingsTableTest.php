<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FollowingsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FollowingsTable Test Case
 */
class FollowingsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FollowingsTable
     */
    public $Followings;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Followings'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Followings') ? [] : ['className' => FollowingsTable::class];
        $this->Followings = TableRegistry::getTableLocator()->get('Followings', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Followings);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
