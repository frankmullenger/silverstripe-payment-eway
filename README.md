# SilverStripe Payment Eway Module

**Work in progress, some changes to the API still to come**

## Maintainer Contacts
*  [Frank Mullenger](https://github.com/frankmullenger)

## Requirements
* SilverStripe 3.0.x
* Payment module 1.0.x

## Documentation
Paystation integration for payment module. This module currently supports [Rapid 3.0 processing](http://www.eway.co.nz/developers/api/rapid-3-0) only. The Rapid 3.0 API works by using a form hosted on the merchants website to capture the credit card details, this form is posted directly to eWay. 

### Developer documentation
How to [get started with the Rapid 3.0 API](https://eway.zendesk.com/entries/22370486-how-to-generate-your-sandbox-rapid-3-0-api-key-and-password).  
[Rapid 3.0 API documentation](http://www.eway.co.nz/docs/api-documentation/rapid3-0documentation.pdf).  
[Gateway response codes](http://www.eway.com.au/developers/resources/response-codes).  
[How to trigger response codes from the gateway using cents values](https://eway.zendesk.com/entries/23054328-I-m-testing-in-sandbox-why-are-my-payments-declined-).  

## Installation Instructions
1. Place this directory in the root of your SilverStripe installation and call it 'payment-eway'.
2. Visit yoursite.com/dev/build?flush=1 to rebuild the database.

**Note:** Because the credit card details are entered by the customer on a page residing on your website (and not a page on the gateway) ensure you have an SSL/TLS cert installed for security.

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

By default the gateway class can accept NZD or AUD (see RapidGateway::$supportedCurrencies). Usually your Eway account will be for a single currency that matches your merchant account. To specify this currency as the single acceptable currency alter the YAML config file e.g: a configuration that will only process payments in Australian dollars:

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

    # Set supported currency
    supported_currencies:
      'AUD' : 'Australian Dollar'
  dev:
    # User credentials
    Payment.Username: ""
    Payment.Password: ""

    #Method Options: SOAP,POST,REST,RPC
    "Request:Method": 'SOAP'

    #Format Options: JSON, XML
    "Request:Format": 'JSON'

    # Set supported currency
    supported_currencies:
      'AUD' : 'Australian Dollar'
```

**Note:** Remember to ?flush=1 after changes to the config YAML files.


## TODO

* Translation support
* Unit tests
* Capture customer and order information and pass to the gateway
* Test error responses in RapidGateway::process()


