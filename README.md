# RaspWeatherSnooze
Make your raspberry pi an alarm clock that speaks about weather information!

### Requisites
- Raspberry Pi, of course
- Php (5.3.0 or newer)
- Lighttpd Web Server
- Espeak

### Installation instructions

1. Install prerequisites
```
sudo apt-get update
sudo apt-get upgrade
sudo apt-get install lighttpd  
sudo apt-get install php5-common php5-cgi php5-cli php5-sqlite 
sudo lighty-enable-mod fastcgi-php  
sudo service lighttpd force-reload
sudo chown www-data:www-data /var/www  
sudo chmod 775 /var/www  
sudo apt-get install espeak
```
2. Copy the project in `/var/www`

3. Edit `cron/config.php` with your settings

4. Add `cron/cron.php` in crontab, executing `crontab -e` and adding
```
* * * * * php /var/www/cron/cron.php
```

5. Modify permission for writing Sqlite Db `api/db.sqlite3`
```
sudo chmod 777 /var/www/api/db.sqlite3
```