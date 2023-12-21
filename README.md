
# Thielader Vindat

The `thielader/vindat` package provides a streamlined integration with DAT Group's vehicle data services. This package simplifies interactions with various SOAP-based web services offered by DAT Group, focusing primarily on VIN (Vehicle Identification Number) queries.

## Features

- Simple and easy-to-use interface for querying vehicle data.
- SOAP client integration for communicating with DAT Group's web services.
- Handles authentication and data retrieval seamlessly.

## Installation

Install the package via Composer:

```bash
composer require thielander/vindat
```

## Usage
Before using the DatGroupClient, you must have valid credentials from DAT Group. These credentials include customer number, customer login, customer password, interface partner number, and interface partner signature.

## Basic Usage
```php
<?php

require_once 'vendor/autoload.php';

use Thielander\Vindat\DatGroupClient;

$client = new DatGroupClient();

// Replace with a valid VIN
$vin = 'YOUR_VIN_NUMBER';

try {
    $vehicleData = $client->getVehicleData($vin);
    if ($vehicleData) {
        echo "Vehicle data retrieved successfully.";
    } else {
        echo "No data found for the provided VIN.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}


```

## Authentication
The package handles the authentication with DAT Group's services internally. However, you must provide your DAT Group credentials for the package to work properly.

## Contributing
Contributions to this package are welcome. Please create an issue or a pull request on GitHub if you find a bug or have a suggestion for an improvement.

## License
This package is released under the MIT License. See the LICENSE file for more information.

## Contact
For any questions or suggestions, feel free to reach out via email at mail@alexanderthiele.de.

## Disclaimer
This package is not officially affiliated with or endorsed by DAT Group. It is developed and maintained independently.

