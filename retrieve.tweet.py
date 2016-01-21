import json
import tweepy
import sys
import pprint
import os
import ConfigParser
import time

tweetid = sys.argv[1]

config = ConfigParser.ConfigParser()
config.read('twitter.ini')

consumer_key = config.get('Twitter', 'consumer_key')
consumer_secret = config.get('Twitter', 'consumer_secret')
access_key = config.get('Twitter', 'access_key')
access_secret = config.get('Twitter', 'access_secret')

auth = tweepy.OAuthHandler(consumer_key, consumer_secret)
auth.set_access_token(access_key, access_secret)
api = tweepy.API(auth)

try:
	tweet = api.get_status(tweetid)
	print json.dumps(tweet.json)
except:
	sys.exit()
