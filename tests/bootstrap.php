<?php

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();


@mkdir(__DIR__ . '/tmp');  # @ - directory may already exist
define('TEMP_DIR', __DIR__ . '/tmp/' . getmypid());
Tester\Helpers::purge(TEMP_DIR);


function test($description, callable $cb)
{
	$cb();
}


class Tests
{
	public function __construct()
	{
		throw new \RuntimeException('This is static class.');
	}


	/**
	 * @return Dibi\Connection
	 */
	public static function createDb()
	{
		$dbFile = TEMP_DIR . '/db-' . Nette\Utils\Random::generate(10) . '.sq3';
		touch($dbFile);
		$connection = new Dibi\Connection([
			'driver' => 'sqlite3',
			'file' => $dbFile,
		]);

		return $connection;
	}


	public static function resetDb(Dibi\Connection $connection)
	{
		Dibi\Helpers::loadFromFile($connection, __DIR__ . '/Transactions/books.sql');
	}
}


class Books
{
	/** @var Dibi\Connection */
	private $connection;


	public function __construct(Dibi\Connection $connection)
	{
		$this->connection = $connection;
	}


	public function resetDb()
	{
		Dibi\Helpers::loadFromFile($this->connection, __DIR__ . '/Transactions/books.sql');
	}


	/**
	 * @return int
	 */
	public function count()
	{
		return (int) $this->connection->query('SELECT COUNT(*) FROM [book]')->fetchSingle();
	}


	/**
	 * @param  string $title
	 * @return void
	 */
	public function insert($name)
	{
		$this->connection->query('INSERT INTO [book]', [
			'name' => $name,
		]);
	}
}
