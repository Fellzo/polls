<?php

require_once "Database.php";

final class Poll
{

    private $name = "";
    private $description = "";
    private $questions = [];
    private $binded = false;
    private $id = null;

    public function __construct(string $name, string $description, array $questions)
    {
        $this->name = $name;
        if (mb_strlen($description) > 2000) {
            throw new Error("Length of description must be less of equal 2000 chars.");
        }
        $this->description = $description;
        if (sizeof($questions) == 0) {
            throw new Error("Poll must contain at least one question.");
        }
        foreach ($questions as $question) {
            if (!($question instanceof Question)) {
                throw new Error('All objects in $questions must be instance of class Question');
            }
        }
        $this->questions = $questions;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    private function bindQuestions()
    {
        if (is_null($this->id)) {
            $this->id = Database::getInstance()->createPoll($this);
        }
        foreach ($this->questions as $question) {
            $question->bindToPoll($this->id);
            $question->bindOptions();
        }
        $this->binded = true;
    }

    public function saveAll()
    {
        $this->bindQuestions();
    }
}