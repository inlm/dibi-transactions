<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$connection = Tests::createDb();
$transactions = new Inlm\DibiTransactions\Transactions($connection);

test('no transaction', function() use ($transactions) {
	Assert::exception(function () use ($transactions) {
		$transactions->commit();
	}, \Inlm\DibiTransactions\InvalidStateException::class, 'No transaction started.');

	Assert::exception(function () use ($transactions) {
		$transactions->rollback();
	}, \Inlm\DibiTransactions\InvalidStateException::class, 'No transaction started.');
});
