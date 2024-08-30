<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'declare_strict_types' => true,
        'phpdoc_to_comment' => ['ignored_tags' => ['psalm-suppress']],
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
