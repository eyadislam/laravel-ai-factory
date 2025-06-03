<?php

namespace FlorianDomgjoni\AIFactory\Concerns;

use FlorianDomgjoni\AIFactory\Facades\AIFactory;
use Illuminate\Support\Facades\Log;

trait HasAIFactory
{
    public function createWithAI(array $overrides = [], bool $bulk = false, ?string $driver = null)
    {
        $count = $this->count ?? 1;

        if (! $this->model) {
            throw new \Exception('You must define a $model property in your factory.');
        }

        if (! method_exists($this, 'aiFields')) {
            throw new \Exception('You must define an aiFields() method in your factory.');
        }

        $fields = array_merge($this->aiFields(), $overrides);

        $prompts = [];
        $manualFields = [];

        foreach ($fields as $key => $value) {
            if (is_string($value)) {
                $prompts[$key] = $value;
            } elseif (is_callable($value)) {
                $manualFields[$key] = $value;
            } else {
                throw new \InvalidArgumentException("Invalid value for key '{$key}'. Must be a string (AI prompt) or callable (manual).");
            }
        }

        $driver ??= config("ai-factory.defaults.{$this->model}", config('ai-factory.driver'));
        $aiData = AIFactory::driver($driver)->generateBulk($prompts, $count);

        $finalData = [];

        foreach ($aiData as $index => $row) {
            foreach ($manualFields as $key => $callback) {
                try {
                    $row[$key] = $callback();
                } catch (\Throwable $e) {
                    Log::warning("[AI Factory] Manual field '{$key}' failed at index {$index}: {$e->getMessage()}");
                    $row[$key] = null;
                }
            }

            $finalData[] = $row;
        }

        if ($bulk) {
            // Bulk insert (no events, fastest)
            $this->model::query()->insert($finalData);

            return collect();
        }

        // One-by-one creation (fires events)
        $createdModels = collect();

        foreach ($finalData as $index => $row) {
            try {
                $createdModels->push($this->model::query()->create($row));
            } catch (\Throwable $e) {
                Log::error("[AI Factory] Failed to create model at index {$index}", [
                    'model' => $this->model,
                    'row' => $row,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $createdModels;
    }
}
