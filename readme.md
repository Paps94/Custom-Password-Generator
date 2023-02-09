# Custom Password Generator

Below you will find my trail of thought as well as some key information on how I go about working on something new for the first time!

## Getting Started

I didn't have any particular idea or design of how I wanted to do this. At the end I decided I just need to not use any pseudo random generator hence why I went with random_int(). While trying to find the best random php function comparing between functions like rand(), mt_rand() and random_int(). At the end I went for random_int which provides `... a cryptographically secure, uniformly selected integer` (https://www.php.net/manual/en/function.random-int) but this requires at least PHP 7. 

I do like the builder pattern approach when it comes to creating something that requires a lot of parementers as it is in this case with a password generator. With the builder pattern we create the object step by step using methods instead of the constructor.

### Prerequisites

Composer - Used it to install PhpUnit! - Need to run `composer install` to be able to run the tests using `./vendor/bin/phpunit`
PHP v7 - Due to reasons mentioned above.

### Over the top run down

I extensively commented the code for anyone's convinience. I always try to since I am also gonna need to read said comments months down the line when I re look at this project! Who knows if I feel motivated I might turn this into a composer downloadable module you can simply add to your dependancies and freely use!

## Issues I did not think of

Only issue i encountered was the fact that my custom exceptions where not being read by the class and therefore I was getting fatal error as the exception could not be found! I tried to find the reason behind this but at the time of writting this i did not. My guess it's something very simple but I have been staring it for so long I cannot find the issue. Perhaps after a come back from dinner :D

## Testing

I used PhpUnit to write my tests. I think I covered everything(?). 

## Final Thoughts and Comments

At this current stage I know that if you request a very large password it will probably take a good while for the class to generate one based on the current logic. If this is a requirement for anyone I would suggest instead of doing a while loop and validating the generated password you can take the parameters given to the class (aka 2 numbers, 2 symbols) add 2 random numbers and 2 random symbols into your available characters array. Fill the rest with more random lowercase and uppercase letters (if those are allowed) and boom no more do while loops!

  - Hat tip to my cats who turned off my pc more times I could count! They made me more resilient than ever since I did not toss them off the 8th floor right into the Thames! I will get an external power button to avoid that from happening in the future of course!
