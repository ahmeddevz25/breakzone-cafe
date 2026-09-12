<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Phase;
use App\Models\Block;
use App\Models\Plot;
use Illuminate\Support\Facades\Auth;

class AiGenerationController extends Controller
{
    public function generatePhase(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:1000'
        ]);

        $userPrompt = $request->input('prompt');

        // System prompt to instruct Ollama
        $systemPrompt = "You are a data extraction assistant for a real estate system.
Your task is to extract information from the user's input and return it in a strictly valid JSON format.
DO NOT return any markdown formatting, explanations, or text other than the JSON object itself.

JSON Schema to follow:
{
    \"phase_name\": \"string (name of the phase/society)\",
    \"land_in_acer\": \"number (optional, default to 0 if not mentioned)\",
    \"land_in_marla\": \"number (optional, default to 0 if not mentioned)\",
    \"blocks\": [
        {
            \"block_name\": \"string\",
            \"plots\": [
                {
                    \"size\": \"string (e.g. '5 Marla')\",
                    \"count\": \"number (how many plots to create)\"
                }
            ]
        }
    ]
}

If blocks are not mentioned, just create a single default block named 'General Block' and put the plots inside it.
If plot size is not explicitly mentioned but plot count is, default to '5 Marla'.";

        try {
            // Send request to local Ollama API with higher timeout (300 seconds)
            $response = Http::timeout(300)->post('http://localhost:11434/api/generate', [
                'model' => 'llama3', // You can change this to 'qwen' or whichever model is installed
                'system' => $systemPrompt,
                'prompt' => $userPrompt,
                'format' => 'json',
                'stream' => false,
            ]);

            if (!$response->successful()) {
                $errorBody = $response->body();
                return response()->json([
                    'success' => false,
                    'message' => 'AI Server Error: ' . $errorBody
                ], 500);
            }

            $aiResponse = $response->json();
            $jsonString = $aiResponse['response'] ?? '';

            $data = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE || !isset($data['phase_name'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI could not understand the structure. Please try a different prompt.'
                ], 400);
            }

            DB::beginTransaction();

            // Create Phase
            $phase = Phase::create([
                'phase_name' => $data['phase_name'],
                'land_in_acer' => $data['land_in_acer'] ?? 0,
                'land_in_marla' => $data['land_in_marla'] ?? 0,
                'created_by' => Auth::id(),
                'ip' => $request->ip()
            ]);
            $totalPlotsCreated = 0;

            // Create Blocks and Plots
            if (isset($data['blocks']) && is_array($data['blocks'])) {
                foreach ($data['blocks'] as $blockData) {
                    $block = Block::create([
                        'phase_id' => $phase->id,
                        'block_name' => $blockData['block_name'],
                        'created_by' => Auth::id(),
                        'ip' => $request->ip()
                    ]);

                    if (isset($blockData['plots']) && is_array($blockData['plots'])) {
                        foreach ($blockData['plots'] as $plotGroup) {
                            $count = (int) ($plotGroup['count'] ?? 0);
                            $size = $plotGroup['size'] ?? '5 Marla';

                            for ($i = 1; $i <= $count; $i++) {
                                Plot::create([
                                    'phase_id' => $phase->id,
                                    'block_id' => $block->id,
                                    'plot_no' => $block->block_name . '-' . $i,
                                    'plot_size' => $size,
                                    'plot_type' => 'Residential', // Default
                                    'plot_category' => 'General', // Default
                                    'created_by' => Auth::id(),
                                    'ip' => $request->ip()
                                ]);
                                $totalPlotsCreated++;
                            }
                        }
                    }
                }
            }
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully created phase '{$phase->phase_name}' with $totalPlotsCreated plots."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
}
