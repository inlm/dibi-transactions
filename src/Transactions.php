<?php

	namespace Inlm\DibiTransactions;


	class Transactions
	{
		/** @var callback[] */
		public $onUnresolved;

		/** @var \Dibi\Connection */
		protected $connection;

		/** @var int */
		protected $level = 0;

		/** @var bool */
		protected $onlyRollback = FALSE;

		/** @var bool */
		protected $enabledSavePoints;


		public function __construct(\Dibi\Connection $connection)
		{
			$this->connection = $connection;
			$this->enabledSavePoints = $this->supportsSavePoints($connection->getDriver());
		}


		/**
		 * @return Transaction
		 */
		public function createTransaction()
		{
			$this->begin();
			return new Transaction($this);
		}


		/**
		 * @return void
		 */
		public function begin()
		{
			if ($this->onlyRollback) {
				throw new InvalidStateException('Only rollback is enabled.');
			}

			if ($this->level === 0) {
				$this->connection->begin();
				$this->onlyRollback = FALSE;

			} elseif ($this->enabledSavePoints) {
				$this->connection->begin('LEVEL' . $this->level);
			}

			$this->level++;
		}


		/**
		 * @return void
		 */
		public function commit()
		{
			if ($this->onlyRollback) {
				throw new InvalidStateException('Only rollback is enabled.');
			}

			if ($this->level === 0) {
				throw new InvalidStateException('No transaction started.');
			}

			$this->level--;

			if ($this->level === 0) {
				$this->connection->commit();

			} elseif ($this->enabledSavePoints) {
				$this->connection->commit('LEVEL' . $this->level);
			}
		}


		/**
		 * @return void
		 */
		public function rollback()
		{
			if ($this->level === 0) {
				throw new InvalidStateException('No transaction started.');
			}

			$this->level--;

			if ($this->level === 0) {
				$this->connection->rollback();
				$this->onlyRollback = FALSE;

			} elseif ($this->enabledSavePoints) {
				$this->connection->rollback('LEVEL' . $this->level);

			} else {
				$this->onlyRollback = TRUE;
			}
		}


		/**
		 * @param  callable
		 * @return void
		 */
		public function transactional($callable)
		{
			$this->begin();

			try {
				call_user_func($callable);
				$this->commit();

			} catch (\Exception $e) {
				$this->rollback();
				throw $e;
			}
		}


		/**
		 * @internal
		 */
		public function reportUnresolvedTransaction(UnresolvedTransactionException $exception)
		{
			if (!is_array($this->onUnresolved)) {
				return;
			}

			foreach ($this->onUnresolved as $callback) {
				call_user_func($callback, $exception);
			}
		}


		/**
		 * @return bool
		 */
		protected function supportsSavePoints(\Dibi\Driver $driver)
		{
			if ($driver instanceof \Dibi\Drivers\MySqlDriver) {
				return TRUE;
			}

			if ($driver instanceof \Dibi\Drivers\MySqliDriver) {
				return TRUE;
			}

			if ($driver instanceof \Dibi\Drivers\PostgreDriver) {
				return TRUE;
			}

			if ($driver instanceof \Dibi\Drivers\Sqlite3Driver) {
				return TRUE;
			}

			return FALSE;
		}
	}
