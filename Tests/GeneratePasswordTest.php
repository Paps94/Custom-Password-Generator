<?php
require_once('AbstractPasswordGeneratorClass.php');

class PasswordGeneratorTest extends \PHPUnit\Framework\TestCase
{
    private $passwordGenerator;

    public function setup(): void
    {
        $this->passwordGenerator = new AbstractPasswordGeneratorClass();
    }

    public function generatePasswordsProvider()
    {
        return array(
            array(''),
            array(null),
            array(-1),
            array(0.1),
            array(true),
        );
    }

    /**
     * @return array
     */
    public function lengthProvider()
    {
        return array(
            array(1),
            array(4),
            array(8),
            array(16),
        );
    }

    public function optionProvider()
    {
        return array(
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE),
            array(AbstractPasswordGeneratorClass::TEST_LOWERCASE),
            array(AbstractPasswordGeneratorClass::TEST_NUMBERS),
            array(AbstractPasswordGeneratorClass::TEST_SYMBOLS),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH),
        );
    }

    public function testGetOptionValueDefault(): void
    {
        $this->assertSame(true, $this->passwordGenerator->getOptionValue($this->passwordGenerator::TEST_UPPERCASE));
        $this->assertSame(true, $this->passwordGenerator->getOptionValue($this->passwordGenerator::TEST_LOWERCASE));
        $this->assertSame(true, $this->passwordGenerator->getOptionValue($this->passwordGenerator::TEST_NUMBERS));
        $this->assertSame(true, $this->passwordGenerator->getOptionValue($this->passwordGenerator::TEST_SYMBOLS));
        $this->assertSame(10, $this->passwordGenerator->getOptionValue($this->passwordGenerator::TEST_LENGTH));
    }

    /**
     * @dataProvider setOptionValueProvider
     *
     * @param $option
     * @param $value
     */
    public function testSetOptionValue($option, $value): void
    {
        $this->passwordGenerator->setOptionValue($option, $value);
        $this->assertSame($this->passwordGenerator->getOptionValue($option), $value);
    }

    /**
     * @return array
     */
    public function setOptionValueProvider()
    {
        return array(
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, true),
            array(AbstractPasswordGeneratorClass::TEST_LOWERCASE, true),
            array(AbstractPasswordGeneratorClass::TEST_NUMBERS, true),
            array(AbstractPasswordGeneratorClass::TEST_SYMBOLS, true),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH, 0),
        );
    }

    /**
     * @dataProvider setOptionExceptionProvider
     *
     * @param $option
     * @param $value
     */
    public function testSetExceptionOption($option, $value): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->passwordGenerator->setOptionValue($option, $value);
    }

    /**
     * @return array
     */
    public function setOptionExceptionProvider()
    {
        return array(
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, 99),
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, null),
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, 'test'),

            array(AbstractPasswordGeneratorClass::TEST_LENGTH, true),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH, null),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH, 'test'),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH, -101),
        );
    }

    public function testUnknownSetOption(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->passwordGenerator->setOption('unknown', array());
    }

    public function testUnknownSetOptionValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->passwordGenerator->setOptionValue('unknown', true);
    }

    public function testUnknownOptionValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->passwordGenerator->getOptionValue('unknown');
    }

    public function testUnknownParameter(): void
    {
        $this->assertNull($this->passwordGenerator->getParameter('unknown'));
    }

    /**
     * @dataProvider parameterProvider
     *
     * @param $parameter
     * @param $value
     */
    public function testSetParameter($parameter, $value): void
    {
        $this->passwordGenerator->setParameter($parameter, $value);
        $this->assertSame($value, $this->passwordGenerator->getParameter($parameter));
    }

    public function parameterProvider()
    {
        return array(
            array('a', 1),
            array('ab', null),
            array('test', true),
            array('test2', 'value'),
        );
    }

    /**
     * @dataProvider validateValueProvider
     *
     * @param $option
     * @param $value
     * @param $return
     */
    public function testValidateValue($option, $value, $return): void
    {
        if (!$return) {
            $this->expectException('\InvalidArgumentException');
        }

        $this->passwordGenerator->setOptionValue($option, $value);

        if ($return) {
            $this->assertSame($value, $this->passwordGenerator->getOptionValue($option));
        }
    }

    /**
     * @return array
     */
    public function validateValueProvider()
    {
        return array(
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, true, true),
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, 1, false),
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, null, false),

            array(AbstractPasswordGeneratorClass::TEST_LENGTH, 0, true),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH, 100, true),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH, -100, false),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH, true, false),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH, null, false),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH, '', false),

            array('fail', '', false),
        );
    }

    
    /**
     * @dataProvider lengthProvider
     *
     * @param $length
     */
    public function testLength($length): void
    {
        $this->passwordGenerator->setLength($length);
        $this->assertSame($this->passwordGenerator->getLength(), $length);
    }

        /**
     * @dataProvider invalidLengthProvider
     *
     * @param $length
     */
    public function testInvalidLength($length): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->passwordGenerator->setLength($length);
    }

    
    /**
     * @return array
     */
    public function invalidLengthProvider()
    {
        return array(
            array(-1),
            array('4'),
            array(false),
        );
    }

    /**
     * @dataProvider lengthProvider
     *
     * @param $length
     */
    public function testGeneratePassword($length): void
    {
        $this->passwordGenerator
            ->setOptionValue($this->passwordGenerator::TEST_UPPERCASE, true)
            ->setOptionValue($this->passwordGenerator::TEST_LOWERCASE, true)
            ->setOptionValue($this->passwordGenerator::TEST_NUMBERS, true)
            ->setOptionValue($this->passwordGenerator::TEST_SYMBOLS, true)
            ->setLength($length);

        $this->assertSame(\strlen($this->passwordGenerator->generatePassword()), $length);
    }



    /**
     * @dataProvider      lengthExceptionProvider
     *
     * @param $param
     */
    public function testLengthException($param): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->passwordGenerator->setLength($param);
    }

    public function lengthExceptionProvider()
    {
        return array(
            array('a'),
            array(false),
            array(null),
            array(-1),
        );
    }



    /**
     * @dataProvider validOptionProvider
     */
    public function testValidOption($option, $valid): void
    {

        $this->passwordGenerator
        ->setOptionValue($this->passwordGenerator::TEST_UPPERCASE, true)
        ->setOptionValue($this->passwordGenerator::TEST_LOWERCASE, true)
        ->setOptionValue($this->passwordGenerator::TEST_NUMBERS, true)
        ->setOptionValue($this->passwordGenerator::TEST_SYMBOLS, true);

        $this->assertSame($valid, $this->passwordGenerator->validOption($option));
    }

    public function validOptionProvider()
    {
        return array( 
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, true),
            array(AbstractPasswordGeneratorClass::TEST_LOWERCASE, true),
            array(AbstractPasswordGeneratorClass::TEST_NUMBERS, true),
            array(AbstractPasswordGeneratorClass::TEST_SYMBOLS, true),
            array(null, false),
            array('', false),
            array(AbstractPasswordGeneratorClass::TEST_LENGTH, false),
        );
    }

    /**
     *
     */
    public function testCharacterListException(): void
    {
        $this->passwordGenerator
            ->setOptionValue($this->passwordGenerator::TEST_UPPERCASE, false)
            ->setOptionValue($this->passwordGenerator::TEST_LOWERCASE, false)
            ->setOptionValue($this->passwordGenerator::TEST_NUMBERS, false)
            ->setOptionValue($this->passwordGenerator::TEST_SYMBOLS, false);

        $this->expectException('\Exception');
        $this->passwordGenerator->getCharacterList();
    }

        /**
     *
     */
    public function testCharacterList(): void
    {
        $this->assertSame(
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789@%!?*^&', 
            $this->passwordGenerator->getCharacterList()
        );
    }

    /**
     * @dataProvider countProvider
     *
     * @param $option
     * @param $count
     */
    public function testCount($option, $count): void
    {
        $this->passwordGenerator
        ->setOptionValue($this->passwordGenerator::TEST_UPPERCASE, true)
        ->setOptionValue($this->passwordGenerator::TEST_LOWERCASE, true)
        ->setOptionValue($this->passwordGenerator::TEST_NUMBERS, true)
        ->setOptionValue($this->passwordGenerator::TEST_SYMBOLS, true);

        $this->passwordGenerator->setCount($option, $count);
        $this->assertSame($count, $this->passwordGenerator->getCount($option));
    }

    public function countProvider()
    {
        return array(
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, null),
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, 1),
            array(AbstractPasswordGeneratorClass::TEST_UPPERCASE, 2),
            array(AbstractPasswordGeneratorClass::TEST_LOWERCASE, null),
            array(AbstractPasswordGeneratorClass::TEST_LOWERCASE, 1),
            array(AbstractPasswordGeneratorClass::TEST_LOWERCASE, 2),
            array(AbstractPasswordGeneratorClass::TEST_NUMBERS, null),
            array(AbstractPasswordGeneratorClass::TEST_NUMBERS, 1),
            array(AbstractPasswordGeneratorClass::TEST_NUMBERS, 2),
            array(AbstractPasswordGeneratorClass::TEST_SYMBOLS, null),
            array(AbstractPasswordGeneratorClass::TEST_SYMBOLS, 1),
            array(AbstractPasswordGeneratorClass::TEST_SYMBOLS, 2),
        );
    }

    public function testValidLimits(): void
    {
        $this->passwordGenerator
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_UPPERCASE, true)
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_LOWERCASE, true)
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_NUMBERS, true)
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_SYMBOLS, true)
            ->setLength(4)
            ->setCount(AbstractPasswordGeneratorClass::TEST_UPPERCASE, 1)
            ->setCount(AbstractPasswordGeneratorClass::TEST_LOWERCASE, 1)
            ->setCount(AbstractPasswordGeneratorClass::TEST_NUMBERS, 1)
            ->setCount(AbstractPasswordGeneratorClass::TEST_SYMBOLS, 1)
        ;

        $this->assertTrue($this->passwordGenerator->checkLimits());
    }

    public function testValidLimitsFalse(): void
    {
        $this->passwordGenerator
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_UPPERCASE, true)
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_LOWERCASE, true)
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_NUMBERS, true)
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_SYMBOLS, true)
            ->setLength(3)
            ->setCount(AbstractPasswordGeneratorClass::TEST_UPPERCASE, 1)
            ->setCount(AbstractPasswordGeneratorClass::TEST_LOWERCASE, 1)
            ->setCount(AbstractPasswordGeneratorClass::TEST_NUMBERS, 1)
            ->setCount(AbstractPasswordGeneratorClass::TEST_SYMBOLS, 1)
        ;

        $this->assertFalse($this->passwordGenerator->checkLimits());
    }

    public function testvalidLimitsFalse2(): void
    {
        $this->passwordGenerator
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_UPPERCASE, true)
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_LOWERCASE, true)
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_NUMBERS, true)
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_SYMBOLS, true)
            ->setLength(3)
            ->setCount(AbstractPasswordGeneratorClass::TEST_UPPERCASE, 4)
        ;

        $this->assertFalse($this->passwordGenerator->checkLimits());
    }

    public function testGeneratePasswordException(): void
    {
        $this->passwordGenerator
            ->setOptionValue(AbstractPasswordGeneratorClass::TEST_UPPERCASE, true)
            ->setLength(4)
            ->setCount(AbstractPasswordGeneratorClass::TEST_UPPERCASE, 5)
        ;

        $this->expectException('\LengthException');
        $this->passwordGenerator->generatePassword();
    }



    /**
     * @dataProvider validatePasswordProvider
     *
     * @param $password
     * @param $option
     * @param $min
     * @param $max
     * @param $valid
     */
    public function testValidatePassword($password, $option, $count, $valid): void
    {
        $this->passwordGenerator
        ->setOptionValue(AbstractPasswordGeneratorClass::TEST_UPPERCASE, true)
        ->setOptionValue(AbstractPasswordGeneratorClass::TEST_LOWERCASE, true)
        ->setOptionValue(AbstractPasswordGeneratorClass::TEST_NUMBERS, true)
        ->setOptionValue(AbstractPasswordGeneratorClass::TEST_SYMBOLS, true)
        ->setCount($option, $count);
        
        $this->assertSame($valid, $this->passwordGenerator->validatePassword($password));
    }

    public function validatePasswordProvider()
    {
        return array(
            array('QWERTY', AbstractPasswordGeneratorClass::TEST_UPPERCASE, 2, false),
            array('QWerty', AbstractPasswordGeneratorClass::TEST_UPPERCASE, 2, true),
            array('QWerty', AbstractPasswordGeneratorClass::TEST_LOWERCASE, 2, false),
            array('qwERTY', AbstractPasswordGeneratorClass::TEST_LOWERCASE, 2, true),
            array('qwerty1234!', AbstractPasswordGeneratorClass::TEST_NUMBERS, 2, false),
            array('!qwert12', AbstractPasswordGeneratorClass::TEST_NUMBERS, 2, true),
            array('qwERTY^*%', AbstractPasswordGeneratorClass::TEST_SYMBOLS, 2, false),
            array('qwERTY!@', AbstractPasswordGeneratorClass::TEST_SYMBOLS, 2, true),
        );
    }

    /**
     * @return array
     */
    public function minLengthProvider()
    {
        return array(
            array(8),
            array(16),
        );
    }

    /**
     * @expectException \InvalidArgumentException
     *
     * @param $method
     * @param $option
     */
    public function testCountOptionException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->passwordGenerator->setCount('INVALID_OPTION', 1);
    }

    public function rangeProvider()
    {
        return array(
            array(1, 100),
            array(10, 20),
            array(0, 5),
            array(1, 2),
        );
    }

    /**
     * @dataProvider rangeProvider
     *
     * @param int $min
     * @param int $max
     */
    public function testRandomGenerator($min, $max): void
    {
        $randomInteger = $this->passwordGenerator->randomInteger($min, $max);
        $this->assertGreaterThanOrEqual($min, $randomInteger);
        $this->assertLessThanOrEqual($max, $randomInteger);
    }
}
