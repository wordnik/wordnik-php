Wordnik API: PHP Client (deprecated)
====================================

**Note: This library is outdated**

This is the official PHP client library for the Wordnik API.

It wraps all the API calls listed here: <http://docs.wordnik.com/api/methods>
with PHP methods, and returns arrays of standard php objects containing the results.

To use this library, you'll need an API key, which you can get here:
<http://api.wordnik.com/signup>

After you receive your key, use it to initialize an instance of Wordnik: 

    require_once('Wordnik.php');
    $wordnik = Wordnik::instance('YOUR_API_KEY');

Now you can use $wordnik to access the API.  For example:

    $definitions = $wordnik->getDefinitions('cat');

Returns this: 
    Array ( [0] => stdClass Object ( [id] => 3117123 [notes] => Array ( [0] => stdClass Object ( [pos] => 0 [value] => â˜ž The domestic cat includes many varieties named from their place of origin or from some peculiarity; as, the Angora cat; the Maltese cat; the Manx cat; the Siamese cat. ) ) [headword] => cat [partOfSpeech] => noun [citations] => Array ( [0] => stdClass Object ( [source] => Mark Derr (N. Y. Times, Nov. 2, 1999, Science Times p. F2). [cite] => Laying aside their often rancorous debate over how best to preserve the Florida panther, state and federal wildlife officials, environmentalists, and independent scientists endorsed the proposal, and in 1995 the eight cats [female Texas cougars] were brought from Texas and releasedâ€¦ . Uprooted from the arid hills of West Texas, three of the imports have died, but the remaining five adapted to swamp life and have each given birth to at least one litter of kittens. ) ) [text] => Any animal belonging to the natural family Felidae, and in particular to the various species of the genera Felis, Panthera, and Lynx. The domestic cat is Felis domestica. The European wild cat (Felis catus) is much larger than the domestic cat. In the United States the name wild cat is commonly applied to the bay lynx (Lynx rufus). The larger felines, such as the lion, tiger, leopard, and cougar, are often referred to as cats, and sometimes as big cats. See wild cat, and tiger cat. [labels] => Array ( [0] => stdClass Object ( [type] => fld [text] => (ZoÃ¶l.) ) ) [sequence] => 0 [seqString] => 1. ) [1] => ... )

And now we can loop over these definitions in our template:

    <ul>
      <?php 
        foreach($definitions as $definition) {
          if (isset($definition->text)) {
            echo("<li>".$definition->text."</li>");
          }
        }
      ?>
    </ul>

Via the API, you can get a lot of data about words, and you can also create
and manage word lists.

Resources
---------
Wordnik API Documentation: <http://docs.wordnik.com/api/methods>

Wordnik API Key Signup: <http://api.wordnik.com/signup>

Wordnik API Support: <http://groups.google.com/group/wordnik-api>

Please send comments or questions to <apiteam@wordnik.com>.

Copyright
---------

Copyright (c) 2011 Altay Guvench / Wordnik.com. See LICENSE for details.
