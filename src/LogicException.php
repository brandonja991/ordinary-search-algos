<?php

declare(strict_types=1);

namespace Ordinary\SearchAlgos;

use LogicException as PHPLogicException;

class LogicException extends PHPLogicException implements SearchAlgoException
{
}
