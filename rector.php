<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\CodeQuality\Rector\Class_\InlineClassRoutePrefixRector;
use Rector\Symfony\Set\SymfonySetList;
use Rector\ValueObject\PhpVersion;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->fileExtensions(['php', 'md']);
    $rectorConfig->phpVersion(PhpVersion::PHP_84);
    $rectorConfig->parallel();
    $rectorConfig->importNames();
    $rectorConfig->importShortClasses();

    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

    $rectorConfig->rules([
        CompleteDynamicPropertiesRector::class,
        InlineConstructorDefaultToPropertyRector::class,
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_84,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::CODING_STYLE,
        SetList::TYPE_DECLARATION,
        SetList::PRIVATIZATION,

        SymfonySetList::SYMFONY_72,
        SymfonySetList::SYMFONY_CODE_QUALITY,
        SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
    ]);

    $rectorConfig->skip([
        __DIR__ . '/vendor',
        __DIR__ . '/var',
        ChangeSwitchToMatchRector::class,
        NewlineAfterStatementRector::class,
        RemoveExtraParametersRector::class,
        InlineClassRoutePrefixRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class,
    ]);
};
