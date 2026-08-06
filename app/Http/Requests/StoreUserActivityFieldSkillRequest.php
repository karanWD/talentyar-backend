<?php

namespace App\Http\Requests;

class StoreUserActivityFieldSkillRequest extends ActivityFieldSkillRequest
{
    protected function isPartial(): bool
    {
        return false;
    }
}
