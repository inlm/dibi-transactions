
# Inlm\DibiTransactions

Nested transactions for Dibi.

<a href="https://www.patreon.com/bePatron?u=9680759"><img src="https://c5.patreon.com/external/logo/become_a_patron_button.png" alt="Become a Patron!" height="35"></a>
<a href="https://www.paypal.me/janpecha/1eur"><img src="https://buymecoffee.intm.org/img/button-paypal-white.png" alt="Buy me a coffee" height="35"></a>


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
$transactions->transactional(function () use ($connection) {
	$connection->query('...');
	$connection->query('...');
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
