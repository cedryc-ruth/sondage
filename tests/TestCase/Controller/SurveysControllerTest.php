<?php
namespace App\Test\TestCase\Controller;

use App\Controller\SurveysController;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Controller\SurveysController Test Case
 */
class SurveysControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.surveys',
        'app.users',
        'app.responses',
       /* 'app.responses_surveys'   */
    ];

    public function initialize() {
        parent::initialize();

        $this->loadComponent('Auth');
    }
    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testIsAuthorized()
    {
        $sc = new SurveysController();
        
        $this->assertFalse($sc->isAuthorized(null));
    }
    
    /**
     * Test view method
     *
     * @return void
     */
    public function testView()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
