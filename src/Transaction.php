<?php

	namespace Inlm\DibiTransactions;


	class Transaction
	{
		/** @var Transactions */
		private $transactions;

		/** @var UnresolvedTransactionException */
		private $exception;

		/** @var bool */
		private $resolved = FALSE;


		public function __construct(Transactions $transactions)
		{
			$this->transactions = $transactions;
			$this->exception = new UnresolvedTransactionException;
		}


		public function commit()
		{
			if ($this->resolved) {
				throw new InvalidStateException('Transaction is already resolved.');
			}

			$this->resolved = TRUE;
			$this->transactions->commit();
		}


		public function rollback()
		{
			if ($this->resolved) {
				throw new InvalidStateException('Transaction is already resolved.');
			}

			$this->resolved = TRUE;
			$this->transactions->rollback();
		}


		public function __destruct()
		{
			if (!$this->resolved) {
				$this->transactions->reportUnresolvedTransaction($this->exception);

				// destruktor nemuze vyhazovat vyjimky
				trigger_error('Unresolved transaction!', E_USER_NOTICE);
			}
		}
	}
