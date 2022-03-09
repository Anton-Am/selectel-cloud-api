# Selectel cloud api

A simple API wrapper for Selectel object storage.

### Installation

* **Using Composer**:

```sh
composer require anton-am/selectel-cloid-api
```

### Connecting

```php
require_once('vendor/autoload.php');

use AntonAm\Selectel\Cloud\Manager;

$key = 'ACCOUNT_ID_USER';
$secret = 'USER_PASSWORD';
$containerName = 'CONTAINER_NAME';

$client = new Manager($key, $secret, $containerName);
```

All available options:

###### Manager(REQUIRED KEY, REQUIRED SECRET, OPTIONAL CONTAINER NAME, OPTIONAL REGION, OPTIONAL HOST);

&nbsp;

### Uploading/Downloading Files

```php
$pathToFileInContainer = 'image.png';
$pathToFile = '/app/image.png';
$client->file($pathToFileInContainer)->setFileData($pathToFile)->create();


$downloadFile = 'image.png';
$saveAs = '/app/folder/downloaded-image.png';

$client->file($downloadFile)->download($saveAs);
```

&nbsp;

### Deleting Files/Folders

```php

$pathToFileInContainer = 'image.png';

$client->file($pathToFileInContainer)->delete();
```

&nbsp;