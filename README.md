# Matomo Provider Plugin

[![Build Status](https://github.com/matomo-org/plugin-Provider/actions/workflows/matomo-tests.yml/badge.svg?branch=4.x-dev)](https://github.com/matomo-org/plugin-Provider/actions/workflows/matomo-tests.yml)

## Description

This plugin adds a new report to your Matomo showing which Internet Service Providers your visitors used to access your website. 
You can click on a provider name for more details. If Matomo can't determine a visitor's provider, it is listed as IP.

## Configuration

Add the following to `config/config.ini.php` to change the plugin behaviour:

```ini
[Provider]
; Whether to skip the DNS reverse lookup when the configured location provider (eg an ISP or
; ASN database) doesn't return any ISP or organisation information for a visitor's IP.
; The lookup is a blocking, uncached DNS request performed within the tracking request, and can
; add several seconds to it whenever no PTR record exists or the resolver is slow to answer.
; Set to 0 to perform the lookup anyway, at the cost of slower tracking requests.
skip_reverse_dns_lookup = 1
```
