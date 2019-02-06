<?php
namespace tests;

use Germania\Databases\DatabasesServiceProvider;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DatabasesServiceProviderTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @dataProvider provideErrorModes
     */
    public function testInstantiation( $error_mode, $expected_error_mode ) {

        $sut = new DatabasesServiceProvider( $error_mode );
        $this->assertInstanceOf( ServiceProviderInterface::class, $sut);

        $dic = new Container;
        $dic->register( $sut );

        $this->assertArrayHasKey( "PDO.Options",  $dic);
        $this->assertArrayHasKey( "PDO.ErrorMode",  $dic);
        $this->assertArrayHasKey( "PDO.Factory",  $dic);
    }


    /**
     * @dataProvider provideErrorModes
     */
    public function testPdoErrorModes( $error_mode, $expected_error_mode ) {
        $sut = new DatabasesServiceProvider( $error_mode );

        $dic = new Container;
        $dic->register( $sut );

        $this->assertEquals( $expected_error_mode, $dic['PDO.ErrorMode']);
    }


    /**
     * @dataProvider provideErrorModes
     */
    public function testPdoOptions( $error_mode, $expected_error_mode ) {
        $sut = new DatabasesServiceProvider( $error_mode );

        $dic = new Container;
        $dic->register( $sut );

        $options = $dic['PDO.Options'];
        $this->assertArrayHasKey(\PDO::ATTR_ERRMODE, $options);
        $this->assertEquals($expected_error_mode, $options[ \PDO::ATTR_ERRMODE ]);
    }


    /**
     * @dataProvider provideDatabaseCredentials
     */
    public function testPdoFactory( $credentials, $expected_exception_class ) {
        $dic = new Container;
        $dic->register( new DatabasesServiceProvider );

        $factory = $dic['PDO.Factory'];
        $this->assertTrue( is_callable($factory));

        if ($expected_exception_class):
            $this->expectException( $expected_exception_class);
        endif;

        $pdo = $factory(  $credentials );
        $this->assertInstanceOf( \PDO::class, $pdo);
    }


    public function provideErrorModes() {
        return array(
            [ null, \PDO::ERRMODE_EXCEPTION ],
            [ \PDO::ERRMODE_SILENT,  \PDO::ERRMODE_SILENT ],
            [ \PDO::ERRMODE_WARNING, \PDO::ERRMODE_WARNING ],
            [ \PDO::ERRMODE_SILENT,  \PDO::ERRMODE_SILENT ]
        );
    }


    public function provideDatabaseCredentials() {

        $credentials = [
            'dsn' => $GLOBALS['DB_DSN'],
            'user' => $GLOBALS['DB_USER'],
            'pass' => $GLOBALS['DB_PASSWD']
        ];

        return array(
            // These will succeed
            [ $credentials, null],
            [ (object) $credentials, null ],
            [ new \ArrayObject($credentials), null ],

            // But these not:
            [ "Invalid", \InvalidArgumentException::class ],
            [ 9, \InvalidArgumentException::class ]
        );
    }
}
