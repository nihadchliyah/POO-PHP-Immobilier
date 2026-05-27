<?php

class BienFactory
{
    private static array $createurs = [];

    public static function enregistrerType(string $type, callable $createur): void
    {
        self::$createurs[$type] = $createur;
    }

    public static function creerDepuisTableau(array $data): BienImmobilier
    {
        $type = $data['type'] ?? '';

        if (!isset(self::$createurs[$type])) {
            throw new InvalidArgumentException("Type de bien inconnu : {$type}.");
        }

        return (self::$createurs[$type])($data);
    }
}
