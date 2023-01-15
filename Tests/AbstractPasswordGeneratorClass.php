<?php

require_once('PasswordGenerator.php');

class AbstractPasswordGeneratorClass extends PasswordGenerator
{
    const TEST_UPPERCASE = 'UPPERCASE';
    const TEST_LOWERCASE = 'LOWERCASE';
    const TEST_NUMBERS = 'NUMBERS';
    const TEST_SYMBOLS = 'SYMBOLS';
    const TEST_LENGTH = 'LENGTH';

    public function __construct()
    {
        $this
            ->setOption(self::TEST_LENGTH, array('type' => 'integer', 'default' => 10))
            ->setOption(self::TEST_UPPERCASE, array('type' => 'boolean', 'default' => true))
            ->setOption(self::TEST_LOWERCASE, array('type' => 'boolean', 'default' => true))
            ->setOption(self::TEST_NUMBERS, array('type' => 'boolean', 'default' => true))
            ->setOption(self::TEST_SYMBOLS, array('type' => 'boolean', 'default' => true))
            ->setParameter(self::UPPER_CASE, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ')
            ->setParameter(self::LOWER_CASE, 'abcdefghijklmnopqrstuvwxyz')
            ->setParameter(self::NUMBERS, '0123456789')
            ->setParameter(self::SYMBOLS, '@%!?*^&')
        ;
    }
}
