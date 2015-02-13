#!/usr/bin/env python

#INSTALL "twitter" AND "twittersearch" IF NEEDED
#EXAMPLE:
#	sudo pip install twitter 
#	sudo pip install twittersearch

import twitter
from TwitterSearch import *
import time

#CREATE AN APP AT "https://apps.twitter.com" AND CREATE ACCESS TOKEN FOR OAUTH
#YOUR TWITTER API KEYS HERE

auth={}
auth["consumer_key"]=""
auth["consumer_secret"]=""
auth["access_token"]=""
auth["access_token_secret"]=""


tso = TwitterSearchOrder()
tso.set_include_entities(False)
ts=TwitterSearch(
    consumer_key=auth["consumer_key"],
    consumer_secret=auth["consumer_secret"],
    access_token=auth["access_token"],
    access_token_secret=auth["access_token_secret"]
)

def find_by_url(url):
    global tso,ts
    tso.set_keywords([url])
    first_tweet=None
    first_time=None
    for tweet in ts.search_tweets_iterable(tso):
        timestamp=time.strftime('%Y-%m-%d %H:%M:%S', time.strptime(tweet['created_at'],'%a %b %d %H:%M:%S +0000 %Y'))
        if first_time is None or timestamp<first_time:
            first_tweet=tweet
            first_time=timestamp
    if first_tweet:
        print "@%s %s" % (first_tweet["user"]["screen_name"],first_tweet["text"])

print "Twitter find Authors by Mandingo v1.0 - Feb 2015"
print "------------------------------------------------"
        
api=twitter.Api(consumer_key=auth["consumer_key"],consumer_secret=auth["consumer_secret"],access_token_key=auth["access_token"],access_token_secret=auth["access_token_secret"])

for t in api.GetHomeTimeline():
    if not "urls" in t.AsDict():
        continue
    url=t.AsDict()["urls"].values()[0]
    print url
    find_by_url(url)
