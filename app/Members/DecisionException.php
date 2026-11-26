<?php

namespace App\Members;

use RuntimeException;

/** A decision that cannot be made, with a message for the person who tried. */
final class DecisionException extends RuntimeException {}
