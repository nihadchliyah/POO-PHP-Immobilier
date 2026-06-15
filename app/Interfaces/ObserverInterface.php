<?php

interface ObserverInterface
{
    public function update(string $event, array $data): void;
}
