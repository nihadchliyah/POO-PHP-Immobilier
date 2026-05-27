<?php

class JsonDataSource implements DataSourceInterface
{
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function recupererDonnees(): array
    {
        if (!file_exists($this->filePath)) {
            throw new RuntimeException("Le fichier {$this->filePath} est introuvable.");
        }

        $json = file_get_contents($this->filePath);
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("Erreur lors du décodage du JSON : " . json_last_error_msg());
        }

        return $data;
    }
}
