<?php

namespace Cyclonecode\NewsDataIO\Interfaces;

interface NewsApiInterface
{
    public function getNews(array $args = []): ?NewsResponseInterface;
}
