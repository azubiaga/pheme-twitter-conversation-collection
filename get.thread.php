<?php
ini_set("memory_limit", "2048M");

function get_replying_ids ($tweetid, $username) {
  global $replyingids;

  $maxposition = "";

  do {
    if ($maxposition == "") {
      $url = "https://twitter.com/" . $username . "/status/" . $tweetid;
    }
    else {
      $url = "https://twitter.com/i/" . $username . "/conversation/" . $tweetid . "?include_available_features=1&include_entities=1&max_position=" . $maxposition;
    }
    $content = shell_exec("wget \"" . $url . "\" -q --load-cookies=./cookies.txt -O -");
    $content = html_entity_decode(str_replace("\\n", "\n", $content));
    $content = str_replace("\\u003c", "<", $content);
    $content = str_replace("\\u003e", ">", $content);
    $content = str_replace("\\/", "/", $content);
    $content = str_replace("\\\"", "\"", $content);

    if (preg_match_all("|<a href=\"(/[^/]*/status/[0-9]*)\" class=\"tweet-timestamp js-permalink js-nav js-tooltip\"|U", $content, $reptweets)) {
      foreach ($reptweets[1] as $key => $reptweet) {
        $reptweettokens = explode("/", $reptweet);
        $repusername = $reptweettokens[1];
        $reptweetid = $reptweettokens[count($reptweettokens) - 1];

        if (!in_array($reptweetid, $replyingids)) {
          array_push($replyingids, $reptweetid);
          get_replying_ids($reptweetid, $repusername);
        }
      }
    }

    $maxposition = "";
    if (preg_match("|data-min-position=\"([^\"]*)\"|U", $content, $mp) || preg_match("|\"min_position\":\"([^\"]*)\"|U", $content, $mp)) {
      $maxposition = $mp[1];
    }
  } while ($maxposition != "");
}

function add_to_structure ($tweetid, $inreplyto) {
  global $structure;

  foreach ($structure as $id => $substructure) {
    if ($id == $inreplyto) {
      $structure[$id] = $tweetid;
    }
    else {
      add_to_structure($tweetid, $inreplyto, $structure[$id]);
    }
  }
}

function collect_replying_tweets ($tweetid, $username) {
  global $argv, $replyingids;
  $replycount = 0;

  @mkdir("data/" . $tweetid . "/reactions/");
  @chmod("data/" . $tweetid . "/reactions/", 0777);
  get_replying_ids($tweetid, $username);

  $idsstr = "";
  $idcount = 0;
  $allcount = 0;
  foreach ($replyingids as $replyingid) {
    $allcount++;
    $idsstr .= $replyingid . ",";
    $idcount++;
    if ($idcount == 100 || $allcount == count($replyingids)) {
      $tweets = @shell_exec("python retrieve.tweet.list.py " . substr($idsstr, 0, strlen($idsstr) - 1));
      $tweets = explode("\n", $tweets);
      foreach ($tweets as $tweet) {
        $tweetobj = @json_decode($tweet);
        if (isset($tweetobj->id_str)) {
          file_put_contents("data/" . $tweetid . "/reactions/" . $tweetobj->id_str . ".json", $tweet);
          $replycount++;
        }
      }

      $idsstr = "";
      $idcount = 0;
    }
  }

  if (isset($argv[1])) {
    echo $tweetid . " - source tweet and " . $replycount . " replies collected.\n";
  }
}

function create_structure($tweetid) {
  global $structure;

  $parents = array();
  $dir = dir("data/" . $tweetid . "/reactions/");
  while (($file = $dir->read()) !== false) {
    if ($file != "." && $file != "..") {
      $tweet = json_decode(file_get_contents("data/" . $tweetid . "/reactions/" . $file));

      $inreplyto = $tweet->in_reply_to_status_id_str;
      $id = $tweet->id;

      if (!isset($parents[$inreplyto])) {
        $parents[$inreplyto] = array();
      }
      array_push($parents[$inreplyto], $id);
    }
  }

  foreach ($structure as $sid => $substructure) {
    if (isset($parents[$sid])) {
      foreach ($parents[$sid] as $cid) {
        $structure[$sid][$cid] = array();
      }
    }
  }

  file_put_contents("data/" . $tweetid . "/structure.json", json_encode($structure));
  chmod("data/" . $tweetid . "/structure.json", 0777);
}

if (!isset($argv[1])) {
  exit(0);
}
$tweetid = $argv[1];

if (strstr($tweetid, "/")) {
  $tweetid = explode("/", $tweetid);
  $tweetid = $tweetid[count($tweetid) - 1];
}

$replyingids = array();
$structure = array($tweetid => array());

$sourcetweet = @shell_exec("python retrieve.tweet.py " . $tweetid);
$sourcetweetobj = json_decode($sourcetweet);
if (isset($sourcetweetobj->id_str)) {
  $username = $sourcetweetobj->user->screen_name;

  @mkdir("data/" . $tweetid);
  @chmod("data/" . $tweetid, 0766);
  @mkdir("data/" . $tweetid . "/source-tweets/");
  @chmod("data/" . $tweetid . "/source-tweets/", 0766);
  file_put_contents("data/" . $tweetid . "/source-tweets/" . $tweetid . ".json", $sourcetweet);

  collect_replying_tweets($tweetid, $username);

  create_structure($tweetid);
}
?>
