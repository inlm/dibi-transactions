# Inlm\DibiTransactions

[![Build Status](https://github.com/inlm/dibi-transactions/workflows/Build/badge.svg)](https://github.com/inlm/dibi-transactions/actions)
[![Downloads this Month](https://img.shields.io/packagist/dm/inlm/dibi-transactions.svg)](https://packagist.org/packages/inlm/dibi-transactions)
[![Latest Stable Version](https://poser.pugx.org/inlm/dibi-transactions/v/stable)](https://github.com/inlm/dibi-transactions/releases)
[![License](https://img.shields.io/badge/license-New%20BSD-blue.svg)](https://github.com/inlm/dibi-transactions/blob/master/license.md)

Nested transactions for Dibi.

<a href="https://www.janpecha.cz/donate/"><img src="https://buymecoffee.intm.org/img/donate-banner.v1.svg" alt="Donate" height="100"></a>


## Installation

[Download a latest package](https://github.com/inlm/dibi-transactions/releases) or use [Composer](http://getcomposer.org/):

```
composer require inlm/dibi-transactions
```

Inlm\Dibi-transactions requires PHP 5.6.0 or later and [Dibi](https://dibiphp.com).


## Usage

``` php
$connection = new Dibi\Connection();
$transactions = new Inlm\DibiTransactions\Transactions($connection);
```

### `transactional()`

``` php
$value = $transactions->transactional(function () use ($connection) {
	$connection->query('...');
	$connection->query('...');
	return $value;
});
```

### `Transaction`

``` php
$transaction = $transactions->createTransaction();

try {
	$connection->query('...');
	$connection->query('...');
	$transaction->commit();

} catch (Exception $e) {
	$transaction->rollback();
	throw $e;
}
```

Object `Transaction` throws error if you forget to call `commit()` or `rollback()`. It can be connected with [Tracy](https://tracy.nette.org/):

``` php
$transactions->onUnresolved[] = array(Inlm\DibiTransactions\Bridges\Tracy::class, 'logUnresolved');
```


### `begin` / `commit` / `rollback`

``` php
try {
	$transactions->begin();
	$connection->query('...');
	$connection->query('...');
	$transactions->commit();

} catch (Exception $e) {
	$transactions->rollback();
}
```

------------------------------

License: [New BSD License](license.md)
<br>Author: Jan Pecha, https://www.janpecha.cz/
