<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
    <title>PHEME - Collection of Twitter Threads</title>
  </head>
  <body>
    <h1>Collection of Twitter Threads/Conversations</h1>
    <p>This web application enables to collect the whole conversation in the form of replies, given the source tweet as the input. The output is rendered in an HTML file that visualises the conversation in a forum-like visualisation where replies are indented.</p>

    <p>To start, please input the URL of a tweet for which you would like to see the whole conversation:</p>

    <form method="post" action="./get.thread.php">
      <input type="text" name="tweetid" value="" style="width: 400px;" /> <input type="submit" name="submit" value="OK" />
    </form>
  </body>
</html>
