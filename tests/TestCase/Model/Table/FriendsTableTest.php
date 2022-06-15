<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\FriendsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\FriendsTable Test Case
 */
class FriendsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\FriendsTable
     */
    public $Friends;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Friends'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Friends') ? [] : ['className' => FriendsTable::class];
        $this->Friends = TableRegistry::getTableLocator()->get('Friends', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Friends);

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
