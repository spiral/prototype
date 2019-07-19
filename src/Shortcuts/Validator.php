<?php
/**
 * Spiral Framework.
 *
 * @license MIT
 * @author  Valentin V (vvval)
 */
declare(strict_types=1);

namespace Spiral\Prototype\Shortcuts;

class Validator
{
    private const ERRORS = [
        'shortcut' => 'Invalid name format.',
        'binding'  => 'Invalid binding format.',
    ];

    private const NAME_REGEX = '/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/';

    public function validate(string $shortcut, string $binding): ?array
    {
        $errors = self::ERRORS;
        if ($this->isValidName($shortcut)) {
            unset($errors['shortcut']);
        }

        if ($this->isValidBinding($binding)) {
            unset($errors['binding']);
        }

        return $errors ?? null;
    }

    private function isValidBinding(string $binding): bool
    {
        $binding = trim($binding, '\\');
        foreach (explode('\\', $binding) as $part) {
            if (!empty($part) && $this->isValidName($part)) {
                return true;
            }
        }

        return false;
    }

    private function isValidName(string $name): bool
    {
        return (bool)preg_match(self::NAME_REGEX, $name);
    }
}