<?php

interface Louable
{
    public function louer(float $loyerMensuel): string;
    public function resilier(): string;
    public function isLoue(): bool;
    public function getLoyerMensuel(): float;
}
