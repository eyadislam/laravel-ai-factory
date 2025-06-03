<?php

namespace FlorianDomgjoni\AIFactory\Tests\Database\Models;

use FlorianDomgjoni\AIFactory\Tests\Database\Factories\DummyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dummy extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function newFactory()
    {
        return DummyFactory::new();
    }
}
