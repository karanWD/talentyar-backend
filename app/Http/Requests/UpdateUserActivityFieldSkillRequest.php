<?php

namespace App\Http\Requests;

class UpdateUserActivityFieldSkillRequest extends ActivityFieldSkillRequest
{
    protected function isPartial(): bool
    {
        return true;
    }
}
