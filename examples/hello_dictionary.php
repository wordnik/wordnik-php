<?php 
  require("Wordnik.php"); 
  $api_key = "YOUR_API_KEY";
  // create an instance of the Wordnik class, to make api calls
  $wordnik = Wordnik::instance($api_key);
  // get the Word of the Day
  $wotd = $wordnik->getWordOfTheDay();
  $wotd = $wotd->wordstring;
  // if the user pressed the "I'm Feeling Wordie" button, fetch a random word and set it to $wordstring
  if (isset($_GET['commit']) && ($_GET['commit']=="I\'m Feeling Wordie")) {
    $word = $wordnik->getRandomWord();
    $wordstring = $word->wordstring;
  } else if (isset($_GET['word'])) { // the user is searching for a specific word, either via the form or a link
    $wordstring = $_GET['word'];
  }
?>
<html>
  <head>
    <title>Hello, Dictionary!</title>
  </head>
  <body>
    <div>
      <h1>Hello, Dictionary!</h3>
      <h3>The Wordnik Word of the Day for <?php echo(date("l F j, Y")) ?> is... <strong><a href="/hello_dictionary.php?word=<?php echo(urlencode($wotd)) ?>"><?php echo($wotd) ?></a></strong>!
      </h3>
      <p>
        <form method='get' action='/hello_dictionary.php'>
          Look up a word: 
          <input type='text' name='word' />
          <input type='submit' name='commit' value='Search' />
          <input type='submit' name='commit' value="I'm Feeling Wordie" />
        </form>
      </p>
<?php if (isset($wordstring)): ?>
  <hr />

  <!-- Definitions -->
  <div>
    <?php $definitions = $wordnik->getDefinitions($wordstring); ?>
    <h2>Definitions of <em><?php echo($wordstring) ?></em></h2>
    <?php if (empty($definitions)): ?>
      <em>Sorry, we couldn't find any definitions!</em>
    <?php else: ?>
      <ul>
        <?php 
          foreach($definitions as $definition) {
            if (isset($definition->text)) {
              echo("<li>".$definition->text."</li>");
            }
          }
        ?>
      </ul>
      <a href="http://wordnik.com/words/<?php echo(urlencode($wordstring)) ?>">more definitions</a>
    <?php endif ?>
  </div>

  <!-- Example sentences -->
  <div>
    <?php $examples = $wordnik->getExamples($wordstring); ?>
    <h2>Examples of <em><?php echo($wordstring) ?></em></h2>
    <?php if (empty($examples)): ?>
      <em>Sorry, we couldn't find any definitions!</em>
    <?php else: ?>
      <ul>
        <?php
          foreach($examples as $example) {
            if (isset($example->display)) {
              echo("<li>".$example->display);
              if (isset($example->title)) {
                echo("<br />- <em>".$example->title."</em>");
              }
            }
          }
        ?>
      </ul>
      <a href="http://wordnik.com/words/<?php echo(urlencode($wordstring)) ?>">more examples</a>
    <?php endif ?>
  </div>

  <!-- Related words -->
  <div>
    <?php $relateds = $wordnik->getRelatedWords($wordstring); ?>
    <h2>Words related to <em><?php echo($wordstring) ?></em></h2>
    <?php if (empty($relateds)): ?>
      <em>Sorry, we couldn't find any definitions!</em>
    <?php else: ?>
      <?php 
        foreach($relateds as $related) {
          echo("<h3>".$related->relType."</h3>");
          echo("<ul>");
          foreach($related->wordstrings as $r) {
            echo("<li><a href='/hello_dictionary.php?word=".urlencode($r)."'>".$r."</a></li>");
          }
          echo("</ul>");
        }
      ?>
      <a href="http://wordnik.com/words/<?php echo(urlencode($wordstring)) ?>">more examples</a>
    <?php endif ?>
  </div>

  <!-- Word phrases (bigrams) -->
  <div>
    <?php $phrases = $wordnik->getPhrases($wordstring); ?>
    <h2>Phrases containing <em><?php echo($wordstring) ?></em></h2>
    <?php if (empty($phrases)): ?>
      <em>Sorry, we couldn't find any phrases!</em>
    <?php else: ?>
      <ul>
        <?php 
          foreach($phrases as $phrase)  {
            $complete_phrase = ($phrase->gram1 . ' ' . $phrase->gram2);
            print("<li><a href='/hello_dictionary.php?word=" . urlencode($complete_phrase) . "'>" . $complete_phrase . "</a></li>");
          }
        ?>
      </ul>
      <a href="http://wordnik.com/words/<?php echo(urlencode($wordstring)) ?>">more phrases</a>
    <?php endif ?>
  </div>
<?php endif ?>
    <hr />
    <div>
      <p>Hello Dictionary was built with the <a href="http://wordnik.com/developers">Wordnik API</a>.</p>
      <p><strong>Documentation</strong>: <a href="http://docs.wordnik.com/api/methods">http://docs.wordnik.com/api/methods</a></p>
      <p><strong>API Key Signup</strong>: <a href="http://api.wordnik.com/signup/">http://api.wordnik.com/signup</a></p>
      <p><strong>Support</strong>: <a href="http://groups.google.com/group/wordnik-api">http://groups.google.com/group/wordnik-api</a></p>
    </div>
  </body>
</html>
