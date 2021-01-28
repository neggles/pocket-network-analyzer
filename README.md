# Pocket Network Analyzer Custom Web Interface

![alt text](https://github.com/brandon-bailey/pocket-network-analyzer/blob/master/assets/screenshots/main.jpg?raw=true)

## There is alot to unpack here

- This project is coming from a "proprietary" project I built, so there will be some bugs as I work out the kinks
- If there is a following for this project I will work closely with all interested stakeholders to work through any issues or question that arise from its use

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

