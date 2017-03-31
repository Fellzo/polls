<?php

require_once "Database.php";

final class Poll
{
    /**
     * @var string
     */
    private $name = "";
    private $description = "";
    /**
     * @var Question
     */
    private $questions = [];
    private $binded = false;
    private $id = null;

    const POLL_MODE = 0;
    const STATISTIC_MODE = 1;

    private $curr_mode = self::POLL_MODE;

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

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
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
        return $this;
    }

    public function saveAll()
    {
        $this->bindQuestions();
        return $this;
    }

    public function render(): string
    {
        $questions = "";
        foreach ($this->questions as $question) {
            $question_id = $question->getId();
            $question_text = $question->getQuestion();

            $questions .= "<div id='questiond_{$question_id}' class='question_text'>{$question_text}</div>";
            $hidden = $this->curr_mode == self::STATISTIC_MODE? "hidden" : "";
            $questions .= "<ul class='question' id='{$question_id}' {$hidden}>";
            if ($this->curr_mode == self::STATISTIC_MODE) {
                $question_statistic = Database::getInstance()->getQuestionStatistic($question_id);
            }
            foreach ($question->getOptions() as $option) {
                $option_html = $option->render($this->curr_mode);
                if ($this->curr_mode == self::STATISTIC_MODE) {
                    $option_statistic = $question_statistic[$option->getValue()];
                }
                $questions .= "<li option_num='{$option_statistic}'>
                                    {$option_html}     
                               </li>";
            }
            $questions .= "</ul>
                            <div id='chart_{$question_id}'></div>
                            <hr>";
        }
        $html = "
        <div id='poll_{$this->id}'>
        <h1>{$this->name}</h1>
        <div class='poll_description' id='description_{$this->id}'>{$this->description}</div>
        <hr>
        <form method='post'>
            <input type='hidden' value='{$this->id}' name='poll[poll_id]'>
            {$questions}
        ";
        if ($this->curr_mode == self::POLL_MODE) {
            $html .= "<button>Отправить ответы</button>";
        }
        $html .= "</form></div>";
        return $html;
    }

    public function isValid(array $data): bool
    {
        if (!is_numeric($data["poll_id"]) || $this->id != intval($data["poll_id"])) {
            return false;
        }
        foreach ($data["answers"] as $key => $val) {
            if (!is_numeric($key) || !is_numeric($val)) {
                return false;
            }
        }
        return sizeof($data["answers"]) == sizeof($this->questions);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setCurrMode(int $mode = self::POLL_MODE)
    {
        if ($mode == self::POLL_MODE or $mode == self::STATISTIC_MODE) {
            $this->curr_mode = $mode;
            return $this;
        } else {
            throw new Error("Wrong mode.");
        }
    }
}