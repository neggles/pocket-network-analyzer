# Pocket Network Analyzer Custom Web Interface

![Main Webpage](./assets/screenshots/main.JPG?raw=true)


## User Experience Network Test

> Note: This is intended to give an insight into what the network quality will look like given the providers guidance on network requirements

![User Experience](./assets/screenshots/ux.JPG?raw=true)


## There is alot to unpack here
- This was a project built for ISP's to verify their network speeds after installations to ensure the customer is receiving their full speeds.
- If there is a following for this project I will work closely with all interested stakeholders to work through any issues or question that arise from its use

> Note: This project is intended to fill the gap in devices capable of achieving full Gigabit speeds. While ISP's will sell you 1GB^ most consumer devices are not capable of testing these speeds. Enter this project!

### For working with debian linux operating system.

- Built specifically to work on Odroid-XU4 platform since it had a fully Gig ethernet port which was a requirement for the project
- It should work on rasberry pi or any other debian based system really. The software NEEDS two wireless cards to function properly, one on board "nl80211" compatible ideally and a USB wifi chip will work for the secondary.

> Note: See wiki pages for information on dependencies and developer information.

## Settings to change to get started

```php
// application/config/database.php
$db['default'] = array(
    'password' => '',
    'database' => './application/database/pfi.db',
    'dbdriver' => 'sqlite3',
);

```

# Speedtest results using Odroid-XU4 and iperf3 (pspeed3)
![iPerf3](./assets/screenshots/pspeed-results.JPG?raw=true)
