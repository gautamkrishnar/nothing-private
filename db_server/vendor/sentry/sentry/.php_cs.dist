<?php

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
        ],
        'declare_strict_types' => true,
        'psr0' => true,
        'psr4' => true,
        'random_api_migration' => true,
        'yoda_style' => true,
        'self_accessor' => false,
        'phpdoc_no_useless_inheritdoc' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_align' => [
            'tags' => ['param', 'return', 'throws', 'type', 'var'],
        ],
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude([
                'tests/resources',
                'tests/phpt',
                'tests/Fixtures',
            ])
    )
;
