<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EmptyProject extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('project-template')
            ->setAliases(['empty-project'])
            ->setDescription('Create new project template')
            ->setDefinition([
                new InputArgument('name', InputArgument::OPTIONAL, 'Name of the entity'),
                new InputArgument('path', InputArgument::OPTIONAL, 'Path of the module', '.'),
                new InputArgument('repo', InputArgument::OPTIONAL, 'Project repository url'),
                new InputArgument('vendor', InputArgument::OPTIONAL, 'Vendor of the module', 'mygento'),
            ])
            ->setHelp(
                <<<EOT
<info>php jeeves.phar project-template</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getIO();
        $path = $input->getArgument('path') . '/';
        $vendor = strtolower($input->getArgument('vendor'));
        $name = strtolower($input->getArgument('name'));
        $name = $io->askAndValidate(
            'Project Name [<comment>' . $name . '</comment>]: ',
            function ($value) use ($name) {
                if (null === $value) {
                    return $name;
                }

                return $value;
            },
            null,
            $name
        );

        $repo = strtolower($input->getArgument('repo'));
        $repo = $io->askAndValidate(
            'Project repository [<comment>' . $repo . '</comment>]: ',
            function ($value) use ($repo) {
                if (null === $value) {
                    return $repo;
                }

                return $value;
            },
            null,
            $repo
        );

        $nodeGenerator = new \Mygento\Jeeves\Generators\Project\Node();
        $this->writeFile($path . 'package.json', $nodeGenerator->generatePackages($name));
        $this->writeFile($path . 'gulpfile.js', $nodeGenerator->generateGulp($name));
        $this->writeFile($path . '.eslintrc.json', $nodeGenerator->generateJsLint());

        $phpGenerator = new \Mygento\Jeeves\Generators\Project\Php();
        if (!is_dir($path . 'app')) {
            mkdir($path . 'app');
        }

        if (!is_dir($path . 'app/etc')) {
            mkdir($path . 'app/etc');
        }

        if (!is_dir($path . 'app/design')) {
            mkdir($path . 'app/design');
        }

        if (!is_dir($path . 'app/design/frontend')) {
            mkdir($path . 'app/design/frontend');
        }
        $v = ucfirst($vendor);

        if (!is_dir($path . 'app/design/frontend/' . $v)) {
            mkdir($path . 'app/design/frontend/' . $v);
        }

        if (!is_dir($path . 'app/design/frontend/' . $v . '/' . $name)) {
            mkdir($path . 'app/design/frontend/' . $v . '/' . $name);
        }

        if (!is_dir($path . 'app/design/frontend/' . $v . '/' . $name . '/web')) {
            mkdir($path . 'app/design/frontend/' . $v . '/' . $name . '/web');
        }

        if (!is_dir($path . 'app/design/frontend/' . $v . '/' . $name . '/web/scss')) {
            mkdir($path . 'app/design/frontend/' . $v . '/' . $name . '/web/scss');
        }

        if (!is_dir($path . 'app/design/frontend/' . $v . '/' . $name . '/web/css')) {
            mkdir($path . 'app/design/frontend/' . $v . '/' . $name . '/web/css');
        }

        $this->writeFile($path . 'app/etc/config.php', $phpGenerator->generateConfig());
        $this->writeFile($path . 'composer.json', $phpGenerator->generateComposer($vendor, $name));
        $this->writeFile($path . 'grumphp.yml', $phpGenerator->generateLint());
        $this->writeFile($path . '.php_cs', $phpGenerator->generateFixer());

        $deployGenerator = new \Mygento\Jeeves\Generators\Project\Deploy();
        if (!is_dir($path . 'config')) {
            mkdir($path . 'config');
        }
        $this->writeFile($path . 'config/deploy.rb', $deployGenerator->generateDeployer($name, $repo));
        $this->writeFile($path . 'Gemfile', $deployGenerator->generateGems());
        $this->writeFile($path . '.shippable.yml', $deployGenerator->generateCI());

        $editor = <<<EDITOR
root = true

[*]
indent_style = space
indent_size = 4
end_of_line = lf
charset = utf-8
trim_trailing_whitespace = true

[*.{json,yml,js,scss,css}]
indent_size = 2

[*.md]
trim_trailing_whitespace = false

EDITOR;

        $this->writeFile($path . '.editorconfig', $editor);

        $scss = <<<CONFIG
severity: error

linters:

  BangFormat:
    enabled: true
    space_before_bang: true
    space_after_bang: false

  BemDepth:
    enabled: true
    max_elements: 3

  BorderZero:
    enabled: true
    convention: zero

  ChainedClasses:
    enabled: false

  ColorKeyword:
    enabled: true

  ColorVariable:
    enabled: true

  Comment:
    enabled: false

  DebugStatement:
    enabled: true

  DeclarationOrder:
    enabled: true

  DisableLinterReason:
    enabled: true

  DuplicateProperty:
    enabled: true

  ElsePlacement:
    enabled: true
    style: same_line

  EmptyLineBetweenBlocks:
    enabled: true
    ignore_single_line_blocks: true

  EmptyRule:
    enabled: true

  ExtendDirective:
    enabled: false

  FinalNewline:
    enabled: true
    present: true

  HexLength:
    enabled: true
    style: short

  HexNotation:
    enabled: true
    style: lowercase

  HexValidation:
    enabled: true

  IdSelector:
    enabled: true
    severity: warning

  ImportantRule:
    enabled: true
    severity: warning

  ImportPath:
    enabled: true
    leading_underscore: false
    filename_extension: false

  Indentation:
    enabled: true
    allow_non_nested_indentation: true
    character: space
    width: 2

  LeadingZero:
      enabled: true
      style: include_zero

  LengthVariable:
    enabled: false
    severity: warning

  MergeableSelector:
    enabled: false
    force_nesting: false

  NameFormat:
    enabled: true
    convention: hyphenated_lowercase
    allow_leading_underscore: true

  NestingDepth:
    enabled: true
    max_depth: 3

  PlaceholderInExtend:
    enabled: true

  PrivateNamingConvention:
    enabled: true
    prefix: _

  PropertyCount:
    enabled: false

  PropertySortOrder:
    enabled: false

  PropertySpelling:
    enabled: true
    extra_properties: []

  PropertyUnits:
    enabled: true
    global: ['em', 'rem', '%', 'vh', 'vw']
    properties:
      line-height: [] # No units allowed
      font-size: [] # No units allowed
      border: []
      transition: ['s']

  PseudoElement:
    enabled: true

  QualifyingElement:
    enabled: true
    allow_element_with_attribute: false
    allow_element_with_class: false
    allow_element_with_id: false
    severity: warning

  SelectorDepth:
    enabled: true
    max_depth: 3

  SelectorFormat:
    enabled: false
    convention: hyphenated_lowercase

  Shorthand:
    enabled: true

  SingleLinePerProperty:
    enabled: true
    allow_single_line_rule_sets: false

  SingleLinePerSelector:
    enabled: true

  SpaceAfterComma:
    enabled: true

  SpaceAfterPropertyColon:
    enabled: true
    style: one_space

  SpaceAfterPropertyName:
    enabled: true

  SpaceAfterVariableColon:
    enabled: true
    style: at_least_one_space

  SpaceAfterVariableName:
    enabled: true

  SpaceAroundOperator:
    enabled: true
    style: one_space

  SpaceBeforeBrace:
    enabled: true
    style: space
    allow_single_line_padding: true

  SpaceBetweenParens:
    enabled: true
    spaces: 0

  StringQuotes:
    enabled: true
    style: single_quotes

  TrailingSemicolon:
    enabled: true

  TrailingZero:
    enabled: true

  TrailingWhitespace:
    enabled: true

  TransitionAll:
    enabled: false

  UnnecessaryMantissa:
    enabled: true

  UnnecessaryParentReference:
    enabled: true

  UrlFormat:
    enabled: false

  UrlQuotes:
    enabled: true

  VariableForProperty:
    enabled: true
    properties:
      - color
      - font-size
      - line-height

  VendorPrefixes:
    enabled: true
    identifier_list: base
    include: []
    exclude: []

  ZeroUnit:
    enabled: true

CONFIG;

        $this->writeFile($path . '.scss-lint.yml', $scss);

        return 0;
    }
}
