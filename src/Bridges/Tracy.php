<?php

	declare(strict_types=1);

	namespace Inlm\DibiTransactions\Bridges;

	use Inlm\DibiTransactions\UnresolvedTransactionException;


	class Tracy
	{
		/**
		 * @return void
		 */
		public static function logUnresolved(UnresolvedTransactionException $exception)
		{
			\Tracy\Debugger::log($exception, \Tracy\Debugger::EXCEPTION);
		}
	}
