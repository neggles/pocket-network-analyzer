## About check-timeout.py

This program is supposed to work as follows:
* Check the timeout.json file to check the last time it was updated
* If it has been more than 5 minutes, sound the alarm
* After sounding the alarm update the timestamp
* The timeout.json file is updated by this script which is run by a cron job
	* The file is also updated by the web interface whenever the interface is being used