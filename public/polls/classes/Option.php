<?php


require_once "Database.php";

final class Option
{
    const ONE_ANSWER = 0;
    const MULTI_CHOICE = 1;

    private static $available_types = [0, 1];

    private $textOfOption = "";
    private $type;
    private $value;
    private $questionId = null;
    private $id = null;

    public function __construct(string $text, int $value, int $type = self::ONE_ANSWER)
    {
        if (mb_strlen($text) > 2000) {
            throw new Error("Length of answer text must be less of equal 2000 chars.");
        }
        $this->textOfOption = $text;
        if (array_search($type, self::$available_types) === false) {
            throw new Error("Unavailable type.");
        }
        $this->type = $type;
        $this->value = $value;
    }


    public function render(): string
    {
        $type = $this->type == self::ONE_ANSWER? "radio" : "checkbox";
        $checked = "";
        // Restore checked radio
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $checked = $_POST["poll"]["answers"][$this->questionId] == $this->id ? "checked" : "";
        }
        $template = "<input type='{$type}' id='option_{$this->id}' name='poll[answers][{$this->questionId}]' 
                     value='{$this->id}' {$checked}>
                     <label for='option_{$this->id}'>{$this->textOfOption}</label>";
        return $template;
    }

    /**
     * @return string
     */
    public function getTextOfOption(): string
    {
        return $this->textOfOption;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getQuestionId()
    {
        return $this->questionId;
    }

    public function bindToQuestion(int $question_id)
    {
        $this->questionId = $question_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function save()
    {
        $this->id = Database::getInstance()->createOption($this);
    }

    /**
     * @param int $id
     */
    public function setId($id): Option
    {
        $this->id = $id;
        return $this;
    }
}