<?php

namespace App\Http\Controllers;

use App\Models\LearningRecommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningRecommendationEventController extends Controller
{
    /**
     * Track a click event and redirect to the recommendation URL.
     */
    public function click(Request $request, LearningRecommendation $recommendation)
    {
        // record event only if URL exists
        if (!empty($recommendation->url)) {
            DB::table('learning_recommendation_events')->insert([
                'recommendation_id' => $recommendation->id,
                'user_id' => $request->user()->id,
                'event_type' => 'clicked',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return redirect()->away($recommendation->url);
        }
        return redirect()->back();
    }
}
