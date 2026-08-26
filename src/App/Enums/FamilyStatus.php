<?php

namespace App\Enums;

enum FamilyStatus: string
{
    case Single = 'single';
    case Married = 'married';
    case Divorced = 'divorced';
}
