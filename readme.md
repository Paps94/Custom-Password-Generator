# Krystal Test Exercise

Below you will find my trail of thought as well as some key information on how I go about working on something new for the first time!

## Getting Started

First things first I thought the url provided is invalid. After checking again it was correct and I am just stupid (https://github.com/krystal/code-tasks/blob/main/password-generator.md).

After going though the instructions I tried to figure out if I had any questions but almost everyting seemed staight forward. Things I wasn't sure about but didn't want to bother Jonathan during the weekend goes as follow:

⋅⋅* 'The function should produce an error in the event of invalid options' - Instead of errors I tried to catch exceptions. Not sure if the word errors was meant literally or more of a guideline.
⋅⋅* 'The code should be packaged as a module which could be included into another library or application' - Again I am not sure if this means literally create a composer package or is more of a guide.

While trying to find the best random php function comparing between functions like rand(), mt_rand() and random_int(). At the end I went for random_int which provides `... a cryptographically secure, uniformly selected integer` (https://www.php.net/manual/en/function.random-int) but this requires at least PHP 7. 

### Prerequisites

Composer - Used it to install PhpUnit! - Need to run `composer install` to be able to run the tests using `./vendor/bin/phpunit`
PHP v7 - Due to reasons mentioned above.

### Over the top run down

After setting up my wokring folder and created my files I started working on the test. Since it was asked to be packaged as a module I thought it was only applicable to create a class that you can instanciate and generate custom passwords on the fly.

I extensively commented the code for your convinience.

## Issues I did not think of

Only issue i encountered was the fact that my custom exceptions where not being read by the class and therefore I was getting fatal error as the exception could not be found! I tried to find the reason behind this but at the time of writting this i did not. My guess it's something very simple but I have been staring it for so long I cannot find the issue. Perhaps after a come back from dinner :D

### Testing

I used PhpUnit to write my tests. I think I covered everything(?). 

### Final Thoughts and Comments

I really enjoyed this test actually. I spent on it more time that recommended but as Jonathan said.. This is what the weeneds are for! If i did miss something I would love to know, feedback is always greatly appriciated!

  - Hat tip to my cats who turned off my pc more times I could count! They made me more resilient than ever since I did not toss them off the 8th floor right into the Thames! I will get an external power button to avoid that from happening in the future of course!
