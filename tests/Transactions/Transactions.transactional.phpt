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

	Assert::exception(function () use ($books, $transactions) {
		$transactions->transactional(function () use ($books) {
			$books->insert('Test book');
			throw new Exception('my exception');
		});
	}, \Exception::class, 'my exception');

	Assert::same(3, $books->count());
});


test('success', function() use ($books, $transactions) {
	$books->resetDb();
	Assert::same(3, $books->count());

	$transactions->transactional(function () use ($books) {
		$books->insert('Test book');
	});

	Assert::same(4, $books->count());
});


test('success & return value', function() use ($books, $transactions) {
	$books->resetDb();
	Assert::same(3, $books->count());

	$value = $transactions->transactional(function () use ($books) {
		$books->insert('Test book');
		return 'my value';
	});

	Assert::same(4, $books->count());
	Assert::same('my value', $value);
});


test('nested fail', function() use ($books, $transactions) {
	$books->resetDb();
	Assert::same(3, $books->count());

	Assert::exception(function () use ($books, $transactions) {
		$transactions->transactional(function () use ($books, $transactions) {
			$books->insert('Test book');

			$transactions->transactional(function () use ($books) {
				$books->insert('Test book 2');
				throw new \Exception('my nested exception');
			});
		});
	}, \Exception::class, 'my nested exception');

	Assert::same(3, $books->count());
});


test('nested success', function() use ($books, $transactions) {
	$books->resetDb();
	Assert::same(3, $books->count());

	$transactions->transactional(function () use ($books, $transactions) {
		$books->insert('Test book');

		$transactions->transactional(function () use ($books) {
			$books->insert('Test book 2');
		});
	});

	Assert::same(5, $books->count());
});


test('nested save points', function() use ($books, $transactions) {
	$books->resetDb();
	Assert::same(3, $books->count());

	$transactions->transactional(function () use ($books, $transactions) {
		$books->insert('Test book');

		try {
			$transactions->transactional(function () use ($books) {
				$books->insert('Test book 2');

				throw new \Exception('my nested exception');
			});
		} catch (\Exception $e) {
			Assert::same('my nested exception', $e->getMessage());
		}
	});

	Assert::same(4, $books->count());
});
