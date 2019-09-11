<?php

namespace Mygento\Jeeves\Generators\Project;

class Php
{
    public function generateComposer($vendor, $name)
    {
        return json_encode([
            'name' => $vendor . '/' . $name,
            'type' => 'project',
            'version' => '2.2.6',
            'require' => [
                'magento/product-community-edition' => '~2.2',
                'composer/composer' => '1.4.1',
                'mygento/base' => '~2.2',
                'mygento/module-configsync' => '~2.2',
                'mageplaza/module-smtp' => '^1.1.0',
                'etws/magento-language-ru_ru' => 'dev-develop',
            ],
            'require-dev' => [
                'mygento/coding-standard' => '~2.2',
            ],
            'replace' => [
                'magento/module-marketplace' => '*',
                'magento/module-cybersource' => '*',
                'magento/module-authorizenet' => '*',
                'magento/module-signifyd' => '*',
                'amzn/amazon-pay-module' => '*',
                'amzn/amazon-pay-and-login-magento-2-module' => '*',
                'amzn/amazon-pay-and-login-with-amazon-core-module' => '*',
                'amzn/login-with-amazon-module' => '*',
                'dotmailer/dotmailer-magento2-extension' => '*',
                'klarna/module-core' => '*',
                'klarna/module-kp' => '*',
                'klarna/module-ordermanagement' => '*',
                'shopialfb/facebook-module' => '*',
                'temando/module-shipping-m2' => '*',
                'vertex/module-tax' => '*',
            ],
            'autoload' => [
                'psr-4' => [
                    'Magento\\Framework\\' => 'lib/internal/Magento/Framework/',
                    'Magento\\Setup\\' => 'setup/src/Magento/Setup/',
                    'Magento\\' => 'app/code/Magento/',
                ],
                'psr-0' => [
                    '' => ['app/code/'],
                ],
                'files' => ['app/etc/NonComposerComponentRegistration.php'],
                'exclude-from-classmap' => [
                    '**/dev/**',
                    '**/update/**',
                    '**/Test/**',
                ],
            ],
            'autoload-dev' => [
                'psr-4' => [
                    'Magento\\Sniffs\\' => 'dev/tests/static/framework/Magento/Sniffs/',
                    'Magento\\Tools\\' => 'dev/tools/Magento/Tools/',
                    'Magento\\Tools\\Sanity\\' => 'dev/build/publication/sanity/Magento/Tools/Sanity/',
                    'Magento\\TestFramework\\Inspection\\' => 'dev/tests/static/framework/Magento/TestFramework/Inspection/',
                    'Magento\\TestFramework\\Utility\\' => 'dev/tests/static/framework/Magento/TestFramework/Utility/',
                ],
            ],
            'minimum-stability' => 'stable',
            'prefer-stable' => true,
            'repositories' => [
                [
                    'type' => 'composer',
                    'url' => 'https://repo.magento.com/',
                ],
            ],
            'extra' => [
                'magento-force' => 'override',
            ],
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    public function generateLint()
    {
        $d = 'd' . 'ie';
        $v = 'var' . '_dump';
        $e = 'e' . 'xit';

        return <<<CONFIG
parameters:
  git_dir: .
  bin_dir: vendor/bin
  tasks:
    git_blacklist:
      keywords:
        - "${d}("
        - "${v}("
        - "${e};"
    phplint:
    phpcsfixer2:
      config: '.php_cs'
    xmllint:

CONFIG;
    }

    public function generateFixer()
    {
        $finder = '$finder';

        return <<<CONFIG
<?php
${finder} = PhpCsFixer\Finder::create()
    ->in('app')
    ->name('*.phtml')
    ->exclude('dev/tests/functional/generated')
    ->exclude('dev/tests/functional/var')
    ->exclude('dev/tests/functional/vendor')
    ->exclude('dev/tests/integration/tmp')
    ->exclude('dev/tests/integration/var')
    ->exclude('lib/internal/Cm')
    ->exclude('lib/internal/Credis')
    ->exclude('lib/internal/Less')
    ->exclude('lib/internal/LinLibertineFont')
    ->exclude('pub/media')
    ->exclude('pub/static')
    ->exclude('setup/vendor')
    ->exclude('var');

return PhpCsFixer\Config::create()
    ->setFinder(${finder})
    ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'include' => true,
        'new_with_braces' => true,
        'no_empty_statement' => true,
        'no_extra_consecutive_blank_lines' => true,
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,
        'no_multiline_whitespace_around_double_arrow' => true,
        'no_multiline_whitespace_before_semicolons' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_unused_imports' => true,
        'no_whitespace_in_blank_line' => true,
        'object_operator_without_whitespace' => true,
        'ordered_imports' => true,
        'standardize_not_equals' => true,
        'ternary_operator_spaces' => true,
        // mygento
        'phpdoc_order' => true,
        'phpdoc_types' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'single_quote' => true,
        'standardize_not_equals' => true,
        'ternary_to_null_coalescing' => true,
        'ternary_operator_spaces' => true,
        'lowercase_cast' => true,
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'return_type_declaration' => true,
    ]);

CONFIG;
    }

    public function generateConfig()
    {
        return <<<CONFIG
<?php
return array (
  'modules' =>
  array (
    'Magento_Store' => 1,
    'Magento_Directory' => 1,
    'Magento_AdvancedPricingImportExport' => 1,
    'Magento_Config' => 1,
    'Magento_Backend' => 1,
    'Magento_Theme' => 1,
    'Magento_Eav' => 1,
    'Magento_Backup' => 1,
    'Magento_Customer' => 1,
    'Magento_AdminNotification' => 1,
    'Magento_BundleImportExport' => 1,
    'Magento_CacheInvalidate' => 1,
    'Magento_Indexer' => 1,
    'Magento_Cms' => 1,
    'Magento_Security' => 1,
    'Magento_CatalogImportExport' => 1,
    'Magento_Rule' => 1,
    'Magento_Cron' => 1,
    'Magento_Catalog' => 1,
    'Magento_Search' => 1,
    'Magento_CatalogUrlRewrite' => 1,
    'Magento_Widget' => 1,
    'Magento_Quote' => 1,
    'Magento_SalesSequence' => 1,
    'Magento_Payment' => 1,
    'Magento_CmsUrlRewrite' => 1,
    'Magento_User' => 1,
    'Magento_ConfigurableImportExport' => 1,
    'Magento_Msrp' => 1,
    'Magento_CatalogInventory' => 1,
    'Magento_Contact' => 1,
    'Magento_Cookie' => 1,
    'Magento_Newsletter' => 1,
    'Magento_CurrencySymbol' => 1,
    'Magento_Sales' => 1,
    'Magento_Integration' => 1,
    'Magento_CustomerImportExport' => 1,
    'Magento_Deploy' => 1,
    'Magento_Developer' => 1,
    'Magento_Dhl' => 1,
    'Magento_Authorization' => 1,
    'Magento_Downloadable' => 1,
    'Magento_ImportExport' => 1,
    'Magento_Bundle' => 1,
    'Magento_Email' => 1,
    'Magento_EncryptionKey' => 1,
    'Magento_Fedex' => 1,
    'Magento_GiftMessage' => 0,
    'Magento_Checkout' => 1,
    'Magento_GoogleAnalytics' => 1,
    'Magento_Ui' => 1,
    'Magento_GroupedImportExport' => 1,
    'Magento_GroupedProduct' => 1,
    'Magento_DownloadableImportExport' => 1,
    'Magento_CatalogRule' => 1,
    'Magento_InstantPurchase' => 1,
    'Magento_Analytics' => 1,
    'Magento_LayeredNavigation' => 1,
    'Magento_Marketplace' => 1,
    'Magento_MediaStorage' => 1,
    'Magento_ConfigurableProduct' => 1,
    'Magento_Multishipping' => 1,
    'Magento_NewRelicReporting' => 1,
    'Magento_Reports' => 1,
    'Magento_OfflinePayments' => 1,
    'Magento_SalesRule' => 1,
    'Magento_PageCache' => 1,
    'Magento_Vault' => 1,
    'Magento_Paypal' => 1,
    'Magento_Persistent' => 1,
    'Magento_ProductAlert' => 1,
    'Magento_ProductVideo' => 1,
    'Magento_CheckoutAgreements' => 1,
    'Magento_QuoteAnalytics' => 1,
    'Magento_ReleaseNotification' => 1,
    'Magento_Review' => 1,
    'Magento_RequireJs' => 1,
    'Magento_Shipping' => 1,
    'Magento_ReviewAnalytics' => 1,
    'Magento_Robots' => 1,
    'Magento_Rss' => 1,
    'Magento_CatalogRuleConfigurable' => 1,
    'Magento_Captcha' => 1,
    'Magento_SalesAnalytics' => 1,
    'Magento_SalesInventory' => 1,
    'Magento_OfflineShipping' => 1,
    'Magento_ConfigurableProductSales' => 1,
    'Magento_UrlRewrite' => 1,
    'Magento_CatalogSearch' => 1,
    'Magento_CustomerAnalytics' => 1,
    'Magento_SendFriend' => 1,
    'Magento_Wishlist' => 1,
    'Magento_Signifyd' => 1,
    'Magento_Sitemap' => 1,
    'Magento_Authorizenet' => 1,
    'Magento_Swagger' => 1,
    'Magento_Swatches' => 1,
    'Magento_SwatchesLayeredNavigation' => 1,
    'Magento_Tax' => 1,
    'Magento_TaxImportExport' => 1,
    'Magento_GoogleAdwords' => 1,
    'Magento_Translation' => 1,
    'Magento_GoogleOptimizer' => 1,
    'Magento_Ups' => 1,
    'Magento_SampleData' => 1,
    'Magento_CatalogAnalytics' => 1,
    'Magento_Usps' => 1,
    'Magento_Variable' => 1,
    'Magento_Braintree' => 1,
    'Magento_Version' => 1,
    'Magento_Webapi' => 1,
    'Magento_WebapiSecurity' => 1,
    'Magento_Weee' => 1,
    'Magento_CatalogWidget' => 1,
    'Magento_WishlistAnalytics' => 1,
    'Mygento_Base' => 1,
    'Mygento_Configsync' => 1
  ),
);
CONFIG;
    }
}
