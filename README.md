# Compralo payment plugin for Magento 2.x
Use Compralo's plugin for Magento to offer mobile payments online in your e-commerce.

## Integration
The plugin integrates Magento store with payments on Compralo App.

## Requirements
The plugin supports the Magento (version 2 and higher). 

## Collaboration
We commit all our new features directly into our GitHub repository.
But you can also request or suggest new features or code changes yourself!

## Support
Open new issue [https://github.com/santanamic/m2-compralo/issues](https://github.com/santanamic/m2-compralo/issues).

## Installation

Use composer:
```
composer require santanamic/m2-compralo/
```

After:
```
php bin/magento setup:upgrade
```

## API Documentation
##### - [Compralo registration page](https://ecommerce.picpay.com/)

##### - [Compralo API documentation](https://app.compralo.io/register)

## Caching / Varnish configuration
In case you are using a caching layer such as Varnish, please exclude the following URL pattern from being cached
```
/compralo/*
```

## License
MIT license. For more information, see the LICENSE file.