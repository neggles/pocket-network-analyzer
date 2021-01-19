<?php
defined('BASEPATH') or exit('No direct script access allowed');

$active_group = 'default';

$query_builder = true;

$db['default'] = array(
    'dsn'   => '',
    'hostname' => '',
    'username' => '',
    'password' => '',
    'database' => './application/database/pfi.db',
    'dbdriver' => 'sqlite3',
    'dbprefix' => '',
    'pconnect' => false,
    'db_debug' => false,
    'cache_on' => true,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => false,
    'compress' => false,
    'stricton' => false,
    'failover' => array(),
    'save_queries' => true
);

$db['macvendors'] = array(
    'dsn'   => '',
    'hostname' => '',
    'username' => '',
    'password' => '',
    'database' => './application/database/macvendors.db',
    'dbdriver' => 'sqlite3',
    'dbprefix' => '',
    'pconnect' => false,
    'db_debug' => false,
    'cache_on' => true,
    'cachedir' => '',
    'char_set' => 'utf8',
    'dbcollat' => 'utf8_general_ci',
    'swap_pre' => '',
    'encrypt' => false,
    'compress' => false,
    'stricton' => false,
    'failover' => array(),
    'save_queries' => true
);
