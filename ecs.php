<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\ClassNotation\ClassAttributesSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer;
use PhpCsFixer\Fixer\ControlStructure\YodaStyleFixer;
use PhpCsFixer\Fixer\FunctionNotation\NativeFunctionInvocationFixer;
use PhpCsFixer\Fixer\FunctionNotation\StaticLambdaFixer;
use PhpCsFixer\Fixer\FunctionNotation\UseArrowFunctionsFixer;
use PhpCsFixer\Fixer\FunctionNotation\VoidReturnFixer;
use PhpCsFixer\Fixer\Import\GlobalNamespaceImportFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Operator\NotOperatorWithSuccessorSpaceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocLineSpanFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitMethodCasingFixer;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

return static function (ECSConfig $ecsConfig): void {
    $ecsConfig->fileExtensions(['php', 'md']);
    $ecsConfig->paths([
        __DIR__ . '/src',
    ]);
    $ecsConfig->skip([
        __DIR__ . '/vendor',
        __DIR__ . '/var',
        NotOperatorWithSuccessorSpaceFixer::class,
    ]);

    $ecsConfig->parallel();

    $ecsConfig->sets([
        SetList::COMMON,
        SetList::CLEAN_CODE,
        SetList::PSR_12,
    ]);

    $ecsConfig->rules([
        StaticLambdaFixer::class,
        UseArrowFunctionsFixer::class,
        NativeFunctionInvocationFixer::class,
        VoidReturnFixer::class,
    ]);

    $ecsConfig->ruleWithConfiguration(LineLengthFixer::class, [
        'max_line_length' => 100,
        'break_long_lines' => true,
        'inline_short_lines' => false,
    ]);

    $ecsConfig->ruleWithConfiguration(GlobalNamespaceImportFixer::class, [
        'import_constants' => true,
        'import_functions' => true,
        'import_classes' => true,
    ]);

    $ecsConfig->ruleWithConfiguration(PhpdocLineSpanFixer::class, [
        'const' => 'single',
        'property' => 'single',
        'method' => 'single',
    ]);

    $ecsConfig->ruleWithConfiguration(YodaStyleFixer::class, [
        'equal' => true,
        'identical' => true,
        'less_and_greater' => true,
        'always_move_variable' => true,
    ]);

    $ecsConfig->ruleWithConfiguration(PhpUnitMethodCasingFixer::class, [
        'case' => PhpUnitMethodCasingFixer::SNAKE_CASE,
    ]);

    $ecsConfig->skip(
        [
            OrderedImportsFixer::class,
            VisibilityRequiredFixer::class,
            ClassAttributesSeparationFixer::class,
        ]
    );
};
