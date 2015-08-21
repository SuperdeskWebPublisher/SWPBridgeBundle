# Superdesk Bridge bundle
[![Build Status](https://travis-ci.org/SuperdeskWebPublisher/SWPBridgeBundle.svg?branch=master)](https://travis-ci.org/SuperdeskWebPublisher/SWPBridgeBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SuperdeskWebPublisher/SWPBridgeBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SuperdeskWebPublisher/SWPBridgeBundle/?branch=master)
[![Code Climate](https://codeclimate.com/github/SuperdeskWebPublisher/SWPBridgeBundle/badges/gpa.svg)](https://codeclimate.com/github/SuperdeskWebPublisher/SWPBridgeBundle)

This bundle is a bridge between the Superdesk Content API and the Superdesk Web Publisher.

## Installation

1. Download SWPBridgeBundle
2. Enable the bundle and its dependencies
3. Import SWPUpdaterBundle routing file
4. Configure the SWPBridgeBundle

### Step 1: Install SWPBridgeBundle with Composer

Run the following composer require command:

``` bash
$ php composer.phar require swp/bridge-bundle
```

### Step 2: Enable the bundle and its dependencies

Enable the bundle in `AppKernel.php`.

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new SWP\BridgeBundle\SWPBridgeBundle()
    );
}
```

### Step 3: Import SWPBridgeBundle routing file

You have to import SWPBridgeBundle routing file. You can use YAML or XML format.

YAML:

``` yaml
# app/config/routing.yml
swp_bridge:
    resource: "@SWPBridgeBundle/Resources/config/routing.yml"
    prefix:   /
```

### Step 4: Configure the SWPBridgeBundle

Add the following parameter to your `parameters.yml` file.

```yaml
# app/config/parameters.yml
parameters:
    swp_bridge.base_uri: 'http://example.com:5050' 
```

Change the hostname to the one of your content api instance.

#### Adding custom http client options:

SWPBridgeBundle uses Guzzle to fetch data from the external server. You can add
custom Guzzle options / headers for your http client by simply adding an array
of options as a parameter in your configuration.  
The example below shows how to add custom curl options.

```yaml
# app/config/parameters.yml
parameters:
    swp_bridge.options:
        curl: # http://guzzle.readthedocs.org/en/latest/faq.html#how-can-i-add-custom-curl-options
            10203: # integer value of CURLOPT_RESOLVE
                 - "example.com:5050:localhost"  # This will resolve the host example.com to your localhost 
```

For more details see [Guzzle documentation](http://guzzle.readthedocs.org/en/latest/request-options.html).

At this stage, the bundle is ready to be used by your application.

##### Development Configuration

The above example is specific for the Guzzle client and allows you to do custom
hostname resolving, practical when using docker in your devevelopment environment.
Just add ```127.0.0.1    publicapi``` to your hosts file and change the value 
_localhost_ to the ip address of you publicapi docker container.
