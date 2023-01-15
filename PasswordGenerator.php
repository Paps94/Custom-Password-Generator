<?php
use Krystal\PasswordGenerator\CustomExceptions\CharactersNotFoundException;
use Krystal\PasswordGenerator\CustomExceptions\ImpossibleLimitsException;
use Krystal\PasswordGenerator\CustomExceptions\ImpossiblePasswordLengthException;
use Krystal\PasswordGenerator\CustomExceptions\InvalidOptionException;
use Krystal\PasswordGenerator\CustomExceptions\InvalidOptionTypeException;

/**
 * Class PasswordGenerator
 */
class PasswordGenerator
{
    /**
     * Set our type constants
     */
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_STRING = 'string';


    /**
     * Set our option constants
     */
    const UPPER_CASE = 'UPPERCASE';
    const LOWER_CASE = 'LOWERCASE';
    const NUMBERS = 'NUMBERS';
    const SYMBOLS = 'SYMBOLS';
    const LENGTH = 'LENGTH';

    //Holds the number of characters each group should contain based on details entered by the end user
    private $counts = array();

    //Holds the groups (Uppercase, Lowercase, Numbers e.t.c) which should be inculded in the password
    private $validOptions = array();

    //Holds the logic behind the password groups (what type it accepts, if it's valid e.t.c)
    private $options = array();

    //Holds the characters of each group (Uppercase = 'ABCD...', Lowercase = 'abcd', Numbers e.t.c)
    private $parameters = array();

    /**
     * Even though the user should enter what is valid and what is not I believe it's good practice to set
     * some defaults hence the below. Can be overwritten by the user if needed be
     */
    public function __construct()
    {
        try {
            $this
                ->setOption(self::UPPER_CASE, array('type' => self::TYPE_BOOLEAN, 'default' => true))
                ->setOption(self::LOWER_CASE, array('type' => self::TYPE_BOOLEAN, 'default' => true))
                ->setOption(self::NUMBERS, array('type' => self::TYPE_BOOLEAN, 'default' => true))
                ->setOption(self::SYMBOLS, array('type' => self::TYPE_BOOLEAN, 'default' => true))
                ->setOption(self::LENGTH, array('type' => self::TYPE_INTEGER, 'default' => 10))
                ->setParameter(self::UPPER_CASE, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ')
                ->setParameter(self::LOWER_CASE, 'abcdefghijklmnopqrstuvwxyz')
                ->setParameter(self::NUMBERS, '0123456789')
                ->setParameter(self::SYMBOLS, '@%!?*^&');
        } catch (\Exception $e) {
            echo $e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
        }

        $this->validOptions = array(
            self::UPPER_CASE,
            self::LOWER_CASE,
            self::NUMBERS,
            self::SYMBOLS,
        );
    }

    /**
     * Set length of desired password.
     *
     * @param int $characterCount
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setLength($characterCount)
    {
        if (!is_int($characterCount) || $characterCount < 1) {
            throw new \InvalidArgumentException('Value corresponding to the password length must be a positive integer. Passed: ' . $characterCount);
        }

        $this->setOptionValue(self::LENGTH, $characterCount);

        return $this;
    }

    /**
     * Set password generator option.
     * 
     * @param mixed $option
     * @param mixed $optionSettings
     * @throws \InvalidArgumentException
     * @return PasswordGenerator
     */
    public function setOption($option, $optionSettings)
    {
        $type = isset($optionSettings['type']) ? $optionSettings['type'] : null;
        $value = isset($optionSettings['default']) ? $optionSettings['default'] : null;
        $this->options[$option] = [
            'type' => $type,
            'value' => $value
        ];

        if ($this->options[$option]['type'] == null || $this->options[$option]['value'] == null)   {
            throw new \InvalidArgumentException('Invalid option passed \'' . $option . '\' in the setOption function');
        }
        if ($this->options[$option] === null) {
            throw new \InvalidArgumentException('Invalid Option Type');
        }

        return $this;
    }

    /**
     * Set password generator option value.
     *
     * @param string $option
     * @param $value
     *
     * @return $this
     */
    public function setOptionValue($option, $value)
    {
        //Uppercase the option passed ourselves to avoid user entering 'uppercase' and throwing an error as that could be missleading to the end use
        $option = strtoupper($option);

        if (!isset($this->options[$option])) {
            throw new \InvalidArgumentException($option . ' is not a valid option. Please select from: ' . self::UPPER_CASE . ' - '  . self::LOWER_CASE . ' - ' . self::NUMBERS . ' - ' . self::SYMBOLS);
        }

        if (!is_bool($value) && $option != self::LENGTH) {
            throw new \InvalidArgumentException($option . ' only takes an boolean (true or false) argument. ' . $value . ' is a ' . gettype($value));
        }

        if ((!is_int($value) || $value < 0) && $option == self::LENGTH) {
            throw new \InvalidArgumentException($option . ' only takes a positive integer argument.');
        }
        
        $this->options[$option]['value'] = $value;


        //If the user sets a new option we add that the array of valid groups but if it's set to fault, aka the user wants to remove a group, we unset it for said array
        if (!in_array($option, $this->validOptions) && $option != self::LENGTH) {
            $this->validOptions[] = $option;
        }

        if ($value == false) {
            if (($key = array_search($option, $this->validOptions)) !== false) {
                unset($this->validOptions[$key]);
                $this->validOptions = array_values($this->validOptions);
            }
        }

        return $this;
    }

    /**
     * Return the option value.
     *
     * @param $option
     *
     * @return mixed
     */
    public function getOptionValue($option)
    {
        if (!isset($this->options[$option])) {
            throw new \InvalidArgumentException('Invalid Option');
        }

        return $this->options[$option]['value'];
    }


    /**
     * Set a list of characters a group should contain i.e setParameter('SYMBOLS', '@%!?*^&')
     * @param string $parameter
     * @param mixed $value
     *
     * @return $this
     */
    public function setParameter($parameter, $value)
    {
        $this->parameters[$parameter] = $value;

        return $this;
    }

    /**
     * Retrieve the list of characters a group contains assuming that group exists
     * 
     * @param string $parameter
     * @return null|mixed
     */
    public function getParameter($parameter)
    {
        if (!isset($this->parameters[$parameter])) {
            return null;
        }

        return $this->parameters[$parameter];
    }

    /**
     * Generate a password based on options.
     *
     * @return string password
     * @throws \Exception
     */
    public function generatePassword()
    {
        /**
         * First we check that we are within valid limits. For example that the 
         * required length of a certain group is not bigger than the set password length
         */
        if (!$this->checkLimits()) {
            throw new \LengthException('The combined requred number of characters is less than the set password length!');
        }

        do {
            //Get the list of all available characters we can use
            $characterList = $this->getCharacterList();
            //Get the # of all available characters
            $noOfCharacters = strlen($characterList);
            //Get the password length
            $length = $this->getLength();

            $password = '';
            for ($i = 0; $i < $length; ++$i) {
                //Random through our available characters and add that the the password variable
                $password .= $characterList[$this->randomInteger(0, $noOfCharacters - 1)];
            }
            //Check that the password is valid meaning it passes the checks given to it by the user
        } while (!$this->validatePassword($password));
        return $password;
    }

    /**
     * Return the character list for us we can use when generating a password.
     *
     * @return string Character list
     * @throws \Exception
     */
    public function getCharacterList()
    {
        $characters = '';

        if ($this->getOptionValue(self::UPPER_CASE)) {
            $characters .= $this->getParameter(self::UPPER_CASE);
        }

        if ($this->getOptionValue(self::LOWER_CASE)) {
            $characters .= $this->getParameter(self::LOWER_CASE);
        }

        if ($this->getOptionValue(self::NUMBERS)) {
            $characters .= $this->getParameter(self::NUMBERS);
        }

        if ($this->getOptionValue(self::SYMBOLS)) {
            $characters .= $this->getParameter(self::SYMBOLS);
        }

        if (!$characters) {
            /**
             * I got a number of issues when trying to call my own custom exceptions
             * You can see all of them called at the top of the page but never used
             * So at the end I decided to call the base exception and add a meaningful message
             */
            // throw new CharactersNotFoundException('No character sets selected.');
            throw new \Exception('No character sets selected to generate a password from.');
        }
        return $characters;
    }

    /**
     * Set count of option for a desired password.
     *
     * @param string   $option Use class constants
     * @param int|null $characterCount
     *
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function setCount($option, $characterCount)
    {
        //In case a user sets say the password to have 5 numbers (->setCount('NUMBERS', 5)) but numbers is not a valid group aka they didn't use ->setOptionValue('NUMBERS', true)
        if (!$this->validOption($option)) {
            throw new \InvalidArgumentException('You set ' . $option . ' as an invalid option');
        }

        if (is_null($characterCount)) {
            unset($this->counts[$option]);

            return $this;
        }

        //Only acccepts positive numebrs of course
        if (!is_int($characterCount) || $characterCount < 0) {
            throw new \InvalidArgumentException('Value passed for ' . $option . ' but be a positive integer. Passed: ' . $characterCount);
        }

        $this->counts[$option] = $characterCount;
        return $this;
    }

    /**
     * Retrive count for option.
     *
     * @param string $option Use class constants
     *
     * @return int|null
     */
    public function getCount($option)
    {
        return isset($this->counts[$option]) ? $this->counts[$option] : null;
    }

    /**
     * Retrieve the password length.
     *
     * @return int
     */
    public function getLength()
    {
        return $this->getOptionValue(self::LENGTH);
    }

    /**
     * Check the limits based on the counts and the options given to us
     * 
     * @return bool
     */
    public function checkLimits()
    {
        //If there are not any counts then there are no limits
        if (empty($this->counts)) {
            return true;
        }
        
        $total = 0;
        foreach ($this->counts as $option => $value) {
            if ($this->getOptionValue($option)) {
                $total += $value;
            }
        }

        $flag = false;
        foreach ($this->validOptions as $option) {
            if (!isset($this->counts[$option])) {
                $flag = true;
            }
        }

        //If the total required # of group characters are more than the total
        if ($total > $this->getLength()) {
            return false;
        }

        /**
         * If we have the case where all the valid groups have specific counts
         * without any wiggle room then we need to make sure that their total
         * match the requested password length else we are stuck in an infinate loop! 
         */
        if ($total != $this->getLength() && !$flag) {
            throw new \Exception('Password length does not match requested number and special length');
        }
        return true;
    }

    /**
     * Checks if a group is valid
     * 
     * @param string $option
     * @return bool
     */
    public function validOption($option)
    {
        return in_array($option, $this->validOptions, true);
    }

    /**
     * Check if the password is valid when comparing to counts of options.
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword($password)
    {        
        foreach ($this->validOptions as $option) {
            if (isset($this->counts[$option])) {
                $count = $this->getCount($option);
                $passwordCount = strlen(preg_replace('|[^'.preg_quote($this->getParameter($option)).']|', '', $password));
                if (!is_null($count) && $count != $passwordCount) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Generate a random value
     *
     * @param int $min
     * @param int $max
     *
     * @return int
     */
    public function randomInteger($min, $max)
    {
        return \random_int($min, $max);
    }
}
