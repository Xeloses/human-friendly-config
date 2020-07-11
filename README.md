# Human-friendly config files support for PHP.
Human-friendly config file parser for PHP

Allows to use config files with pairs `Option name = value`

Every pair should be on a new line.

*Similar to INI files, but without groups.*

Also commentaries allowed:
* `# this is a comment`
* `// this is a comment too`

# Usage
**Example config file:**
```
# User data:
User name = Suprauser
Address = London, New Avenue, 77
Phone number = 990-123-45-67
```
Every option name will be converted to valid lower case attribute name where all spaces between words replaced with underscores.
```php
use Xeloses\HumanFriendlyConfig\ConfigFile;

$config = ConfigFile('../conf/user.info');
echo "User: {$config->user_name} ({$config->phone_number}); {$config->address}.";
```
Change or add new values:
```php
$config->user_name .= 'notRoot';
$config->email = 'suprauser@myhost.com';

$config->save();
```
Get value with "default" option:
```php
echo 'Age: '.$config->get('age','unknown');
```
Config can be saved to a new file:
```php
$config->saveAs('../conf/backup/user.info');
```
