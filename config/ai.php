<?php

return [
    'api_key' => env('NVIDIA_API_KEY'),
    'api_url' => env('NVIDIA_API_URL', 'https://integrate.api.nvidia.com/v1'),
    'temperature' => (float) env('NVIDIA_API_TEMPERATURE', 0.5),
    'max_tokens' => (int) env('NVIDIA_API_MAX_TOKENS', 1524),
    
    /*
    |--------------------------------------------------------------------------
    | Multi-Model AI Routing Directory
    |--------------------------------------------------------------------------
    | Routes different business actions to the most specialized AI models
    | optimized for those specific roles.
    */
    'models' => [
        'default' => env('NVIDIA_API_MODEL', 'meta/llama-3.3-70b-instruct'),
        'client_analyze' => env('NVIDIA_MODEL_CLIENT', 'meta/llama-3.3-70b-instruct'),
        'proposal_improve' => env('NVIDIA_MODEL_PROPOSAL', 'mistralai/mistral-large-2-instruct'),
        'contract_risk' => env('NVIDIA_MODEL_CONTRACT', 'deepseek-ai/deepseek-v4-pro'),
        'expense_analyze' => env('NVIDIA_MODEL_EXPENSE', 'meta/llama-3.1-8b-instruct'),
        'briefing' => env('NVIDIA_MODEL_BRIEFING', 'meta/llama-3.3-70b-instruct'),
        'work_summary' => env('NVIDIA_MODEL_WORK', 'meta/llama-3.1-8b-instruct'),
    ],
];
