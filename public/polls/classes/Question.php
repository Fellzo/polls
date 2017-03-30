<?php

require_once "Database.php";

final class Question
{
    private $question = "";
    private $pollId = null;
    private $options = [];
    private $type;
    private $id = null;

    const ONE_ANSWER = 0;
    const MULTI_CHOICE = 1;

    public function __construct(string $question, int $type, array $options)
    {
        $this->question = $question;
        if (sizeof($options) == 0) {
            throw new Error("Question must contain at least one option.");
        }
        if ($this->type) {
            throw new Error("Unavailable type.");
        }
        $this->type = $type;
        foreach ($options as $option) {
            if (!($option instanceof Option)) {
                throw new Error("All objects in \$answers must be instance of class Option.");
            }
        }
        $this->options = $options;
    }

    public function bindToPoll(int $poll_id)
    {
        $this->pollId = $poll_id;
        return $this;
    }


    /**
     * @return int
     */
    public function getPollId(): int
    {
        return $this->pollId;
    }

    /**
     * @return string
     */
    public function getQuestion(): string
    {
        return $this->question;
    }

    public function bindOptions()
    {
        if (is_null($this->pollId)) {
            throw new Error("Poll is not created or question not binded.");
        }
        if (is_null($this->id)) {
            $this->id = Database::getInstance()->createQuestion($this);
        }
        foreach ($this->options as $option) {
            $option->bindToQuestion($this->id);
            $option->save();
        }
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): Question
    {
        $this->id = $id;
        return $this;
    }

}