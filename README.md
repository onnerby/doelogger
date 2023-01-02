# doelogger
A JSON logger for PHP
Sometimes you need to log stuff - but you may need to read the log later on and be able to intepret the content of the log in a nice way. This is where JSON is perfect since you can serialize and deserialize in a simple way.
The log uses file locks to prevent corrupt files.

## \Doe\Logger
The Doe\Logger is designer to make it super simple to log data and also to read the log.

## Installation
```
composer require onnerby/doelogger
```

### Basic example

```php
// Initialize the log by
\Doe\Logger::addDefaultStream('/var/log/mylog')

// The anywhere in your code
\Doe\Logger::write('adduser', 'Added a new user', $user);

```
This will make a log entry `adduser` with the data provided.
The logger will create a file at `/var/log/mylog_2023-01-02.json` (if it's not there already) and write a json entry in that log looking something like this
```json
{
	"when": 1672656212.123,
	"type": "adduser",
	"content": [
		...
	]
}
```

### Reading

```php
// Initialize the log by
\Doe\Logger::addDefaultStream('/var/log/mylog')

// Get a reader for the last 24 hours
$logReader = \Doe\Logger::reader('default', microtime(true) - 60*60*24);

// Loop through the log
foreach ($logReader as $logEntry) {
	echo "Log " . date('Y-m-d H:i:s', $logEntry['when']) . ' ' . $logEntry['type'] . "\n";
	echo "  User: " . json_encode($logEntry['content'][0]) . "\n";
}

```
