<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

final class Database
{
    private static $instance = null;
    private static $capsule;

    private function __construct()
    {
        require_once "../../vendor/autoload.php";
        self::$capsule = new Capsule();
        $cfg = (require_once "config.php")["db_config"];
        self::$capsule->addConnection($cfg);
        self::$capsule->setEventDispatcher(new Dispatcher(new Container));
        self::$capsule->setAsGlobal();
        self::$capsule->bootEloquent();
    }

    public static function getInstance(): Database
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function createPoll(Poll $poll): int
    {
        return (self::$capsule)::table("polls")->insertGetId([
            "name" => $poll->getName(), "description" => $poll->getDescription()]);
    }

    public function createQuestion(Question $question): int
    {
        if (is_null($question->getPollId())) {
            throw new Error("Question must bind to poll.");
        }
        return (self::$capsule)::table("questions")->insertGetId([
            "poll_id" => $question->getPollId(), "question" => $question->getQuestion(), "type" => $question->getType()]);
    }

    public function createOption(Option $option): int
    {
        if (is_null($option->getQuestionId())) {
            throw new Error("Question must bind to poll.");
        }
        return (self::$capsule)::table("options")->insertGetId([
            "question_id" => $option->getQuestionId(), "answer" => $option->getTextOfOption(), "value" => $option->getValue()]);
    }


    public function getFullPollData(int $pollId): Poll
    {
        $pollData = (self::$capsule)::table("polls")->select()->where('id', '=', $pollId)->first();
        if (is_null($pollData)) {
            throw new Error("Poll not found");
        }
        $questionsData = (self::$capsule)::table("questions")->select()->where("poll_id", "=", $pollData->id)->get();
        $questions = [];
        foreach ($questionsData as $questionData) {
            $options = [];
            $optionsData = $this->getQuestionOptions($questionData->id);
            foreach ($optionsData as $optionData) {
                $options[] = (new Option($optionData->answer, $optionData->value, $questionData->type))
                    ->bindToQuestion($questionData->id)->setId($optionData->id);
            }
            $questions[] = (new Question($questionData->question, $questionData->type, $options))->setId($questionData->id);
        }
        return (new Poll($pollData->name, $pollData->description, $questions))->setId($pollData->id);
    }

    public function saveAnswers(array $data): bool
    {
        $poll_id = intval($data["poll_id"]);
        $answers = [];
        foreach ($data["answers"] as $key => $val) {
            $answers[] = ["poll_id" => $poll_id, "question_id" => intval($key), "option_id" => intval($val)];
        }
        foreach ($answers as $answer) {
            (self::$capsule)::table("answers")->insert($answer);
        }
        return true;
    }

    public function getQuestionOptions(int $questionId)
    {
        return (self::$capsule)::table("options")->where("question_id", "=", $questionId)->get();
    }

    public function getQuestionStatistic(int $questionId)
    {
        $options = $this->getQuestionOptions($questionId);
        $statistic = [];
        foreach ($options as $option) {
            $statistic[$option->value] =
                sizeof((self::$capsule)::table("answers")->where("option_id", "=", $option->id)->get());
        }
        return $statistic;
    }
}

