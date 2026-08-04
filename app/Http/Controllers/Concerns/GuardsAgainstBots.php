<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait GuardsAgainstBots
{
    /**
     * A submission is treated as bot traffic if the honeypot field was
     * filled in (real visitors never see it) or if the form was submitted
     * suspiciously fast after being rendered (faster than a human could
     * realistically fill it in).
     */
    protected function isLikelyBot(Request $request, int $minSecondsToFill = 20): bool
    {
        if (filled($request->input('website'))) {
            return true;
        }

        $renderedAt = (int) $request->input('form_rendered_at');

        if ($renderedAt <= 0) {
            return true;
        }

        return (time() - $renderedAt) < $minSecondsToFill;
    }
}
