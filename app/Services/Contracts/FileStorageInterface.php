<?php
namespace App\Services\Contracts;

interface FileStorageInterface
{
    public function storeVideo($file): array;
    public function storeFile($file): array;
}

?>