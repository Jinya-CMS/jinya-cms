<?php

namespace Jinya\Cms\Database\Analyzer;

/**
 * Declares the different variable types available in MySQL, MariaDB, Percona etc.
 */
enum VariablesType
{
    case Global;
    case Local;
    case Session;
}
