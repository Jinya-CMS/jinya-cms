<?php

namespace App\Database\Analyzer;

enum VariablesType {
    case Global;
    case Local;
    case Session;
}
