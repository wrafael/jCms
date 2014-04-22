<?php
/**
 * This is a little halper class to generate funny quotes instead of errors
 */
namespace jcms;

class ErrorHelper {
  public static function getRandomQuote(){
    $quotes = array(
        '"I\'m going to make him an offer he can\'t refuse." - Don Vito Corleone',
        '"Toto, I\'ve got a feeling we\'re not in Kansas anymore." - Dorothy Gale',
        '"Frankly, my dear, I don\'t give a damn." - Rhett Butler',
        '"Go ahead, make my day." - Harry Callahan',
        '"May the Force be with you." - Han Solo',
        '"What we\'ve got here is failure to communicate." - Captain (Cool Hand Luke)',
        '"I love the smell of napalm in the morning." - Lt. Col. Bill Kilgore',
        '"E.T. phone home." - E.T.',
        '"After all, tomorrow is another day!"- Scarlett O\'Hara',
        '"I\'ll be back." - The Terminator',
        '"Mama always said life was like a box of chocolates. You never know what you\'re gonna get." - Forrest Gump',
        '"I see dead people." - Cole Sear',
        '"It\'s alive! It\'s alive!" - Henry Frankenstein',
        '"Houston, we have a problem." - Jim Lovell',
        '"Say \'hello\' to my little friend!" - Tony Montana',
        '"Hasta la vista, baby." - The Terminator'
    );
    return $quotes[array_rand($quotes)];
  }
}

?>