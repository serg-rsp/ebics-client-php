## 2.1

* Up supported PHP version to >= 7.4
* Add `FUL`, `H3K`, `HAC`, `CDS` order type for EBICS 2.5
* Add methods `YCT`, `ZSR`, `Z54`, `HAC` order type for EBICS 3.0
* Major update for keyring manager. Added Array & File keyring managers instead of keyring.
* Add responseHandler into ClientInterface
* Add $storageCallback for download methods that handle acknowledge.
* Fix UTF-8 encoding.
* Added CurlHttpClient and PsrHttpClient to use standard client.
* Updated AbstractX509Generator to handle custom options.
* Improved Bank-letter
* Add CustomerServiceContainer for create Service XML-Container (SVC format)
* Add ability to select user signature A006
* Fix headers of EBICS 3.0 BTU request
