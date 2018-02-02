<?php
namespace Germania\Databases;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DatabasesServiceProvider implements ServiceProviderInterface
{

    /**
     * Values may be either:
     *
     *   - 2: PDO::ERRMODE_EXCEPTION
     *   - 1: PDO::ERRMODE_WARNING
     *   - 0: PDO::ERRMODE_SILENT
     *
     * @var int
     */
    public $pdo_error_mode = 2;


    /**
     * @param int $pdo_error_mode Default: PDO::ERRMODE_EXCEPTION
     */
    public function __construct( $pdo_error_mode = \PDO::ERRMODE_EXCEPTION )
    {
        if (is_null($pdo_error_mode)):
            $pdo_error_mode = \PDO::ERRMODE_EXCEPTION;
        endif;
        $this->pdo_error_mode = $pdo_error_mode;
    }


    /**
     * @param Container $dic Pimple Containter instance
     *
     * @implements ServiceProviderInterface
     */
    public function register(Container $dic)
    {

        /**
         * @return int
         */
        $dic['PDO.ErrorMode'] = function($dic) {
            return $this->pdo_error_mode;
        };


        /**
         * @return array
         */
        $dic['PDO.Options'] = function( $dic ) {
            return array(
                \PDO::ATTR_ERRMODE => $dic['PDO.ErrorMode']
            );
        };


        /**
         * @return Callable
         */
        $dic['PDO.Factory'] = $dic->protect(function( $db ) use ($dic) {

            // Parameter check
            if ($db instanceOf \StdClass):
                $db = (array) $db;
            elseif (!is_array($db) and !$db instanceOf \ArrayAccess):
                throw new \InvalidArgumentException("Array or StdClass or ArrayAccess expected");
            endif;

            // Setup
            $pdo_options = $dic['PDO.Options'];
            return new \PDO( $db['dsn'], $db['user'], $db['pass'], $pdo_options);
        });
    }

}


