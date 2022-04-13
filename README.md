# PHP client library for VirusTotal Public API v3.0

This is an unofficial PHP client library for VirusTotal. With this library you can interact with the [VirusTotal REST API v3](https://developers.virustotal.com/v3.0/reference) and automate your workflow quickly and efficiently.

This is an upgraded version of [Virus Total Public API v2.0 PHP Wrapper](https://github.com/jayzeng/virustotal_apiwrapper).

## Things you can do with this sdk:
* Scan files and URLs
* Get information about files, URLs, domains, etc
* And much more ...

## Installation:
- You will need composer (http://getcomposer.org/)
- composer search `vt-php-api3` or visit the package info on packagist (https://packagist.org/packages/monaz/vt-php-api3)

Install using composer by running:
```
composer require monaz/vt-php-api3
```

Or include the following in your composer.json:
```json
{
  "require": {
    "monaz/vt-php-api3": "dev-master"
  }
}
```
Then run:
```
composer update
```

## Usage:
```php
<?php
require_once 'Vendors/autoload.php';

$apiKey = 'your_api_key';

// Scan file
$fileScanner = new \Monaz\VirusTotal\File($apiKey);
$resp = $fileScanner->scan('foo.txt');
$result = $fileScanner->getReport($resp['hash']);

// Scan Url
$urlScanner = new \Monaz\VirusTotal\Url($apiKey);
$resp = $urlScanner->scan('foo.txt');
$result = $urlScanner->getReport($resp['hash']);

// Get Domain Report
$domainScanner = new \Monaz\VirusTotal\Domain($apiKey);
$result = $domainScanner->getReport("domain.com");

// Get IP Report
$ipScanner = new \Monaz\VirusTotal\Ip($apiKey);
$result = $ipScanner->getReport("1.1.1.1");
?>
```

Since the helper scanners does not wrap all the available VirusTotal API endpoints, we have created request helpers that you can utilize to make direct requests.
```php
<?php
require_once 'Vendors/autoload.php';

$apiKey = 'your_api_key';

$baseClient = new \Monaz\VirusTotal\BaseClient($apiKey);

$baseClient->makePostRequest("endpoint", $payload, $type);
$baseClient->makeGetRequest("endpoint", $payload);
$baseClient->makePatchRequest("endpoint", $payload);
$baseClient->makeDeleteRequest("endpoint");
?>
```
The `$type` parameter in `makePostRequest` can be `form_data` and `multipart`. You can choose the required one based on VirusTotal API doc of the desired endpoint.


## Contributing
Thank you for considering contributing to the library! Just fork and when you are done make an PR. Just make sure you run the tests before submitting your request.

You can run the phpunit tests using this command:
```
"vendor/bin/phpunit" --coverage-text --configuration phpunit.xml.dist tests
```

## License
The library is open-sourced software licensed under the [MIT license](LICENSE.md).

