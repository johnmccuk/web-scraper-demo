# web-scraper-demo
A web scraping technical demo by [John McCracken](https://blog.john-mccracken.com)

Pulls web links from a test site and returns a json formatted array of meta data for listed urls listed under a specific tag.

See the example file ***example-cli.php*** for usage.

## Requirements
***PHP Curl***

See your specific operating system package manager

***Composer*** 

`curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer`


## Install

```
git clone https://github.com/johnmccuk/web-scraper-demo.git
cd web-scraper-demo
composer install
```

***Note:*** If you dont want the development libraries use `composer install --no-dev`

## Run

From the command line

`php -f example-cli.php`

## Test

`vendor/bin/phpunit tests/`

