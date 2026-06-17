<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;
use App\Ai\Tools\SearchProducts;
use App\Ai\Tools\GetProductDetails;
use App\Ai\Tools\ListCategories;
use App\Ai\Tools\GetProductStatistics;

class SupportBot implements Agent, Conversational, HasTools
{
    use Promptable;

    public function model(): string
    {
        return 'openai/gpt-oss-120b:free';
    }
    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are a helpful assistant.';
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new SearchProducts,
            new GetProductDetails,
            new ListCategories,
            new GetProductStatistics,
        ];
        // return [];
    }
}
