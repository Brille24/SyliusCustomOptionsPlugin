<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Alias\EregToPregFixer;
use PhpCsFixer\Fixer\Alias\NoAliasFunctionsFixer;
use PhpCsFixer\Fixer\Alias\PowToExponentiationFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoMultilineWhitespaceAroundDoubleArrowFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\NoWhitespaceBeforeCommaInArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\NormalizeIndexBraceFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrailingCommaInMultilineArrayFixer;
use PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer;
use PhpCsFixer\Fixer\ArrayNotation\WhitespaceAfterCommaInArrayFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\Basic\NonPrintableCharacterFixer;
use PhpCsFixer\Fixer\Casing\LowercaseConstantsFixer;
use PhpCsFixer\Fixer\Casing\LowercaseKeywordsFixer;
use PhpCsFixer\Fixer\Casing\MagicConstantCasingFixer;
use PhpCsFixer\Fixer\Casing\NativeFunctionCasingFixer;
use PhpCsFixer\Fixer\CastNotation\CastSpacesFixer;
use PhpCsFixer\Fixer\CastNotation\LowercaseCastFixer;
use PhpCsFixer\Fixer\CastNotation\ModernizeTypesCastingFixer;
use PhpCsFixer\Fixer\CastNotation\NoShortBoolCastFixer;
use PhpCsFixer\Fixer\CastNotation\ShortScalarCastFixer;
use PhpCsFixer\Fixer\ClassNotation\MethodSeparationFixer;
use PhpCsFixer\Fixer\ClassNotation\NoBlankLinesAfterClassOpeningFixer;
use PhpCsFixer\Fixer\ClassNotation\NoNullPropertyInitializationFixer;
use PhpCsFixer\Fixer\ClassNotation\NoPhp4ConstructorFixer;
use PhpCsFixer\Fixer\ClassNotation\NoUnneededFinalMethodFixer;
use PhpCsFixer\Fixer\ClassNotation\ProtectedToPrivateFixer;
use PhpCsFixer\Fixer\ClassNotation\SelfAccessorFixer;
use PhpCsFixer\Fixer\ClassNotation\SingleClassElementPerStatementFixer;
use PhpCsFixer\Fixer\Comment\MultilineCommentOpeningClosingFixer;
use PhpCsFixer\Fixer\Comment\NoEmptyCommentFixer;
use PhpCsFixer\Fixer\Comment\NoTrailingWhitespaceInCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\ElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\IncludeFixer;
use PhpCsFixer\Fixer\ControlStructure\NoBreakCommentFixer;
use PhpCsFixer\Fixer\ControlStructure\NoSuperfluousElseifFixer;
use PhpCsFixer\Fixer\ControlStructure\NoTrailingCommaInListCallFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededControlParenthesesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUnneededCurlyBracesFixer;
use PhpCsFixer\Fixer\ControlStructure\NoUselessElseFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSemicolonToColonFixer;
use PhpCsFixer\Fixer\ControlStructure\SwitchCaseSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionDeclarationFixer;
use PhpCsFixer\Fixer\FunctionNotation\FunctionTypehintSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\MethodArgumentSpaceFixer;
use PhpCsFixer\Fixer\FunctionNotation\NoSpacesAfterFunctionNameFixer;
use PhpCsFixer\Fixer\FunctionNotation\ReturnTypeDeclarationFixer;
use PhpCsFixer\Fixer\Import\NoLeadingImportSlashFixer;
use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Import\OrderedImportsFixer;
use PhpCsFixer\Fixer\Import\SingleImportPerStatementFixer;
use PhpCsFixer\Fixer\Import\SingleLineAfterImportsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveIssetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\CombineConsecutiveUnsetsFixer;
use PhpCsFixer\Fixer\LanguageConstruct\DirConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\FunctionToConstantFixer;
use PhpCsFixer\Fixer\LanguageConstruct\IsNullFixer;
use PhpCsFixer\Fixer\NamespaceNotation\BlankLineAfterNamespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\NoLeadingNamespaceWhitespaceFixer;
use PhpCsFixer\Fixer\NamespaceNotation\SingleBlankLineBeforeNamespaceFixer;
use PhpCsFixer\Fixer\Naming\NoHomoglyphNamesFixer;
use PhpCsFixer\Fixer\Operator\NewWithBracesFixer;
use PhpCsFixer\Fixer\Operator\ObjectOperatorWithoutWhitespaceFixer;
use PhpCsFixer\Fixer\Operator\PreIncrementFixer;
use PhpCsFixer\Fixer\Operator\StandardizeNotEqualsFixer;
use PhpCsFixer\Fixer\Operator\TernaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\Operator\TernaryToNullCoalescingFixer;
use PhpCsFixer\Fixer\Operator\UnaryOperatorSpacesFixer;
use PhpCsFixer\Fixer\PhpTag\BlankLineAfterOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PhpCsFixer\Fixer\PhpTag\NoClosingTagFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitDedicateAssertFixer;
use PhpCsFixer\Fixer\PhpUnit\PhpUnitFqcnAnnotationFixer;
use PhpCsFixer\Fixer\Phpdoc\NoBlankLinesAfterPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\NoEmptyPhpdocFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocIndentFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocInlineTagFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoAliasTagFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocNoUselessInheritdocFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocReturnSelfReferenceFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocScalarFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSeparationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSingleLineVarSpacingFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer;
use PhpCsFixer\Fixer\Semicolon\NoEmptyStatementFixer;
use PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer;
use PhpCsFixer\Fixer\Semicolon\SpaceAfterSemicolonFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\StringNotation\SingleQuoteFixer;
use PhpCsFixer\Fixer\Whitespace\BlankLineBeforeStatementFixer;
use PhpCsFixer\Fixer\Whitespace\IndentationTypeFixer;
use PhpCsFixer\Fixer\Whitespace\LineEndingFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesAroundOffsetFixer;
use PhpCsFixer\Fixer\Whitespace\NoSpacesInsideParenthesisFixer;
use PhpCsFixer\Fixer\Whitespace\NoTrailingWhitespaceFixer;
use PhpCsFixer\Fixer\Whitespace\NoWhitespaceInBlankLineFixer;
use PhpCsFixer\Fixer\Whitespace\SingleBlankLineAtEofFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->set(EregToPregFixer::class);

    $services->set(NoAliasFunctionsFixer::class);

    $services->set(PowToExponentiationFixer::class);

    $services->set('PhpCsFixer\Fixer\Alias\NoMixedEchoPrintFixer')
        ->call('configure', [['use' => 'echo']]);

    $services->set('PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer')
        ->call('configure', [['syntax' => 'short']]);

    $services->set(NoMultilineWhitespaceAroundDoubleArrowFixer::class);

    $services->set(NormalizeIndexBraceFixer::class);

    $services->set(NoTrailingCommaInSinglelineArrayFixer::class);

    $services->set(NoWhitespaceBeforeCommaInArrayFixer::class);

    $services->set(TrailingCommaInMultilineArrayFixer::class);

    $services->set(TrimArraySpacesFixer::class);

    $services->set(WhitespaceAfterCommaInArrayFixer::class);

    $services->set('PhpCsFixer\Fixer\Basic\BracesFixer')
        ->call('configure', [['allow_single_line_closure' => true]]);

    $services->set(EncodingFixer::class);

    $services->set(NonPrintableCharacterFixer::class);

    $services->set(LowercaseConstantsFixer::class);

    $services->set(LowercaseKeywordsFixer::class);

    $services->set(NativeFunctionCasingFixer::class);

    $services->set(MagicConstantCasingFixer::class);

    $services->set(CastSpacesFixer::class);

    $services->set(LowercaseCastFixer::class);

    $services->set(ModernizeTypesCastingFixer::class);

    $services->set(NoShortBoolCastFixer::class);

    $services->set(ShortScalarCastFixer::class);

    $services->set('PhpCsFixer\Fixer\ClassNotation\ClassDefinitionFixer')
        ->call('configure', [['single_item_single_line' => true, 'multi_line_extends_each_single_line' => true]]);

    $services->set(MethodSeparationFixer::class);

    $services->set(NoBlankLinesAfterClassOpeningFixer::class);

    $services->set(NoNullPropertyInitializationFixer::class);

    $services->set(NoPhp4ConstructorFixer::class);

    $services->set(NoUnneededFinalMethodFixer::class);

    $services->set(ProtectedToPrivateFixer::class);

    $services->set(SelfAccessorFixer::class);

    $services->set(SingleClassElementPerStatementFixer::class);

    $services->set('PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer')
    ->call('configure', [['elements' => ['property', 'method']]]);

    $services->set(MultilineCommentOpeningClosingFixer::class);

    $services->set(NoEmptyCommentFixer::class);

    $services->set(NoTrailingWhitespaceInCommentFixer::class);

    $services->set(ElseifFixer::class);

    $services->set(IncludeFixer::class);

    $services->set(NoBreakCommentFixer::class);

    $services->set(NoSuperfluousElseifFixer::class);

    $services->set(NoTrailingCommaInListCallFixer::class);

    $services->set(NoUnneededControlParenthesesFixer::class);

    $services->set(NoUnneededCurlyBracesFixer::class);

    $services->set(NoUselessElseFixer::class);

    $services->set(SwitchCaseSemicolonToColonFixer::class);

    $services->set(SwitchCaseSpaceFixer::class);

    $services->set(FunctionDeclarationFixer::class);

    $services->set(FunctionTypehintSpaceFixer::class);

    $services->set(MethodArgumentSpaceFixer::class);

    $services->set(NoSpacesAfterFunctionNameFixer::class);

    $services->set(ReturnTypeDeclarationFixer::class);

    $services->set(NoLeadingImportSlashFixer::class);

    $services->set(NoUnusedImportsFixer::class);

    $services->set(OrderedImportsFixer::class);

    $services->set(SingleImportPerStatementFixer::class);

    $services->set(SingleLineAfterImportsFixer::class);

    $services->set(CombineConsecutiveIssetsFixer::class);

    $services->set(CombineConsecutiveUnsetsFixer::class);

    $services->set('PhpCsFixer\Fixer\LanguageConstruct\DeclareEqualNormalizeFixer')
        ->call('configure', [['space' => 'none']]);

    $services->set(DirConstantFixer::class);

    $services->set(FunctionToConstantFixer::class);

    $services->set(IsNullFixer::class);

    $services->set('PhpCsFixer\Fixer\ListNotation\ListSyntaxFixer')
        ->call('configure', [['syntax' => 'short']]);

    $services->set(BlankLineAfterNamespaceFixer::class);

    $services->set(NoLeadingNamespaceWhitespaceFixer::class);

    $services->set(SingleBlankLineBeforeNamespaceFixer::class);

    $services->set(NoHomoglyphNamesFixer::class);

    $services->set('PhpCsFixer\Fixer\Operator\BinaryOperatorSpacesFixer')
        ->call('configure', [['operators' => ['=>' => 'align', '=' => 'align']]]);

    $services->set('PhpCsFixer\Fixer\Operator\ConcatSpaceFixer')
        ->call('configure', [['spacing' => 'none']]);

    $services->set(NewWithBracesFixer::class);

    $services->set(ObjectOperatorWithoutWhitespaceFixer::class);

    $services->set(PreIncrementFixer::class);

    $services->set(StandardizeNotEqualsFixer::class);

    $services->set(TernaryOperatorSpacesFixer::class);

    $services->set(TernaryToNullCoalescingFixer::class);

    $services->set(UnaryOperatorSpacesFixer::class);

    $services->set(NoBlankLinesAfterPhpdocFixer::class);

    $services->set(NoEmptyPhpdocFixer::class);

    $services->set('PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer')
        ->call('configure', [['only_untyped' => false]]);

    $services->set(PhpdocIndentFixer::class);

    $services->set(PhpdocInlineTagFixer::class);

    $services->set(PhpdocNoAccessFixer::class);

    $services->set(PhpdocNoAliasTagFixer::class);

    $services->set(PhpdocNoEmptyReturnFixer::class);

    $services->set(PhpdocNoPackageFixer::class);

    $services->set(PhpdocNoUselessInheritdocFixer::class);

    $services->set(PhpdocReturnSelfReferenceFixer::class);

    $services->set(PhpdocScalarFixer::class);

    $services->set(PhpdocSeparationFixer::class);

    $services->set(PhpdocSingleLineVarSpacingFixer::class);

    $services->set(PhpdocTrimFixer::class);

    $services->set(PhpdocTypesFixer::class);

    $services->set('PhpCsFixer\Fixer\Phpdoc\PhpdocTypesOrderFixer')
        ->call('configure', [['null_adjustment' => 'always_last', 'sort_algorithm' => 'none']]);

    $services->set(PhpdocVarWithoutNameFixer::class);

    $services->set(BlankLineAfterOpeningTagFixer::class);

    $services->set(FullOpeningTagFixer::class);

    $services->set(NoClosingTagFixer::class);

    $services->set(PhpUnitDedicateAssertFixer::class);

    $services->set(PhpUnitFqcnAnnotationFixer::class);

    $services->set(NoEmptyStatementFixer::class);

    $services->set(NoSinglelineWhitespaceBeforeSemicolonsFixer::class);

    $services->set(SpaceAfterSemicolonFixer::class);

    $services->set(DeclareStrictTypesFixer::class);

    $services->set(SingleQuoteFixer::class);

    $services->set(BlankLineBeforeStatementFixer::class);

    $services->set(IndentationTypeFixer::class);

    $services->set(LineEndingFixer::class);

    $services->set('PhpCsFixer\Fixer\Whitespace\NoExtraConsecutiveBlankLinesFixer')
        ->call('configure', [['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use']]);

    $services->set(NoSpacesAroundOffsetFixer::class);

    $services->set(NoSpacesInsideParenthesisFixer::class);

    $services->set(NoTrailingWhitespaceFixer::class);

    $services->set(NoWhitespaceInBlankLineFixer::class);

    $services->set(SingleBlankLineAtEofFixer::class);

    $parameters = $containerConfigurator->parameters();

    $parameters->set('skip', ['PhpCsFixer\Fixer\ClassNotation\VisibilityRequiredFixer' => ['*Spec.php']]);
};
