<?php

namespace Cyclonecode\NewsDataIO\Interfaces;

interface ArticleInterface
{
    public function getTitle(): string;
    public function getContent(): string;
}
