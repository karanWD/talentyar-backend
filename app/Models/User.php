<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
    ];

    const GENDER_MALE = 0;
    const GENDER_FEMALE = 1;
    const GENDERS = [
        self::GENDER_MALE,
        self::GENDER_FEMALE,
    ];

    const FOOT_SPECIALIZATION_RIGHT = 'right';
    const FOOT_SPECIALIZATION_LEFT = 'left';
    const FOOT_SPECIALIZATION_BOTH = 'both';
    const FOOT_SPECIALIZATION = [
        self::FOOT_SPECIALIZATION_RIGHT,
        self::FOOT_SPECIALIZATION_LEFT,
        self::FOOT_SPECIALIZATION_BOTH,
    ];

    const POST_SKILL_GK = 'GK';
    const POST_SKILL_CB = 'CB';
    const POST_SKILL_RB = 'RB';
    const POST_SKILL_LB = 'LB';
    const POST_SKILL_DM = 'DM';
    const POST_SKILL_CM = 'CM';
    const POST_SKILL_AM = 'AM';
    const POST_SKILL_LW = 'LW';
    const POST_SKILL_RW = 'RW';
    const POST_SKILL_CF = 'CF';
    const POST_SKILL_SS = 'SS';
    const POST_SKILL_ST = 'ST';

    const POST_SKILL = [
        self::POST_SKILL_GK,
        self::POST_SKILL_CB,
        self::POST_SKILL_RB,
        self::POST_SKILL_LB,
        self::POST_SKILL_DM,
        self::POST_SKILL_CM,
        self::POST_SKILL_AM,
        self::POST_SKILL_LW,
        self::POST_SKILL_RW,
        self::POST_SKILL_CF,
        self::POST_SKILL_SS,
        self::POST_SKILL_ST
    ];



    const SKILL_LEVEL_BEGINNER = 'beginner';
    const SKILL_LEVEL_SEMI_PROFESSIONAL = 'semi-professional';
    const SKILL_LEVEL_PROFESSIONAL = 'professional';
    const SKILL_LEVEL = [
        self::SKILL_LEVEL_BEGINNER,
        self::SKILL_LEVEL_SEMI_PROFESSIONAL,
        self::SKILL_LEVEL_PROFESSIONAL,
    ];
}
