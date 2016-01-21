The PHEME conversation collection script allows the user to collect the set of tweets replying to a specific tweet, forming a conversation or a thread. The user needs to specify a single source tweet, providing its URL or tweet ID, and the tool collects the replies and stories them in the 'data/tweet-id' directory.

NOTE: Since retrieval of replying tweets has been discontinued in Twitter API v1.1 (it used to be available through the 'related_results/show' endpoint in v1.0), this script scrapes the replies from the HTML of the source tweet.

-----
SETUP
-----

The script is developed using PHP (it was initially intended to run as a web service) and Python (for the back-end to access the Twitter API using the Tweepy library). It is ready to run on a system with PHP installed (i.e., the 'php5-cli' package on UNIX systems, or the equivalent PHP CLI for Windows). However, it does need to define a few settings in order to be able to access Twitter.com and the API. There are basically two steps:

1. Create a cookie file to enable web access to replies:

This can be done using Mozilla Firefox and the add-on Cookies Export/Import, which can be installed from:
https://addons.mozilla.org/en-US/firefox/addon/cookies-exportimport/

It is recommended to delete all existing cookies on Firefox before proceeding. Then, you should log in on Twitter.com with some user account. After logging in, click on 'Tool -> Export cookies', and save the file with the name 'cookies.txt'. This file should be copied into the main directory of this script.

2. Add Twitter API credentials to twitter.ini:

This is the Twitter API configuration file. You will see four lines there where you can specify the Twitter API credentials.

-------------
HOW TO RUN IT
-------------

Once you have the ID of the tweet that you want to get the conversation for, you can run the following command:

php get.thread.php tweet-id

Where tweet-id is the ID of the tweet that you are interested in. If successful, the script will output the number of replies that have been collected, and a new directory will be created in the 'data' folder.

---------
REFERENCE
---------

This conversation collection script was used for the following paper:

Arkaitz Zubiaga, Geraldine Wong Sak Hoi, Maria Liakata, Rob Procter, Peter Tolmie. Analysing How People Orient to and Spread Rumours in Social Media by Looking at Conversational Threads. arXiv. 2015.
# pheme-conversation-collection
