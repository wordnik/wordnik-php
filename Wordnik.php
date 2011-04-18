<?php
/**
 * This is the official PHP client library for the Wordnik API.
 *
 * It wraps all the API calls listed here: http://docs.wordnik.com/api/methods
 * with PHP methods, and returns arrays of standard php objects containing the results.
 *
 * To use the API you'll need a key, which you can apply for here:
 * http://api.wordnik.com/signup/
 *
 * After you receive your key assign it to the API_KEY constant, below.
 * Then, to get an array of definition objects, do something like this:
 *
 * require_once('Wordnik.php');
 * $definitions = Wordnik::instance()->getDefinitions('donkey');
 *
 * $definitions will hold an array of objects, which can be accessed individually:
 * $definitions[0]->headword
 *
 * Or you can loop through the results and display info about each,
 * which could look something like this in a template context:
 *
 * <ul>
 *   <? foreach ($definitions as $definition): ?>
 *     <li>
 *       <strong><?= $definition->headword ?></strong: 
 *       <?= $definition->text ?>
 *     </li>
 *   <? endforeach; ?>
 * </ul>
 *
 * Please send comments or questions to apiteam@wordnik.com.
 */

class Wordnik {
  // the base url for all wordnik api calls
  const BASE_URI = 'http://api.wordnik.com/api';

  /*
   * If there's an existing Wordnik instance, return it, otherwise create and return a new one.
   */
  private static $instance;
  public static function instance($api_key) {
    if (self::$instance == NULL) {
      if (!isset($api_key) || $api_key=="YOUR_API_KEY" || trim($api_key)=='') {
        throw new Exception("You need to specify a valid api_key!");
      }
      self::$instance = new Wordnik();
      self::$instance->api_key=$api_key;
    }

    return self::$instance;
  }

  /*
   * Pass in a word as a string, get back an array of phrases containing this word (bigrams).
   * Optional params:
   *  count : the number of results returned (default=10)
   * More info: http://docs.wordnik.com/api/methods#phrases
   */
  public function getPhrases($word, $count=10) {
    if(is_null($word) || trim($word) == '') {
      throw new InvalidParameterException("getPhrases expects word to be a string");
    }
    $params = array();
    $params['count'] = $count;
    return $this->callApi('/word.json/' . rawurlencode($word) . '/phrases', $params);
  }

  public function getWordSearch($query, $params = array()) {
    if(is_null($query) || trim($query) == '') {
      throw new InvalidParameterException("getWordSearch expects query to be a string");
    }
    return $this->callApi('/words.json/search', array_merge($params, array("query" => $query)));
  }

  public function getDefinitions($word, $params = array()) {
    if(is_null($word) || trim($word) == '') {
      throw new InvalidParameterException("getDefinitions expects word to be a string");
    }
    return $this->callApi('/word.json/' . rawurlencode($word) . '/definitions', $params);
  }

  /*
   * Pass in a word as a string, get back an array of example sentences. 
   * More info: http://docs.wordnik.com/api/methods#examples
   */
  public function getExamples($word) {
    if(is_null($word) || trim($word) == '') {
      throw new InvalidParameterException("getExamples expects word to be a string");
    }
    return $this->callApi( '/word.json/' . rawurlencode($word) . '/examples' );
  }

  /*
   * Pass in a word as a string, get back an array of related words.
   * Optional params:
   *   count : the number of results returned (default=10)
   *   type : only get definitions with a specific type of relation (e.g., 'synonym' or 'antonym')
   * More info: http://docs.wordnik.com/api/methods#relateds
   */
  public function getRelatedWords($word, $count=10, $type=null) {
    if(is_null($word) || trim($word) == '') {
      throw new InvalidParameterException("getRelatedWords expects word to be a string");
    }
    $params = array();
    $params['count'] = $count;
    if (isset($type)) {
      $params['type'] = $type;
    }
    return $this->callApi('/word.json/' . rawurlencode($word) . '/related', $params);
  }

  /*
   * Pass in a word as a string, get back frequency data.
   * More info: http://docs.wordnik.com/api/methods#frequency
   */
  public function getFrequency($word) {
    if(is_null($word) || trim($word) == '') {
      throw new InvalidParameterException("getFrequency expects word to be a string");
    }
    return $this->callApi('/word.json/' . rawurlencode($word) . '/frequency');
  }

  /*
   * Pass in a word as a string, get back punctuation factor.
   * More info: http://docs.wordnik.com/api/methods#punc
   */
  public function getPunctuation($word) {
    if(is_null($word) || trim($word) == '') {
      throw new InvalidParameterException("getPunctuation expects word to be a string");
    }
    return $this->callApi('/word.json/' . rawurlencode($word) . '/punctuationFactor');
  }

  /*
   * Pass in a word as a string, get back text pronunciations
   * More info: http://docs.wordnik.com/api/methods#textpron
   */
  public function getTextPronunciations($word) {
    if(is_null($word) || trim($word) == '') {
      throw new InvalidParameterException("getPunctuation expects word to be a string");
    }
    return $this->callApi('/word.json/' . rawurlencode($word) . '/pronunciations');
  }

  /*
   * Pass in a word fragment as a string, get back suggested words that start with that phrase.
   * Useful for autocomplete functionality.
   * Optional params:
   *  count : the number of results to return
   *  start_at : the offset for the results (useful for pagination)
   * More info: http://docs.wordnik.com/api/methods#auto
   */
  public function getSuggestions($word_fragment, $count=10, $start_at=0) {
    if(is_null($word_fragment) || trim($word_fragment) == '') {
      throw new InvalidParameterException("Autocomplete expects word to be a string");
    }
    $params = array();
    $params['count'] = $count;
    $params['startAt'] = $start_at;
    return $this->callApi('/suggest.json/' . rawurlencode($word_fragment), $params);
  }

  /*
   * Get the Word of the Day
   * More info: http://docs.wordnik.com/api/methods#wotd
   */
  public function getWordOfTheDay() {
    return $this->callApi('/wordoftheday.json/');
  }

  /*
   * Get a random word from the Wordnik corpus
   * Optional params:
   *    has_definiton : force the method to return a word with a definition (default=true)
   * More info: http://docs.wordnik.com/api/methods#random
   */
  public function getRandomWord($has_definition=true) {
    $params = array();
    if (!$has_definition) {
      $params['hasDictionaryDef'] = false;
    }
    return $this->callApi('/words.json/randomWord', $params);
  }

  /*
   * Authenticate this instance of Wordnik.
   * You need to do this before making any list-related calls.
   * Required params:
   *   username : the authenticating user
   *   password : the authenticating user's password
   * More info: http://docs.wordnik.com/api/methods#auth
   */
  public function getAuthToken($username, $password) {
    if(is_null($username) || trim($username) == '') {
      throw new InvalidParameterException("Authentication expects username to be a string");
    }
    $params = array();
    $params['password'] = $password;
    $auth_info = $this->callApi('/account.json/authenticate/' . urlencode($username), $params);
    $this->auth_token = $auth_info->token;
    return $this->auth_token;
  }

  /*
   * Ensures that this instance of Wordnik has been authenticated.
   * (See getAuthToken)
   */
  private function ensureAuthentic() {
    if (!isset($this->auth_token)) {
      throw new Exception("You need to call getAuthToken before requesting this api resource.");
    } else {
      return true;
    }
  }

  /*
   * Get all of the authenticated user's lists.
   * Note: you must call getAuthToken before calling this.
   * More info: http://docs.wordnik.com/api/methods#lists
   */
  public function getLists() {
    $this->ensureAuthentic();
    return $this->callApi('/wordLists.json/');
  }

  /*
   * Create a new list on behalf of the authenticated user.
   * Note: you must call getAuthToken before calling this.
   * Required params:
   *  name : the name of this list
   *  description : a description about this list
   * Optional param:
   *  type : list permissions, either 'PUBLIC' or 'PRIVATE'
   * More info: http://docs.wordnik.com/api/methods#lists
   */
  public function createList($name, $description, $type='PUBLIC') {
    $this->ensureAuthentic();
    $params = array();
    $params['name'] = $name;
    $params['description'] = $description;
    $params['type'] = $type;
    return $this->callApi('/wordLists.json/', $params, 'post');
  }

  /*
   * Delete the given list on behalf of the authenticated user.
   * Note: you must call getAuthToken before calling this.
   * Required params:
   *   list_permalink : the list's permalink id
   */
  public function deleteList($list_permalink) { 
    $this->ensureAuthentic();
    return $this->callApi('/wordList.json/'.$list_permalink, null, 'delete');
  }

  /*
   * Get all the words in the given list
   * Note: you must call getAuthToken before calling this.
   * Required params:
   *   list_permalink : the list's permalink id
   */
  public function getListWords($list_permalink) {
    $this->ensureAuthentic();
    return $this->callApi('/wordList.json/'.$list_permalink.'/words');
  }

  /*
   * Add a word to the given list
   * Note: you must call getAuthToken before calling this.
   * Required params:
   *   wordstring : the word to add to the list
   *   list_permalink : the list's permalink id
   */
  public function addWordToList($wordstring, $list_permalink) {
    $this->ensureAuthentic();
    $params = array();
    $params['wordstring'] = $wordstring;
    $param_container = array();
    $param_container[] = $params;
    return $this->callApi('/wordList.json/'.$list_permalink.'/words', $param_container, 'post');
  }

  /*
   * Delete a word from the given list
   * Note: you must call getAuthToken before calling this.
   * Required params:
   *   wordstring : the word to delete from the list
   *   list_permalink : the list's permalink id
   */
  public function deleteWordFromList($wordstring, $list_permalink) {
    $this->ensureAuthentic();
    $params = array();
    $params['wordstring'] = $wordstring;
    $param_container = array();
    $param_container[] = $params;
    return $this->callApi('/wordList.json/'.$list_permalink.'/deleteWords', $param_container, 'post');
  }

  /*
   * Utility method to call json apis.
   * This presumes you want JSON back; could be adapted for XML pretty easily.
   */
  private function callApi($url, $params=array(), $method='get') {
    $data = null;

    $headers = array();
    $headers[] = "Content-type: application/json";
    $headers[] = "api_key: " . $this->api_key;
    if (isset($this->auth_token)) {
      $headers[] = "auth_token: " . $this->auth_token;
    }

    $url = (self::BASE_URI . $url);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_TIMEOUT, 5); // 5 second timeout
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // return the result on success, rather than just TRUE
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    if ($method=='get' && !empty($params)) { // set query params if method is get
      $url = ($url . '?' . http_build_query($params));
    } else if ($method=='post') { // set post data if the method is post
      curl_setopt($curl,CURLOPT_POST,true);
      curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($params));
    } else if ($method=='delete') { // set post data if the method is post
      curl_setopt($curl,CURLOPT_CUSTOMREQUEST,"DELETE");
      curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($params));
    }

    curl_setopt($curl, CURLOPT_URL, $url);

    // make the request
    $response = curl_exec($curl);
    $response_info = curl_getinfo($curl);

    // handle the response based on the http code
    if ($response_info['http_code'] == 0) {
      throw new Exception( "TIMEOUT: api call to " . $url . " took more than 5s to return" );
    } else if ($response_info['http_code'] == 200) {
      $data = json_decode($response);
    } else if ($response_info['http_code'] == 401) {
      throw new Exception( "Unauthorized API request to " . $url . ": ".json_decode($response)->message );
    } else if ($response_info['http_code'] == 404) {
      $data = null;
    } else {
      throw new Exception("Can't connect to the api: " . $url . " response code: " . $response_info['http_code']);
    }

    return $data;
  }
}
