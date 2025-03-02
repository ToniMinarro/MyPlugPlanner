<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\ValueObject\PhpVersion;
use Rector\Set\ValueObject\SetList;
use Rector\Symfony\Set\SymfonySetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Php80\Rector\Switch_\ChangeSwitchToMatchRector;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;

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
        ClassPropertyAssignToConstructorPromotionRector::class,
    ]);
};
