Item Syncer
===========

[![](http://poser.pugx.org/gupalo/item-syncer/version)](https://packagist.org/packages/gupalo/item-syncer)
[![](http://poser.pugx.org/gupalo/item-syncer/require/php)](https://packagist.org/packages/gupalo/item-syncer)
[![](https://img.shields.io/packagist/dt/gupalo/item-syncer)](https://packagist.org/packages/gupalo/item-syncer)
![](https://img.shields.io/github/last-commit/gupalo/item-syncer/main)
![](https://img.shields.io/github/actions/workflow/status/gupalo/item-syncer/test.yaml?branch=main)

Sync remote to local items.

## Install

```bash
composer require gupalo/item-syncer
```

## How to use

Create 2 arrays with items implementing `\Gupalo\ItemSyncer\SyncableEntityInterface`:

* `remoteItems`: usually from external API - source of truth
* `localItems`: items from your DB

Update logic:

* remote item that is missing locally - create
* remote item that exists locally - update (you implement logic which properties should be updated)
* local item that is missing remotely - you decide by selecting sync method:
  * `syncKeeping`: don't do anything
  * `syncArchiving`: if local item has method `archive` then archive local item
  * `syncRemoving`: remove local items

## Example

```php
$remoteItems = array_map(
    static fn(array $a) => Country::createFromApi($a),
    $this->countryApiClient->getCountries()
);
$localItems = $this->countryRepository->findAll();
$syncResult = $this->itemSyncer->syncArchiving($remoteItems, $localItems);

print_r($syncResult->stat()); // something like ['created' => 2, 'updated' => 180]
```
