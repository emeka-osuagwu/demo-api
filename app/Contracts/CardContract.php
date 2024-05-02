<?php

namespace App\Contracts;

interface CardContract
{
    /*
	|--------------------------------------------------------------------------
	| Add Card
	|--------------------------------------------------------------------------
	*/
    public function fetchTransactionByReference(array $payload): array;

    // public function chargeCard(array $data): array;

	public function getProviderName(): string;
}
