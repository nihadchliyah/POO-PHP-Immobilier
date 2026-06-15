<?php

interface ObservableInterface
{
    public function attach(ObserverInterface $observer): void;

    public function detach(ObserverInterface $observer): void;

    public function notifyObservers(string $event, array $data): void;
}
