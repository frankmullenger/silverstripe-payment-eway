# SilverStripe Payment Eway Module

**Work in progress, some changes to the API still to come**

## Maintainer Contacts
*  [Frank Mullenger](https://github.com/frankmullenger)

## Requirements
* SilverStripe 3.0.x
* Payment module 1.0.x

## Documentation
Paystation integration for payment module. This module currently supports [Rapid 3.0 processing](http://www.eway.co.nz/developers/api/rapid-3-0) only.

## Installation Instructions
1. Place this directory in the root of your SilverStripe installation and call it 'payment-eway'.
2. Visit yoursite.com/dev/build?flush=1 to rebuild the database.

## Usage Overview
Enable in your application YAML config (e.g: mysite/_config/payment.yaml):

```yaml
PaymentGateway:
  environment:
    'dev'

PaymentProcessor:
  supported_methods:
    dev:
      - 'EwayRapid'
    live:
      - 'EwayRapid'
```
Configure using your Eway account details in the same file:

```yaml
RapidGateway:
  live:
    # User credentials
    Payment.Username: ""
    Payment.Password: ""

    #Method Options: SOAP,POST,REST,RPC
    "Request:Method": 'SOAP'

    #Format Options: JSON, XML
    "Request:Format": 'JSON'
  dev:
    # User credentials
    Payment.Username: ""
    Payment.Password: ""

    #Method Options: SOAP,POST,REST,RPC
    "Request:Method": 'SOAP'

    #Format Options: JSON, XML
    "Request:Format": 'JSON'

    # Set to 1 to see the response objects for CreateAccessCode & GetAccessCodeResult
    # Also, it is able to see the raw response/request messages in either JSON or XML format being sent to the RapidAPI End Point.
    ShowDebugInfo: 0
```

**Note:** Remember to ?flush=1 after changes to the config YAML files.

## TODO

Recording error responses from the gateway on Payment


