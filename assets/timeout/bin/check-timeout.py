#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Copyright 2012-2016 Brandon Bailey
# All Rights Reserved.

import datetime
import time

try:
    import json
except ImportError:
    try:
        import simplejson as json
    except ImportError:
        json = None

LOG_FILE = '/home/pfi/public_html/logs/timeout/timeout.json'

with open(LOG_FILE) as data_file:    
    data = json.loads(data_file.read())

timeStamp = int(data["time"])
currentTime = int(time.time() * 1000)
duration = currentTime - timeStamp

if duration >= 300000:
	print(duration)

print (str(datetime.timedelta(milliseconds=duration)))

#print(timeStamp)