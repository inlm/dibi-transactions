<?php

declare(strict_types=1);

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

$connection = Tests::createDb();
$books = new Books($connection);
$transactions = new Inlm\DibiTransactions\Transactions($connection);

test('fail', function() use ($books, $transactions) {
	$books->resetDb();
	Assert::same(3, $books->count());

	$transaction = $transactions->createTransaction();

	try {
		$books->insert('Test book');
		throw new \Exception('my exception');

	} catch (Exception $e) {
		$transaction->rollback();
		Assert::same('my exception', $e->getMessage());
	}

	Assert::same(3, $books->count());
});


test('success', function() use ($books, $transactions) {
	$books->resetDb();
	Assert::same(3, $books->count());

	$transaction = $transactions->createTransaction();

	try {
		$books->insert('Test book');
		$transaction->commit();

	} catch (Exception $e) {
		$transaction->rollback();
		Assert::same('my exception', $e->getMessage());
	}

	Assert::same(4, $books->count());
});


test('commit & resolved error', function() use ($books, $transactions) {
	$books->resetDb();
	Assert::same(3, $books->count());

	$transaction = $transactions->createTransaction();
	$books->insert('Test book');
	$transaction->commit();

	Assert::same(4, $books->count());

	Assert::exception(function () use ($transaction) {
		$transaction->commit();
	}, \Inlm\DibiTransactions\InvalidStateException::class, 'Transaction is already resolved.');

	Assert::exception(function () use ($transaction) {
		$transaction->rollback();
	}, \Inlm\DibiTransactions\InvalidStateException::class, 'Transaction is already resolved.');
});


test('rollback & resolved error', function() use ($books, $transactions) {
	$books->resetDb();
	Assert::same(3, $books->count());

	$transaction = $transactions->createTransaction();
	$books->insert('Test book');
	$transaction->rollback();

	Assert::same(3, $books->count());

	Assert::exception(function () use ($transaction) {
		$transaction->commit();
	}, \Inlm\DibiTransactions\InvalidStateException::class, 'Transaction is already resolved.');

	Assert::exception(function () use ($transaction) {
		$transaction->rollback();
	}, \Inlm\DibiTransactions\InvalidStateException::class, 'Transaction is already resolved.');
});


test('unresolved', function() use ($books, $transactions) {
	$books->resetDb();
	Assert::same(3, $books->count());

	$reports = 0;
	$transactions->onUnresolved[] = function (Inlm\DibiTransactions\UnresolvedTransactionException $e) use (&$reports) {
		$reports++;
	};

	$transaction = $transactions->createTransaction();
	$books->insert('Test book');

	Assert::error(function () use (&$transaction) {
		$transaction = NULL;
	}, E_USER_NOTICE, 'Unresolved transaction!');

	Assert::same(1, $reports);
});
