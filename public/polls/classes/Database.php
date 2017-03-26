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
}

