<?php

declare(strict_types=1);

namespace Nevamiss\Application;

enum Task: string
{
    case PENDING = 'pending';
    case RUNNING = 'running';
    case COMPLETED = 'completed';
}
