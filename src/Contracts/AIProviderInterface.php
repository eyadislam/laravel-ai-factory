<?php

namespace FlorianDomgjoni\AIFactory\Contracts;

interface AIProviderInterface
{
    public function generateBulk(array $fields, int $count): array;
}
