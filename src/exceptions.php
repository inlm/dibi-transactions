<?php

	declare(strict_types=1);

	namespace Inlm\DibiTransactions;


	class Exception extends \Exception
	{
	}


	class InvalidStateException extends Exception
	{
	}


	class UnresolvedTransactionException extends Exception
	{
	}
