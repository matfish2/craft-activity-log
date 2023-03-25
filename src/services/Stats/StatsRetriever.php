<?php

namespace matfish\ActivityLog\services\Stats;

class StatsRetriever
{
    protected string $name;
    protected array $filters;

    /**
     * @param string $name
     */
    public function __construct(string $name , array $filters)
    {
        $this->name = $name;
        $this->filters = $filters;
    }

    public function get()
    {
        $cls = $this->getClassName();

        return (new $cls($this->filters))->get();
    }

    private function getClassName(): string
    {
        $cls = $this->dashesToCamelCase($this->name);

        return "matfish\ActivityLog\services\Stats\Retrievers\\" . $cls;
    }

    private function dashesToCamelCase($string) : string
    {
        return str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
    }
}