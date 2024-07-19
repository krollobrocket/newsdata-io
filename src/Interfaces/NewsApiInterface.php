<?php

namespace Cyclonecode\NewsDataIO\Interfaces;

interface NewsApiInterface
{
    public function getNews(array $args = []): ?NewsResponseInterface;

    public function getCountryCodes(): array;

    public function getCategoryCodes(): array;

    public function getLanguageCodes(): array;
}
