import eslint from '@eslint/js'
import tseslint from 'typescript-eslint'
import vue from 'eslint-plugin-vue'
import prettierConfig from 'eslint-config-prettier'
import htmlPlugin from 'eslint-plugin-html'
import phpPlugin from 'eslint-plugin-php'

export default tseslint.config(
    {
        ignores: ['dist/**', 'node_modules/**', 'vendor/**'],
    },
    {
        files: ['**/*.ts', '**/*.js', '**/*.vue'],
        extends: [
            eslint.configs.recommended,
            ...tseslint.configs.recommended,
            ...vue.configs['flat/recommended'],
            prettierConfig,
        ],
        languageOptions: {
            parserOptions: {
                ecmaVersion: 'latest',
                sourceType: 'module',
            },
        },
        rules: {
            'vue/multi-word-component-names': 'off',
            'vue/no-setup-props-destructure': 'off',
            '@typescript-eslint/no-unused-vars': [
                'error',
                {
                    argsIgnorePattern: '^_',
                    varsIgnorePattern: '^_',
                },
            ],
            'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'warn',
            'no-debugger': process.env.NODE_ENV === 'production' ? 'error' : 'warn',
        },
    },
    {
        files: ['**/*.html'],
        plugins: {
            html: htmlPlugin,
        },
        languageOptions: {
            parserOptions: {
                ecmaVersion: 'latest',
                sourceType: 'module',
            },
        },
        rules: {
            'html/report-bad-indent': 'error',
            'html/indent': ['error', 2],
        },
    },
    {
        files: ['**/*.php'],
        plugins: {
            php: phpPlugin,
        },
        languageOptions: {
            parserOptions: {
                parser: 'php-eslint-parser',
            },
        },
        rules: {
            'php/no-unused-vars': 'error',
            'php/semi': 'error',
            'php/brace-style': ['error', '1tbs'],
            'php/array-indent': ['error', 2],
        },
    }
)