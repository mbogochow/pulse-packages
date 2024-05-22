<?php

namespace AaronFrancis\Pulse\Outdated;

enum ComposerVersionFilter: string
{
    case MAJOR_ONLY = '--major-only';
    case MINOR_ONLY = '--minor-only';
    case PATCH_ONLY = '--patch-only';
}
