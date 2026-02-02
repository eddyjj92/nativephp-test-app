<?php

namespace App\DTOs;

class FrequentlyAskedQuestionDTO
{
    public function __construct(
        public string $question,
        public string $answer,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            question: $data['question'],
            answer: $data['answer'],
        );
    }
}