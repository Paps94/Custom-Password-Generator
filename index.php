<?php

include 'PasswordGenerator.php';
$generator = new PasswordGenerator();

try {
  $generator
  ->setLength(3)
  ->setOptionValue('UPPERCASE', true)
  ->setOptionValue('LOWERCASE', true)
  ->setOptionValue('NUMBERS', true)
  ->setOptionValue('SYMBOLS', true)
  ->setCount('LOWERCASE', 2)
  ->setCount('UPPERCASE', 2)
  ->setCount('NUMBERS', 1)
  ->setCount('SYMBOLS', 1)
  ;

  try {
    $password = $generator->generatePassword();
    echo '<pre>' . var_export($password, true) . '</pre>';exit;
  } catch (Exception $e) {
    echo $e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
  }
} catch (Exception $e) {
  echo $e->getMessage() . ' ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL;
}





